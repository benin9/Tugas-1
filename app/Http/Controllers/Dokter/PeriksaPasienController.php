<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeriksaPasienController extends Controller
{
    public function index()
    {
        $dokterId = Auth::id();

        $daftarPasien = DaftarPoli::with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }

    public function create($id)
    {
        // Tampilkan semua obat beserta stok (termasuk yang habis, agar bisa ditandai disabled di view)
        $obats = Obat::all();
        return view('dokter.periksa-pasien.create', compact('obats', 'id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'obat_json'     => 'required|string',
            'catatan'       => 'nullable|string',
            'biaya_periksa' => 'required|integer',
        ]);

        $obatIds = json_decode($request->obat_json, true) ?? [];

        if (empty($obatIds)) {
            return redirect()->back()
                ->with('obat_error', 'Minimal satu obat harus dipilih.')
                ->with('type', 'error')
                ->withInput();
        }

        // Ambil semua obat yang dipilih sekaligus untuk validasi stok
        $obatDipilih = Obat::whereIn('id', $obatIds)->get()->keyBy('id');

        // Hitung berapa kali setiap obat muncul dalam resep
        $jumlahPerObat = array_count_values($obatIds);

        // Validasi stok sebelum menyimpan apapun
        foreach ($jumlahPerObat as $idObat => $jumlah) {
            $obat = $obatDipilih->get($idObat);
            if (!$obat || $obat->stok < $jumlah) {
                $stokTersisa = $obat ? $obat->stok : 0;
                return redirect()->back()
                    ->with('obat_error', "Stok obat '{$obat?->nama_obat}' tidak mencukupi. Tersisa: {$stokTersisa}, dibutuhkan: {$jumlah}.")
                    ->with('type', 'error')
                    ->withInput();
            }
        }

        // Simpan dalam satu transaksi database agar konsisten
        DB::transaction(function () use ($request, $obatIds, $obatDipilih, $jumlahPerObat) {
            $periksa = Periksa::create([
                'id_daftar_poli' => $request->id_daftar_poli,
                'tgl_periksa'    => now(),
                'catatan'        => $request->catatan,
                'biaya_periksa'  => $request->biaya_periksa + 150000,
            ]);

            foreach ($obatIds as $idObat) {
                DetailPeriksa::create([
                    'id_periksa' => $periksa->id,
                    'id_obat'    => $idObat,
                ]);
            }

            // Kurangi stok setiap obat yang diresepkan
            foreach ($jumlahPerObat as $idObat => $jumlah) {
                $obat = $obatDipilih->get($idObat);
                $obat->kurangiStok($jumlah);
            }
        });

        return redirect()->route('periksa-pasien.index')
            ->with('message', 'Data periksa berhasil disimpan. Stok obat telah diperbarui.')
            ->with('type', 'success');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index()
    {
        $obats = Obat::all();
        return view('admin.obat.index', compact('obats'));
    }

    public function create()
    {
        return view('admin.obat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_obat' => 'required|string',
            'kemasan'   => 'required|string',
            'harga'     => 'required|integer',
            'stok'      => 'required|integer|min:0',
        ]);

        Obat::create([
            'nama_obat' => $request->nama_obat,
            'kemasan'   => $request->kemasan,
            'harga'     => $request->harga,
            'stok'      => $request->stok,
        ]);

        return redirect()->route('obat.index')
            ->with('success', 'Data Obat berhasil ditambahkan.')
            ->with('type', 'success');
    }

    public function edit(string $id)
    {
        $obat = Obat::findOrFail($id);
        return view('admin.obat.edit')->with(['obat' => $obat]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_obat' => 'required|string',
            'kemasan'   => 'required|string',
            'harga'     => 'required|integer',
            'stok'      => 'required|integer|min:0',
        ]);

        $obat = Obat::findOrFail($id);
        $obat->update([
            'nama_obat' => $request->nama_obat,
            'kemasan'   => $request->kemasan,
            'harga'     => $request->harga,
            'stok'      => $request->stok,
        ]);

        return redirect()->route('obat.index')
            ->with('message', 'Data Obat berhasil diubah.')
            ->with('type', 'success');
    }

    public function destroy(string $id)
    {
        $obat = Obat::findOrFail($id);

        if ($obat->detailPeriksas()->exists()) {
            return redirect()->route('obat.index')
                ->with('message', 'Data Obat tidak dapat dihapus karena sudah memiliki riwayat resep pada pasien.')
                ->with('type', 'error');
        }

        $obat->delete();

        return redirect()->route('obat.index')
            ->with('message', 'Data Obat berhasil dihapus.')
            ->with('type', 'success');
    }

    /**
     * Tambah atau kurangi stok obat secara manual oleh admin.
     */
    public function updateStok(Request $request, string $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'tipe'   => 'required|in:tambah,kurangi',
        ]);

        $obat = Obat::findOrFail($id);

        try {
            if ($request->tipe === 'tambah') {
                $obat->tambahStok($request->jumlah);
                $pesan = "Stok berhasil ditambah {$request->jumlah} unit. Stok sekarang: {$obat->fresh()->stok}";
            } else {
                $obat->kurangiStok($request->jumlah);
                $pesan = "Stok berhasil dikurangi {$request->jumlah} unit. Stok sekarang: {$obat->fresh()->stok}";
            }

            return redirect()->route('obat.index')
                ->with('message', $pesan)
                ->with('type', 'success');

        } catch (\Exception $e) {
            return redirect()->route('obat.index')
                ->with('message', $e->getMessage())
                ->with('type', 'error');
        }
    }
}

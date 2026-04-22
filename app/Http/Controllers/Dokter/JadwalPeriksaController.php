<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\JadwalPeriksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalPeriksaController extends Controller
{
    public function index()
    {
        $dokter = Auth::User();
        $jadwalPeriksa = JadwalPeriksa::where('id_dokter', $dokter->id)->orderBy('hari')->get();
        return view('dokter.jadwal-periksa.index', compact('jadwalPeriksa'));
    }

    public function create()
    {
        return view('dokter.jadwal-periksa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        JadwalPeriksa::create([
            'id_dokter' =>  Auth::id(),
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return redirect()->route('dokter.jadwal-periksa.index')
        ->with('message', 'Jadwal periksa berhasil ditambahkan.')
        ->with ('type', 'success');
    }

    public function edit(string $id)
    {
        $jadwalPeriksa = JadwalPeriksa::findOrFail($id);
        return view('dokter.jadwal-periksa.edit', compact('jadwalPeriksa'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        $jadwalPeriksa = JadwalPeriksa::findOrFail($id);
        $jadwalPeriksa->update([
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return redirect()->route('dokter.jadwal-periksa.index')
        ->with('message', 'Jadwal periksa berhasil diperbarui.')
        ->with ('type', 'success');
    }

    public function destroy(string $id)
    {
        $jadwalPeriksa = JadwalPeriksa::findOrFail($id);
        $jadwalPeriksa->delete();

        return redirect()->route('dokter.jadwal-periksa.index')
        ->with('message', 'Jadwal periksa berhasil dihapus.')
        ->with ('type', 'success');
    }
}

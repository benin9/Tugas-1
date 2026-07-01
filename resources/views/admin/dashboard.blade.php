<x-layouts.app title="Admin Dashboard">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Selamat Datang, Admin!</h1>
        <p class="text-slate-500">Berikut adalah ringkasan sistem Anda hari ini.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Widget Obat Habis --}}
        <div class="card bg-base-100 shadow-sm rounded-2xl border border-red-200">
            <div class="card-body p-0">
                <div class="px-6 py-4 border-b border-red-100 bg-red-50 flex items-center justify-between rounded-t-2xl">
                    <h3 class="font-bold text-red-700 flex items-center gap-2">
                        <i class="fas fa-circle-xmark"></i>
                        Stok Obat Habis
                    </h3>
                    <span class="badge bg-red-600 text-white border-none font-bold">{{ $obatHabis->count() }}</span>
                </div>
                <div class="p-0">
                    @if($obatHabis->count() > 0)
                    <table class="table w-full">
                        <tbody>
                            @foreach($obatHabis as $obat)
                            <tr class="hover:bg-slate-50 transition border-b border-slate-100 last:border-0">
                                <td class="px-6 py-3 font-semibold text-slate-700">{{ $obat->nama_obat }}</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('obat.index') }}" class="btn btn-xs bg-red-100 text-red-700 hover:bg-red-200 border-none">Isi Stok</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="px-6 py-5 text-sm text-slate-500 text-center">
                        Semua obat tersedia.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Widget Stok Menipis --}}
        <div class="card bg-base-100 shadow-sm rounded-2xl border border-amber-200">
            <div class="card-body p-0">
                <div class="px-6 py-4 border-b border-amber-100 bg-amber-50 flex items-center justify-between rounded-t-2xl">
                    <h3 class="font-bold text-amber-700 flex items-center gap-2">
                        <i class="fas fa-triangle-exclamation"></i>
                        Stok Obat Menipis (< 10)
                    </h3>
                    <span class="badge bg-amber-500 text-white border-none font-bold">{{ $obatMenipis->count() }}</span>
                </div>
                <div class="p-0">
                    @if($obatMenipis->count() > 0)
                    <table class="table w-full">
                        <tbody>
                            @foreach($obatMenipis as $obat)
                            <tr class="hover:bg-slate-50 transition border-b border-slate-100 last:border-0">
                                <td class="px-6 py-3 font-semibold text-slate-700">{{ $obat->nama_obat }}</td>
                                <td class="px-6 py-3 text-right">
                                    <span class="badge bg-amber-100 text-amber-800 font-bold mr-2">{{ $obat->stok }} tersisa</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="px-6 py-5 text-sm text-slate-500 text-center">
                        Tidak ada stok obat yang menipis.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
<x-layouts.app title="Data Obat">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Data Obat
        </h2>

        <a href="{{ route('obat.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 
                  bg-primary hover:bg-primary/90 
                  text-white text-sm font-semibold 
                  rounded-xl transition">
            <i class="fas fa-plus text-xs"></i>
            Tambah Obat
        </a>
    </div>

    {{-- Flash Messages --}}
    <x-alert />

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2 border">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table w-full">

                    {{-- Table Head --}}
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Nama Obat</th>
                            <th class="px-6 py-4">Kemasan</th>
                            <th class="px-6 py-4">Harga</th>
                            <th class="px-6 py-4 text-center">Stok</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Table Body --}}
                    <tbody class="text-sm text-slate-700">
                        @forelse($obats as $obat)
                        <tr class="border-t border-slate-100 hover:bg-slate-50 transition">

                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $obat->nama_obat }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 text-xs font-semibold 
                                             rounded-full bg-green-100 text-green-600">
                                    {{ $obat->kemasan ?? '-' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 font-semibold text-slate-800">
                                Rp {{ number_format($obat->harga, 0, ',', '.') }}
                            </td>

                            {{-- Stok badge --}}
                            <td class="px-6 py-4 text-center">
                                @if($obat->stok == 0)
                                    <span class="inline-block px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-600 border border-red-200">
                                        <i class="fas fa-xmark mr-1"></i> Habis
                                    </span>
                                @elseif($obat->stok < 10)
                                    <span class="inline-block px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-600 border border-amber-200">
                                        <i class="fas fa-triangle-exclamation mr-1"></i> {{ $obat->stok }} unit
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-600 border border-emerald-200">
                                        <i class="fas fa-check mr-1"></i> {{ $obat->stok }} unit
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2 flex-wrap">

                                    {{-- Tambah Stok --}}
                                    <button 
                                        onclick="bukaModalStok({{ $obat->id }}, '{{ addslashes($obat->nama_obat) }}', {{ $obat->stok }}, 'tambah')"
                                        class="inline-flex items-center gap-1 px-3 py-2 
                                               bg-emerald-500 hover:bg-emerald-600 
                                               text-white text-xs font-semibold 
                                               rounded-lg transition">
                                        <i class="fas fa-plus text-xs"></i>
                                        +Stok
                                    </button>

                                    {{-- Kurangi Stok --}}
                                    <button 
                                        onclick="bukaModalStok({{ $obat->id }}, '{{ addslashes($obat->nama_obat) }}', {{ $obat->stok }}, 'kurangi')"
                                        class="inline-flex items-center gap-1 px-3 py-2 
                                               bg-orange-500 hover:bg-orange-600 
                                               text-white text-xs font-semibold 
                                               rounded-lg transition">
                                        <i class="fas fa-minus text-xs"></i>
                                        -Stok
                                    </button>

                                    {{-- Edit --}}
                                    <a href="{{ route('obat.edit', $obat->id) }}" class="inline-flex items-center gap-1 px-3 py-2 
                                              bg-amber-500 hover:bg-amber-600 
                                              text-white text-xs font-semibold 
                                              rounded-lg transition">
                                        <i class="fas fa-pen text-xs"></i>
                                        Edit
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('obat.destroy', $obat->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus obat ini?')" class="inline-flex items-center gap-1 px-3 py-2 
                                                   bg-red-500 hover:bg-red-600 
                                                   text-white text-xs font-semibold 
                                                   rounded-lg transition">
                                            <i class="fas fa-trash text-xs"></i>
                                            Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block"></i>
                                Belum ada data obat
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

    {{-- ===== MODAL UPDATE STOK ===== --}}
    <div id="modalStok" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">

            {{-- Modal Header --}}
            <div id="modalHeader" class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-white" id="modalTitle">Update Stok</h3>
                    <p class="text-sm text-white/80 mt-0.5" id="modalSubtitle"></p>
                </div>
                <button onclick="tutupModal()" class="text-white/70 hover:text-white transition">
                    <i class="fas fa-xmark text-xl"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <form id="formStok" method="POST" action="">
                @csrf
                <input type="hidden" name="tipe" id="inputTipe" value="">

                <div class="px-6 py-5">

                    {{-- Info Stok Sekarang --}}
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200 mb-5">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-slate-200">
                            <i class="fas fa-boxes-stacked text-slate-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Stok Saat Ini</p>
                            <p class="text-xl font-bold text-slate-800" id="infoStokSekarang">0</p>
                        </div>
                    </div>

                    {{-- Input Jumlah --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Jumlah <span id="labelTipe" class="text-emerald-600"></span>
                        </label>
                        <input 
                            type="number" 
                            name="jumlah" 
                            id="inputJumlah"
                            min="1" 
                            required
                            placeholder="Masukkan jumlah..."
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                        >
                    </div>

                    {{-- Preview hasil --}}
                    <div class="p-3 rounded-xl bg-blue-50 border border-blue-100 text-sm text-blue-700 flex items-center gap-2">
                        <i class="fas fa-circle-info"></i>
                        <span id="previewHasil">Masukkan jumlah untuk melihat hasil</span>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="px-6 pb-5 flex gap-3">
                    <button type="button" onclick="tutupModal()"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitModal"
                        class="flex-1 px-4 py-2.5 rounded-xl text-white text-sm font-semibold transition">
                        Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Script Modal --}}
    <script>
        let stokSekarang = 0;
        let tipeAksi = 'tambah';

        function bukaModalStok(id, nama, stok, tipe) {
            stokSekarang = stok;
            tipeAksi = tipe;

            const modal = document.getElementById('modalStok');
            const header = document.getElementById('modalHeader');
            const title = document.getElementById('modalTitle');
            const subtitle = document.getElementById('modalSubtitle');
            const labelTipe = document.getElementById('labelTipe');
            const btnSubmit = document.getElementById('btnSubmitModal');
            const inputTipe = document.getElementById('inputTipe');
            const infoStok = document.getElementById('infoStokSekarang');
            const form = document.getElementById('formStok');
            const inputJumlah = document.getElementById('inputJumlah');

            // Set form action
            form.action = `/admin/obat/${id}/stok`;

            // Set stok info
            infoStok.textContent = stok + ' unit';

            // Set tipe
            inputTipe.value = tipe;
            inputJumlah.value = '';
            document.getElementById('previewHasil').textContent = 'Masukkan jumlah untuk melihat hasil';

            if (tipe === 'tambah') {
                header.className = 'px-6 py-4 flex items-center justify-between bg-emerald-600';
                title.textContent = 'Tambah Stok Obat';
                subtitle.textContent = nama;
                labelTipe.textContent = 'ditambahkan';
                labelTipe.className = 'text-emerald-600';
                btnSubmit.className = 'flex-1 px-4 py-2.5 rounded-xl text-white text-sm font-semibold transition bg-emerald-600 hover:bg-emerald-700';
            } else {
                header.className = 'px-6 py-4 flex items-center justify-between bg-orange-500';
                title.textContent = 'Kurangi Stok Obat';
                subtitle.textContent = nama;
                labelTipe.textContent = 'dikurangi';
                labelTipe.className = 'text-orange-600';
                btnSubmit.className = 'flex-1 px-4 py-2.5 rounded-xl text-white text-sm font-semibold transition bg-orange-500 hover:bg-orange-600';
            }

            modal.classList.remove('hidden');
            setTimeout(() => inputJumlah.focus(), 100);
        }

        function tutupModal() {
            document.getElementById('modalStok').classList.add('hidden');
        }

        // Preview hasil secara real-time
        document.getElementById('inputJumlah').addEventListener('input', function () {
            const jumlah = parseInt(this.value) || 0;
            const preview = document.getElementById('previewHasil');

            if (jumlah <= 0) {
                preview.textContent = 'Masukkan jumlah untuk melihat hasil';
                return;
            }

            if (tipeAksi === 'tambah') {
                preview.textContent = `Stok setelah ditambah: ${stokSekarang} + ${jumlah} = ${stokSekarang + jumlah} unit`;
            } else {
                const hasil = stokSekarang - jumlah;
                if (hasil < 0) {
                    preview.textContent = `⚠ Stok tidak mencukupi! Maksimal dapat dikurangi: ${stokSekarang} unit`;
                    preview.className = 'p-3 rounded-xl bg-red-50 border border-red-100 text-sm text-red-700 flex items-center gap-2';
                } else {
                    preview.textContent = `Stok setelah dikurangi: ${stokSekarang} - ${jumlah} = ${hasil} unit`;
                    preview.className = 'p-3 rounded-xl bg-blue-50 border border-blue-100 text-sm text-blue-700 flex items-center gap-2';
                }
            }
        });

        // Tutup modal jika klik di luar
        document.getElementById('modalStok').addEventListener('click', function (e) {
            if (e.target === this) tutupModal();
        });
    </script>

</x-layouts.app>
{{--
    Dibuat: 2026-10-27
    Tujuan: Menampilkan halaman Daftar Staf (List Pegawai) dari tabel staffs.
--}}
@extends('layouts.app')

@section('title', 'Daftar Staf')

@section('content')
<div class="space-y-6">
    {{-- A. Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="font-poppins font-bold text-xl text-slate-800">Daftar Staf</h2>
            <p class="font-inter text-sm text-slate-500 mt-1">Manajemen data pegawai dan penanggung jawab PKS</p>
        </div>
        <div>
            <button type="button" id="btn_tambah_staf" onclick="bukaModalTambah()" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-biru text-white text-sm font-medium font-inter rounded-lg hover:opacity-90 shadow-sm transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5v14m-7-7h14"/>
                </svg>
                <span>+ Tambah Staf</span>
            </button>
        </div>
    </div>

    {{-- B. Bar Toolbar & Card Tabel --}}
    <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 p-4 sm:p-5">
        {{-- Toolbar: Search & Filter Status --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-5">
            {{-- Input Search dengan Ikon Kaca Pembesar --}}
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-3.5-3.5"/>
                    </svg>
                </div>
                <input type="text" id="input_search" oninput="filterTabel()" placeholder="Cari berdasarkan nama atau NPP..." class="w-full pl-9 pr-4 py-2 text-sm font-inter text-slate-800 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-biru/20 focus:border-primary-biru transition-colors shadow-xs">
            </div>

            {{-- Dropdown Filter Status --}}
            <div class="flex items-center gap-2">
                <select id="filter_status" onchange="filterTabel()" class="w-full sm:w-auto px-3 py-2 text-sm font-inter text-slate-700 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-biru/20 focus:border-primary-biru transition-colors shadow-xs cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif / Mutasi</option>
                    <option value="keluar">Keluar</option>
                </select>
            </div>
        </div>

        {{-- C. Tabel Data Staff --}}
        <div class="overflow-x-auto rounded-lg border border-slate-200/80">
            <table class="w-full text-left border-collapse font-inter text-sm" id="table_staff">
                <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th scope="col" class="px-4 py-3 sm:px-6">NPP</th>
                        <th scope="col" class="px-4 py-3 sm:px-6">Nama Staf</th>
                        <th scope="col" class="px-4 py-3 sm:px-6">No. WhatsApp</th>
                        <th scope="col" class="px-4 py-3 sm:px-6">Email</th>
                        <th scope="col" class="px-4 py-3 sm:px-6">Status</th>
                        <th scope="col" class="px-4 py-3 sm:px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="tbody_staff">
                    @forelse ($staffs as $staff)
                        <tr class="hover:bg-slate-50/80 transition-colors" data-nama="{{ strtolower($staff->nama) }}" data-npp="{{ $staff->npp }}" data-status="{{ strtolower($staff->status) }}">
                            <td class="px-4 py-3.5 sm:px-6 font-medium text-slate-700">{{ $staff->npp }}</td>
                            <td class="px-4 py-3.5 sm:px-6 text-slate-900 font-semibold">{{ $staff->nama }}</td>
                            <td class="px-4 py-3.5 sm:px-6 text-slate-600">{{ $staff->nomor_hp }}</td>
                            <td class="px-4 py-3.5 sm:px-6 text-slate-600">{{ $staff->email }}</td>
                            <td class="px-4 py-3.5 sm:px-6">
                                @if ($staff->status === 'aktif')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-hijau/15 text-primary-hijau">
                                        Aktif
                                    </span>
                                @elseif ($staff->status === 'nonaktif')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-200 text-slate-500">
                                        Nonaktif/Mutasi
                                    </span>
                                @elseif ($staff->status === 'keluar')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-500">
                                        Keluar
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 capitalize">
                                        {{ $staff->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 sm:px-6 text-center">
                                <div class="inline-flex items-center justify-center gap-1">
                                    {{-- Tombol Edit --}}
                                    <button type="button" aria-label="Edit Staf" onclick="bukaModalEdit('{{ $staff->npp }}', '{{ addslashes($staff->nama) }}', '{{ $staff->nomor_hp }}', '{{ $staff->email }}', '{{ $staff->status }}')" class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md transition-colors cursor-pointer" title="Edit Staf">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4 20h4L18.5 9.5a2.121 2.121 0 0 0-3-3L5 17l-1 3Z"/>
                                            <path d="m13.5 6.5 3 3"/>
                                        </svg>
                                    </button>

                                    {{-- Tombol Detail --}}
                                    <a href="#" aria-label="Detail Staf" class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-md transition-colors inline-block" title="Detail Staf">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 font-inter text-sm">
                                Belum ada data pegawai.
                            </td>
                        </tr>
                    @endforelse

                    {{-- Baris Pesan Kosong Saat Filter Tidak Menemukan Hasil --}}
                    <tr id="empty_search_row" class="hidden">
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400 font-inter text-sm">
                            Tidak ada data pegawai yang sesuai dengan pencarian / filter.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- D. Modal Tambah Staf (Pop Up) --}}
<div id="modal_tambah_staf" class="fixed inset-0 z-50 bg-black/50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <h3 class="font-poppins font-semibold text-lg text-slate-800">Tambah Staf Baru</h3>
            <button type="button" onclick="tutupModalTambah()" aria-label="Tutup Modal" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="#" method="POST" onsubmit="event.preventDefault(); tutupModalTambah();" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="tambah_npp" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">NPP</label>
                <input type="number" id="tambah_npp" name="npp" placeholder="Contoh: 100011" required class="w-full px-3 py-2 text-sm font-inter text-slate-800 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-biru/20 focus:border-primary-biru transition-colors shadow-xs">
            </div>

            <div>
                <label for="tambah_nama" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Nama Lengkap</label>
                <input type="text" id="tambah_nama" name="nama" placeholder="Nama Lengkap Staf" required class="w-full px-3 py-2 text-sm font-inter text-slate-800 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-biru/20 focus:border-primary-biru transition-colors shadow-xs">
            </div>

            <div>
                <label for="tambah_email" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Email</label>
                <input type="email" id="tambah_email" name="email" placeholder="nama@kepesertaan.id" required class="w-full px-3 py-2 text-sm font-inter text-slate-800 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-biru/20 focus:border-primary-biru transition-colors shadow-xs">
            </div>

            <div>
                <label for="tambah_nomor_hp" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">No. WhatsApp</label>
                <input type="text" id="tambah_nomor_hp" name="nomor_hp" placeholder="08xxxxxxxxxx" required class="w-full px-3 py-2 text-sm font-inter text-slate-800 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-biru/20 focus:border-primary-biru transition-colors shadow-xs">
            </div>

            <div>
                <label for="tambah_status" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Status</label>
                <select id="tambah_status" name="status" required class="w-full px-3 py-2 text-sm font-inter text-slate-700 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-biru/20 focus:border-primary-biru transition-colors shadow-xs cursor-pointer">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif / Mutasi</option>
                    <option value="keluar">Keluar</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="tutupModalTambah()" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-100 text-sm font-medium font-inter transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-primary-biru text-white rounded-lg text-sm font-medium font-inter hover:opacity-90 transition-colors shadow-xs cursor-pointer">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- E. Modal Edit Staf (Pop Up) --}}
<div id="modal_edit_staf" class="fixed inset-0 z-50 bg-black/50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <h3 class="font-poppins font-semibold text-lg text-slate-800">Edit Data Staf</h3>
            <button type="button" onclick="tutupModalEdit()" aria-label="Tutup Modal" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="#" method="POST" onsubmit="event.preventDefault(); tutupModalEdit();" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="edit_npp" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">NPP (Readonly)</label>
                <input type="number" id="edit_npp" name="npp" readonly class="w-full px-3 py-2 text-sm font-inter text-slate-500 bg-slate-100 border border-slate-200 rounded-lg focus:outline-none cursor-not-allowed shadow-xs">
            </div>

            <div>
                <label for="edit_nama" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Nama Lengkap</label>
                <input type="text" id="edit_nama" name="nama" required class="w-full px-3 py-2 text-sm font-inter text-slate-800 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-biru/20 focus:border-primary-biru transition-colors shadow-xs">
            </div>

            <div>
                <label for="edit_email" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Email</label>
                <input type="email" id="edit_email" name="email" required class="w-full px-3 py-2 text-sm font-inter text-slate-800 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-biru/20 focus:border-primary-biru transition-colors shadow-xs">
            </div>

            <div>
                <label for="edit_nomor_hp" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">No. WhatsApp</label>
                <input type="text" id="edit_nomor_hp" name="nomor_hp" required class="w-full px-3 py-2 text-sm font-inter text-slate-800 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-biru/20 focus:border-primary-biru transition-colors shadow-xs">
            </div>

            <div>
                <label for="edit_status" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Status</label>
                <select id="edit_status" name="status" required class="w-full px-3 py-2 text-sm font-inter text-slate-700 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-biru/20 focus:border-primary-biru transition-colors shadow-xs cursor-pointer">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif / Mutasi</option>
                    <option value="keluar">Keluar</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="tutupModalEdit()" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-100 text-sm font-medium font-inter transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-primary-biru text-white rounded-lg text-sm font-medium font-inter hover:opacity-90 transition-colors shadow-xs cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    /**
     * Filter tabel baris demi baris berdasarkan kata kunci pencarian (Nama atau NPP)
     * dan filter dropdown status secara instan tanpa reload halaman.
     */
    function filterTabel() {
        const keyword = (document.getElementById('input_search')?.value || '').toLowerCase().trim();
        const status = (document.getElementById('filter_status')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('#tbody_staff tr[data-nama]');
        let visibleCount = 0;

        rows.forEach(function (row) {
            const nama = row.getAttribute('data-nama') || '';
            const npp = row.getAttribute('data-npp') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchKeyword = !keyword || nama.includes(keyword) || npp.includes(keyword);
            const matchStatus = !status || rowStatus === status;

            if (matchKeyword && matchStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const emptyRow = document.getElementById('empty_search_row');
        if (emptyRow) {
            if (visibleCount === 0 && rows.length > 0) {
                emptyRow.classList.remove('hidden');
            } else {
                emptyRow.classList.add('hidden');
            }
        }
    }

    /**
     * Membuka modal Tambah Staf.
     */
    function bukaModalTambah() {
        const modal = document.getElementById('modal_tambah_staf');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    /**
     * Menutup modal Tambah Staf dan mereset form isian.
     */
    function tutupModalTambah() {
        const modal = document.getElementById('modal_tambah_staf');
        if (modal) {
            modal.classList.add('hidden');
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
            }
        }
    }

    /**
     * Membuka modal Edit Staf dan mengisi field dengan data baris yang diklik.
     */
    function bukaModalEdit(npp, nama, nomorHp, email, status) {
        const editNpp = document.getElementById('edit_npp');
        const editNama = document.getElementById('edit_nama');
        const editNomorHp = document.getElementById('edit_nomor_hp');
        const editEmail = document.getElementById('edit_email');
        const editStatus = document.getElementById('edit_status');

        if (editNpp) editNpp.value = npp || '';
        if (editNama) editNama.value = nama || '';
        if (editNomorHp) editNomorHp.value = nomorHp || '';
        if (editEmail) editEmail.value = email || '';
        if (editStatus) editStatus.value = status || 'aktif';

        const modal = document.getElementById('modal_edit_staf');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    /**
     * Menutup modal Edit Staf.
     */
    function tutupModalEdit() {
        const modal = document.getElementById('modal_edit_staf');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    /**
     * Event listener: menutup modal saat pengguna mengklik area luar modal (backdrop).
     */
    window.addEventListener('click', function (e) {
        const modalTambah = document.getElementById('modal_tambah_staf');
        const modalEdit = document.getElementById('modal_edit_staf');

        if (e.target === modalTambah) {
            tutupModalTambah();
        }
        if (e.target === modalEdit) {
            tutupModalEdit();
        }
    });

    /**
     * Menutup modal dengan tombol ESC keyboard.
     */
    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            tutupModalTambah();
            tutupModalEdit();
        }
    });
</script>
@endpush

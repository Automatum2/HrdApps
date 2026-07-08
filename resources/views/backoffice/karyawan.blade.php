@extends('layouts.admin')

@section('title', 'Manajemen Karyawan - HRDApps')
@section('page_title', 'Manajemen Karyawan')

@section('content')
<!-- Header & Breadcrumbs -->
<div class="flex flex-col md:flex-row md:items-end justify-between mb-6">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-1">
            <a class="hover:text-primary transition-colors" href="{{ route('backoffice.dashboard') }}">Beranda</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary font-semibold">Manajemen Karyawan</span>
        </nav>
        <p class="text-body-sm text-on-surface-variant">Kelola daftar penempatan, tugas, dan detail departemen karyawan Anda.</p>
    </div>
    <div class="flex items-center gap-3 mt-4 md:mt-0">
        <button class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-lg font-semibold text-sm shadow hover:brightness-110 active:scale-95 transition-all cursor-pointer" id="btn-open-modal">
            <span class="material-symbols-outlined text-lg">add</span>
            <span>Tambah Karyawan</span>
        </button>
        <button class="flex items-center gap-2 px-4 py-2.5 bg-surface-container-lowest border border-outline-variant text-secondary rounded-lg font-semibold text-sm hover:bg-surface-container-low active:scale-95 transition-all cursor-pointer" onclick="alert('Mengekspor data ke Excel...')">
            <span class="material-symbols-outlined text-lg">download</span>
            <span>Export Excel</span>
        </button>
    </div>
</div>

<!-- Bento Filter & List Container -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col">
    <!-- Filter Section -->
    <div class="p-6 border-b border-outline-variant bg-surface-bright flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="flex flex-col sm:flex-row gap-4 items-center w-full md:w-auto">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <label class="text-xs uppercase font-bold text-on-surface-variant whitespace-nowrap">Filter Jabatan:</label>
                <select class="form-select bg-white border border-outline-variant rounded-lg text-sm py-2 pl-4 pr-10 focus:ring-primary focus:border-primary w-full sm:w-48 transition-all text-on-surface" id="filter-jabatan">
                    <option value="">Semua Jabatan</option>
                    <option value="Senior Developer">Developer</option>
                    <option value="Designer">Designer</option>
                    <option value="Manager">Manager</option>
                    <option value="Analyst">Analyst</option>
                </select>
            </div>
        </div>
        <div class="relative w-full md:w-96 group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors text-lg">search</span>
            <input class="pl-12 pr-4 py-2 bg-white border border-outline-variant rounded-lg text-sm focus:ring-2 focus:ring-primary/20 w-full transition-all outline-none text-on-surface" placeholder="Cari Karyawan (Nama atau NIK)..." type="text" id="search-karyawan">
        </div>
    </div>
    
    <div class="p-4 bg-surface-container-low border-b border-outline-variant flex justify-between items-center">
        <p class="text-xs text-on-surface-variant font-medium">Menampilkan <span class="font-bold text-on-surface" id="showing-range">1-5</span> dari <span class="font-bold text-on-surface" id="total-entries-top">5</span> karyawan</p>
        <div class="flex gap-2">
            <button class="p-1 hover:bg-surface-container-high rounded transition-colors text-outline cursor-pointer" title="Filter List">
                <span class="material-symbols-outlined text-[20px]">filter_list</span>
            </button>
            <button class="p-1 hover:bg-surface-container-high rounded transition-colors text-outline cursor-pointer" title="Grid View">
                <span class="material-symbols-outlined text-[20px]">grid_view</span>
            </button>
        </div>
    </div>
    
    <!-- Table Content -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-surface-container text-on-surface uppercase tracking-wider font-semibold text-xs border-b border-outline-variant">
                    <th class="px-6 py-4 w-12 text-center">No</th>
                    <th class="px-6 py-4">Foto</th>
                    <th class="px-6 py-4">Nama Lengkap</th>
                    <th class="px-6 py-4">NIK</th>
                    <th class="px-6 py-4">Jabatan</th>
                    <th class="px-6 py-4">Departemen</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10 font-body-sm text-body-sm" id="table-karyawan-body">
                <!-- Employee 1 -->
                <tr class="hover:bg-primary/5 transition-colors group" data-nik="1990010101" data-jabatan="Senior Developer" data-dept="IT" data-status="Tetap">
                    <td class="px-6 py-4 text-center text-on-surface font-semibold font-mono">1</td>
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-full border-2 border-surface-container shadow-sm overflow-hidden bg-primary/5 flex items-center justify-center font-bold text-primary">AF</div>
                    </td>
                    <td class="px-6 py-4 font-bold text-on-surface">Ahmad Fauzi</td>
                    <td class="px-6 py-4 font-mono text-on-surface-variant text-sm">1990010101</td>
                    <td class="px-6 py-4 text-on-surface-variant">Senior Developer</td>
                    <td class="px-6 py-4"><span class="px-2.5 py-1 bg-secondary-container/30 text-secondary rounded-full font-semibold text-xs uppercase">IT</span></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary-container/10 text-primary">
                            Aktif
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <button class="w-8 h-8 flex items-center justify-center rounded bg-primary-container text-on-primary-container hover:brightness-110 active:scale-90 transition-all shadow-sm cursor-pointer" title="Detail / Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button class="btn-lepas w-8 h-8 flex items-center justify-center rounded bg-error text-white hover:brightness-110 active:scale-90 transition-all shadow-sm cursor-pointer" title="Lepas dari Departemen" data-nama="Ahmad Fauzi" data-nik="1990010101">
                                <span class="material-symbols-outlined text-sm">person_remove</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Employee 2 -->
                <tr class="hover:bg-primary/5 transition-colors group" data-nik="1991051502" data-jabatan="HR Officer" data-dept="HRD" data-status="Kontrak">
                    <td class="px-6 py-4 text-center text-on-surface font-semibold font-mono">2</td>
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-full border-2 border-surface-container shadow-sm overflow-hidden bg-primary/5 flex items-center justify-center font-bold text-primary">SN</div>
                    </td>
                    <td class="px-6 py-4 font-bold text-on-surface">Siti Nurhaliza</td>
                    <td class="px-6 py-4 font-mono text-on-surface-variant text-sm">1991051502</td>
                    <td class="px-6 py-4 text-on-surface-variant">HR Officer</td>
                    <td class="px-6 py-4"><span class="px-2.5 py-1 bg-secondary-container/30 text-secondary rounded-full font-semibold text-xs uppercase">HRD</span></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-surface-container-highest text-secondary">
                            Aktif
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <button class="w-8 h-8 flex items-center justify-center rounded bg-primary-container text-on-primary-container hover:brightness-110 active:scale-90 transition-all shadow-sm cursor-pointer" title="Detail / Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button class="btn-lepas w-8 h-8 flex items-center justify-center rounded bg-error text-white hover:brightness-110 active:scale-90 transition-all shadow-sm cursor-pointer" title="Lepas dari Departemen" data-nama="Siti Nurhaliza" data-nik="1991051502">
                                <span class="material-symbols-outlined text-sm">person_remove</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Employee 3 -->
                <tr class="hover:bg-primary/5 transition-colors group" data-nik="1988112003" data-jabatan="Sales Manager" data-dept="Sales" data-status="Tetap">
                    <td class="px-6 py-4 text-center text-on-surface font-semibold font-mono">3</td>
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-full border-2 border-surface-container shadow-sm overflow-hidden bg-primary/5 flex items-center justify-center font-bold text-primary">JD</div>
                    </td>
                    <td class="px-6 py-4 font-bold text-on-surface">Jane Doe</td>
                    <td class="px-6 py-4 font-mono text-on-surface-variant text-sm">1988112003</td>
                    <td class="px-6 py-4 text-on-surface-variant">Sales Manager</td>
                    <td class="px-6 py-4"><span class="px-2.5 py-1 bg-secondary-container/30 text-secondary rounded-full font-semibold text-xs uppercase">Sales</span></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary-container/10 text-primary">
                            Aktif
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <button class="w-8 h-8 flex items-center justify-center rounded bg-primary-container text-on-primary-container hover:brightness-110 active:scale-90 transition-all shadow-sm cursor-pointer" title="Detail / Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button class="btn-lepas w-8 h-8 flex items-center justify-center rounded bg-error text-white hover:brightness-110 active:scale-90 transition-all shadow-sm cursor-pointer" title="Lepas dari Departemen" data-nama="Jane Doe" data-nik="1988112003">
                                <span class="material-symbols-outlined text-sm">person_remove</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Employee 4 -->
                <tr class="hover:bg-primary/5 transition-colors group" data-nik="1993031004" data-jabatan="UX Designer" data-dept="IT" data-status="Kontrak">
                    <td class="px-6 py-4 text-center text-on-surface font-semibold font-mono">4</td>
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-full border-2 border-surface-container shadow-sm overflow-hidden bg-primary/5 flex items-center justify-center font-bold text-primary">DL</div>
                    </td>
                    <td class="px-6 py-4 font-bold text-on-surface">Diana Lestari</td>
                    <td class="px-6 py-4 font-mono text-on-surface-variant text-sm">1993031004</td>
                    <td class="px-6 py-4 text-on-surface-variant">UX Designer</td>
                    <td class="px-6 py-4"><span class="px-2.5 py-1 bg-secondary-container/30 text-secondary rounded-full font-semibold text-xs uppercase">IT</span></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-surface-container-highest text-secondary">
                            Aktif
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <button class="w-8 h-8 flex items-center justify-center rounded bg-primary-container text-on-primary-container hover:brightness-110 active:scale-90 transition-all shadow-sm cursor-pointer" title="Detail / Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button class="btn-lepas w-8 h-8 flex items-center justify-center rounded bg-error text-white hover:brightness-110 active:scale-90 transition-all shadow-sm cursor-pointer" title="Lepas dari Departemen" data-nama="Diana Lestari" data-nik="1993031004">
                                <span class="material-symbols-outlined text-sm">person_remove</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Employee 5 -->
                <tr class="hover:bg-primary/5 transition-colors group" data-nik="1985082505" data-jabatan="Finance Lead" data-dept="Finance" data-status="Magang">
                    <td class="px-6 py-4 text-center text-on-surface font-semibold font-mono">5</td>
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-full border-2 border-surface-container shadow-sm overflow-hidden bg-primary/5 flex items-center justify-center font-bold text-primary">EP</div>
                    </td>
                    <td class="px-6 py-4 font-bold text-on-surface">Eko Prasetyo</td>
                    <td class="px-6 py-4 font-mono text-on-surface-variant text-sm">1985082505</td>
                    <td class="px-6 py-4 text-on-surface-variant">Finance Lead</td>
                    <td class="px-6 py-4"><span class="px-2.5 py-1 bg-secondary-container/30 text-secondary rounded-full font-semibold text-xs uppercase">Finance</span></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-tertiary-container/10 text-tertiary">
                            Aktif
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <button class="w-8 h-8 flex items-center justify-center rounded bg-primary-container text-on-primary-container hover:brightness-110 active:scale-90 transition-all shadow-sm cursor-pointer" title="Detail / Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button class="btn-lepas w-8 h-8 flex items-center justify-center rounded bg-error text-white hover:brightness-110 active:scale-90 transition-all shadow-sm cursor-pointer" title="Lepas dari Departemen" data-nama="Eko Prasetyo" data-nik="1985082505">
                                <span class="material-symbols-outlined text-sm">person_remove</span>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination Footer -->
    <div class="p-6 border-t border-outline-variant bg-surface-bright flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-xs text-on-surface-variant">Menampilkan <span class="font-bold text-on-surface" id="showing-count-footer">5</span> karyawan dari total <span class="font-bold text-on-surface" id="total-count-footer">5</span></p>
        <div class="flex items-center gap-1">
            <button class="px-4 py-2 border border-outline-variant rounded-lg text-xs font-semibold hover:bg-surface-container transition-colors disabled:opacity-50 cursor-pointer" disabled>Sebelumnya</button>
            <button class="w-8 h-8 flex items-center justify-center bg-primary text-white rounded-lg text-xs font-bold shadow-sm">1</button>
            <button class="w-8 h-8 flex items-center justify-center border border-outline-variant rounded-lg text-xs hover:bg-surface-container transition-colors cursor-pointer">2</button>
            <button class="w-8 h-8 flex items-center justify-center border border-outline-variant rounded-lg text-xs hover:bg-surface-container transition-colors cursor-pointer">3</button>
            <span class="px-2 text-outline">...</span>
            <button class="px-4 py-2 border border-outline-variant rounded-lg text-xs font-semibold hover:bg-surface-container transition-colors cursor-pointer">Lanjut</button>
        </div>
    </div>
</div>

<!-- Stats Overview (Bento Rekap Cards) -->
<div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Card 1: Total Staff -->
    <div class="p-6 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined">group</span>
        </div>
        <div>
            <p class="text-xs uppercase font-bold text-on-surface-variant tracking-wider">Total Staff</p>
            <p class="text-headline-md font-bold text-on-surface" id="widget-total-staff">5</p>
        </div>
    </div>
    <!-- Card 2: Aktif -->
    <div class="p-6 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-tertiary/10 flex items-center justify-center text-tertiary">
            <span class="material-symbols-outlined">how_to_reg</span>
        </div>
        <div>
            <p class="text-xs uppercase font-bold text-on-surface-variant tracking-wider">Aktif</p>
            <p class="text-headline-md font-bold text-on-surface" id="widget-aktif">5</p>
        </div>
    </div>
    <!-- Card 3: Cuti/Off -->
    <div class="p-6 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-error/10 flex items-center justify-center text-error">
            <span class="material-symbols-outlined">person_off</span>
        </div>
        <div>
            <p class="text-xs uppercase font-bold text-on-surface-variant tracking-wider">Cuti/Off</p>
            <p class="text-headline-md font-bold text-on-surface" id="widget-cuti">0</p>
        </div>
    </div>
    <!-- Card 4: Baru Bulan Ini -->
    <div class="p-6 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-secondary-container/30 flex items-center justify-center text-secondary">
            <span class="material-symbols-outlined">new_releases</span>
        </div>
        <div>
            <p class="text-xs uppercase font-bold text-on-surface-variant tracking-wider">Baru (Bulan Ini)</p>
            <p class="text-headline-md font-bold text-on-surface" id="widget-baru">2</p>
        </div>
    </div>
</div>

<!-- MODAL: Tambah Karyawan Baru (Assign Departemen) -->
<div class="fixed inset-0 bg-[#0b1c30]/60 backdrop-blur-sm z-50 items-center justify-center p-4 hidden" id="modal-tambah">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full border border-outline-variant flex flex-col max-h-[85vh] overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <!-- Modal Header -->
        <div class="px-xl py-lg border-b border-outline-variant flex justify-between items-center bg-surface">
            <div>
                <h3 class="font-title-sm text-title-sm text-on-surface font-bold">Pilih Anggota Baru</h3>
                <p class="text-body-sm text-on-surface-variant">Daftar karyawan aktif yang belum ditempatkan ke departemen mana pun.</p>
            </div>
            <button class="p-1 hover:bg-surface-container rounded-full text-on-surface-variant cursor-pointer" id="btn-close-modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <!-- Modal Search Bar -->
        <div class="p-lg border-b border-outline-variant bg-surface-bright flex gap-3">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                <input class="w-full bg-white border border-outline-variant rounded-lg pl-10 pr-4 py-2 text-body-md outline-none focus:ring-2 focus:ring-primary/20 transition-all text-on-surface" placeholder="Cari nama atau NIK karyawan..." type="text" id="search-modal-karyawan">
            </div>
        </div>
        
        <!-- Modal Table Content -->
        <div class="overflow-y-auto flex-1">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-surface-container-low text-on-surface sticky top-0">
                    <tr>
                        <th class="font-table-header text-table-header px-lg py-3 uppercase">Nama Karyawan</th>
                        <th class="font-table-header text-table-header px-lg py-3 uppercase">NIK</th>
                        <th class="font-table-header text-table-header px-lg py-3 uppercase text-right">Pilih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10 font-body-sm text-body-sm" id="modal-table-body">
                    <!-- Baris Dummy 1 -->
                    <tr class="hover:bg-primary/5 transition-colors group" data-nik="EMP-010" data-nama="Doni Darmawan" data-jabatan="Recruitment Staff" data-dept="HRD" data-status="Tetap">
                        <td class="px-lg py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/5 flex items-center justify-center text-xs font-bold text-primary/70">DD</div>
                                <span class="font-bold text-on-surface">Doni Darmawan</span>
                            </div>
                        </td>
                        <td class="px-lg py-3 font-mono text-on-surface-variant">EMP-010</td>
                        <td class="px-lg py-3 text-right">
                            <button class="btn-assign bg-primary hover:bg-primary-container text-white px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer active:scale-95 transition-all">
                                + Pilih
                            </button>
                        </td>
                    </tr>
                    <!-- Baris Dummy 2 -->
                    <tr class="hover:bg-primary/5 transition-colors group" data-nik="EMP-011" data-nama="Evi Lestari" data-jabatan="Benefits Analyst" data-dept="HRD" data-status="Kontrak">
                        <td class="px-lg py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/5 flex items-center justify-center text-xs font-bold text-primary/70">EL</div>
                                <span class="font-bold text-on-surface">Evi Lestari</span>
                            </div>
                        </td>
                        <td class="px-lg py-3 font-mono text-on-surface-variant">EMP-011</td>
                        <td class="px-lg py-3 text-right">
                            <button class="btn-assign bg-primary hover:bg-primary-container text-white px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer active:scale-95 transition-all">
                                + Pilih
                            </button>
                        </td>
                    </tr>
                    <!-- Baris Dummy 3 -->
                    <tr class="hover:bg-primary/5 transition-colors group" data-nik="EMP-012" data-nama="Gilang Ramadhan" data-jabatan="HR Generalist" data-dept="HRD" data-status="Magang">
                        <td class="px-lg py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/5 flex items-center justify-center text-xs font-bold text-primary/70">GR</div>
                                <span class="font-bold text-on-surface">Gilang Ramadhan</span>
                            </div>
                        </td>
                        <td class="px-lg py-3 font-mono text-on-surface-variant">EMP-012</td>
                        <td class="px-lg py-3 text-right">
                            <button class="btn-assign bg-primary hover:bg-primary-container text-white px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer active:scale-95 transition-all">
                                + Pilih
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- State Kosong jika hasil pencarian nihil -->
            <div class="p-8 text-center hidden text-on-surface-variant font-medium" id="modal-empty-state">
                <span class="material-symbols-outlined text-4xl mb-2 text-outline">search_off</span>
                <p>Karyawan tidak ditemukan.</p>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="px-xl py-lg border-t border-outline-variant bg-surface flex justify-end">
            <button class="border border-outline-variant hover:bg-surface-container text-on-surface-variant px-lg py-sm rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-colors" id="btn-close-modal-footer">
                Batal
            </button>
        </div>
    </div>
</div>

<!-- MODAL: Konfirmasi Lepas Karyawan -->
<div class="fixed inset-0 bg-[#0b1c30]/60 backdrop-blur-sm z-50 items-center justify-center p-4 hidden" id="modal-konfirmasi">
    <div class="bg-white rounded-xl shadow-xl max-w-sm w-full border border-outline-variant p-6 text-center animate-in fade-in zoom-in-95 duration-200">
        <div class="w-14 h-14 bg-error-container text-error rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-3xl">person_remove</span>
        </div>
        <h3 class="font-title-sm text-title-sm font-bold text-on-surface mb-2">Lepas Karyawan?</h3>
        <p class="text-body-sm text-on-surface-variant mb-6 leading-relaxed">
            Apakah Anda yakin ingin mengeluarkan <span class="font-bold text-on-surface" id="konfirmasi-nama">Nama Karyawan</span> (<span class="font-mono text-[12px]" id="konfirmasi-nik">NIK</span>) dari departemennya? Statusnya akan kembali menjadi "Belum Ditempatkan".
        </p>
        <div class="flex gap-3 justify-center">
            <button class="flex-1 border border-outline-variant hover:bg-surface-container text-on-surface-variant py-2.5 rounded-lg text-sm font-semibold cursor-pointer transition-colors" id="btn-konfirmasi-batal">
                Batal
            </button>
            <button class="flex-1 bg-error hover:bg-error/90 text-white py-2.5 rounded-lg text-sm font-semibold cursor-pointer transition-colors" id="btn-konfirmasi-lepas">
                Ya, Lepas
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inisialisasi Elemen Modal
    const modalTambah = document.getElementById('modal-tambah');
    const modalKonfirmasi = document.getElementById('modal-konfirmasi');
    
    const btnOpenModal = document.getElementById('btn-open-modal');
    const btnCloseModal = document.getElementById('btn-close-modal');
    const btnCloseModalFooter = document.getElementById('btn-close-modal-footer');
    
    const btnKonfirmasiBatal = document.getElementById('btn-konfirmasi-batal');
    const btnKonfirmasiLepas = document.getElementById('btn-konfirmasi-lepas');
    
    // Input Pencarian & Filter
    const searchKaryawan = document.getElementById('search-karyawan');
    const filterJabatan = document.getElementById('filter-jabatan');
    const searchModalKaryawan = document.getElementById('search-modal-karyawan');
    
    // Badan Tabel & Stat
    const tableKaryawanBody = document.getElementById('table-karyawan-body');
    const modalTableBody = document.getElementById('modal-table-body');
    const modalEmptyState = document.getElementById('modal-empty-state');
    
    // Widget Rekap
    const widgetTotalStaff = document.getElementById('widget-total-staff');
    const widgetAktif = document.getElementById('widget-aktif');
    const widgetCuti = document.getElementById('widget-cuti');
    const widgetBaru = document.getElementById('widget-baru');
    
    // Label Teks Atas & Bawah
    const showingRange = document.getElementById('showing-range');
    const totalEntriesTop = document.getElementById('total-entries-top');
    const showingCountFooter = document.getElementById('showing-count-footer');
    const totalCountFooter = document.getElementById('total-count-footer');
    
    // Variabel Penampung Sementara untuk Aksi Lepas
    let targetRowToRelease = null;
    let targetNikToRelease = '';

    // ==========================================
    // 1. Logika Buka/Tutup Modal
    // ==========================================
    btnOpenModal.addEventListener('click', () => {
        modalTambah.classList.remove('hidden');
        modalTambah.classList.add('flex');
        searchModalKaryawan.focus();
    });
    
    const tutupModalTambah = () => {
        modalTambah.classList.remove('flex');
        modalTambah.classList.add('hidden');
        searchModalKaryawan.value = '';
        filterModalKaryawan('');
    };
    
    btnCloseModal.addEventListener('click', tutupModalTambah);
    btnCloseModalFooter.addEventListener('click', tutupModalTambah);

    // ==========================================
    // 2. Pencarian & Filter Real-Time Utama
    // ==========================================
    const filterTableUtama = () => {
        const query = searchKaryawan.value.toLowerCase().trim();
        const jabatanValue = filterJabatan.value;
        const rows = tableKaryawanBody.querySelectorAll('tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const nama = row.querySelector('td:nth-child(3)').innerText.toLowerCase();
            const nik = row.getAttribute('data-nik').toLowerCase();
            const jabatan = row.getAttribute('data-jabatan');
            
            const matchQuery = nama.includes(query) || nik.includes(query);
            const matchJabatan = !jabatanValue || jabatan.toLowerCase().includes(jabatanValue.toLowerCase());
            
            if (matchQuery && matchJabatan) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });
        
        // Perbarui nomor urut visual (hanya baris yang terlihat)
        let index = 1;
        rows.forEach(row => {
            if (!row.classList.contains('hidden')) {
                row.querySelector('td:first-child').innerText = index++;
            }
        });
        
        // Update info perolehan teks
        showingRange.innerText = visibleCount === 0 ? '0' : `1-${visibleCount}`;
        showingCountFooter.innerText = visibleCount;
    };
    
    searchKaryawan.addEventListener('input', filterTableUtama);
    filterJabatan.addEventListener('change', filterTableUtama);

    // ==========================================
    // 3. Pencarian Karyawan Baru pada Modal
    // ==========================================
    searchModalKaryawan.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        filterModalKaryawan(query);
    });

    function filterModalKaryawan(query) {
        const rows = modalTableBody.querySelectorAll('tr');
        let matchCount = 0;
        
        rows.forEach(row => {
            const nama = row.getAttribute('data-nama').toLowerCase();
            const nik = row.getAttribute('data-nik').toLowerCase();
            
            if (nama.includes(query) || nik.includes(query)) {
                row.classList.remove('hidden');
                matchCount++;
            } else {
                row.classList.add('hidden');
            }
        });
        
        if (matchCount === 0 && rows.length > 0) {
            modalEmptyState.classList.remove('hidden');
        } else {
            modalEmptyState.classList.add('hidden');
        }
    }

    // ==========================================
    // 4. Aksi Tambah / Pilih Karyawan Baru
    // ==========================================
    modalTableBody.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-assign')) {
            const row = e.target.closest('tr');
            const nama = row.getAttribute('data-nama');
            const nik = row.getAttribute('data-nik');
            const jabatan = row.getAttribute('data-jabatan');
            const dept = row.getAttribute('data-dept');
            const status = row.getAttribute('data-status');
            
            // Inisial avatar
            const inisial = nama.split(' ').map(n => n[0]).slice(0,2).join('').toUpperCase();
            
            // Dapatkan jumlah baris saat ini untuk nomor urut baru
            const currentTotal = tableKaryawanBody.querySelectorAll('tr').length + 1;
            
            // Tambahkan baris baru ke tabel utama
            const newRow = document.createElement('tr');
            newRow.className = 'hover:bg-primary/5 transition-colors group';
            newRow.setAttribute('data-nik', nik);
            newRow.setAttribute('data-jabatan', jabatan);
            newRow.setAttribute('data-dept', dept);
            newRow.setAttribute('data-status', status);
            newRow.innerHTML = `
                <td class="px-6 py-4 text-center text-on-surface font-semibold font-mono">${currentTotal}</td>
                <td class="px-6 py-4">
                    <div class="w-10 h-10 rounded-full border-2 border-surface-container shadow-sm overflow-hidden bg-primary/5 flex items-center justify-center font-bold text-primary">${inisial}</div>
                </td>
                <td class="px-6 py-4 font-bold text-on-surface">${nama}</td>
                <td class="px-6 py-4 font-mono text-on-surface-variant text-sm">${nik}</td>
                <td class="px-6 py-4 text-on-surface-variant">${jabatan}</td>
                <td class="px-6 py-4"><span class="px-2.5 py-1 bg-secondary-container/30 text-secondary rounded-full font-semibold text-xs uppercase">${dept}</span></td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold ${status === 'Tetap' ? 'bg-primary-container/10 text-primary' : (status === 'Kontrak' ? 'bg-surface-container-highest text-secondary' : 'bg-tertiary-container/10 text-tertiary')}">
                        Aktif
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <button class="w-8 h-8 flex items-center justify-center rounded bg-primary-container text-on-primary-container hover:brightness-110 active:scale-90 transition-all shadow-sm cursor-pointer" title="Detail / Edit">
                            <span class="material-symbols-outlined text-sm">edit</span>
                        </button>
                        <button class="btn-lepas w-8 h-8 flex items-center justify-center rounded bg-error text-white hover:brightness-110 active:scale-90 transition-all shadow-sm cursor-pointer" title="Lepas dari Departemen" data-nama="${nama}" data-nik="${nik}">
                            <span class="material-symbols-outlined text-sm">person_remove</span>
                        </button>
                    </div>
                </td>
            `;
            
            tableKaryawanBody.appendChild(newRow);
            
            // Hapus baris dari tabel modal agar tidak bisa dipilih ganda
            row.remove();
            
            // Update widget statistik & rentang
            updateStatistics();
            filterTableUtama();
            
            // Tutup modal secara halus
            tutupModalTambah();
        }
    });

    // ==========================================
    // 5. Aksi Lepas Karyawan (Trigger Konfirmasi)
    // ==========================================
    tableKaryawanBody.addEventListener('click', (e) => {
        const btnLepas = e.target.closest('.btn-lepas');
        if (btnLepas) {
            const nama = btnLepas.getAttribute('data-nama');
            const nik = btnLepas.getAttribute('data-nik');
            
            targetRowToRelease = btnLepas.closest('tr');
            targetNikToRelease = nik;
            
            document.getElementById('konfirmasi-nama').innerText = nama;
            document.getElementById('konfirmasi-nik').innerText = nik;
            
            modalKonfirmasi.classList.remove('hidden');
            modalKonfirmasi.classList.add('flex');
        }
    });
    
    // Batal Konfirmasi
    btnKonfirmasiBatal.addEventListener('click', () => {
        modalKonfirmasi.classList.remove('flex');
        modalKonfirmasi.classList.add('hidden');
        targetRowToRelease = null;
        targetNikToRelease = '';
    });
    
    // Setuju Konfirmasi (Proses Lepas secara Realtime)
    btnKonfirmasiLepas.addEventListener('click', () => {
        if (targetRowToRelease) {
            const nama = targetRowToRelease.querySelector('td:nth-child(3)').innerText;
            const NIK = targetRowToRelease.querySelector('td:nth-child(4)').innerText;
            const jabatan = targetRowToRelease.getAttribute('data-jabatan');
            const dept = targetRowToRelease.getAttribute('data-dept');
            const status = targetRowToRelease.getAttribute('data-status');
            const inisial = nama.split(' ').map(n => n[0]).slice(0,2).join('').toUpperCase();
            
            // Kembalikan karyawan ke daftar pilihan modal
            const returnRow = document.createElement('tr');
            returnRow.className = 'hover:bg-primary/5 transition-colors group';
            returnRow.setAttribute('data-nik', NIK);
            returnRow.setAttribute('data-nama', nama);
            returnRow.setAttribute('data-jabatan', jabatan);
            returnRow.setAttribute('data-dept', dept);
            returnRow.setAttribute('data-status', status);
            returnRow.innerHTML = `
                <td class="px-lg py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary/5 flex items-center justify-center text-xs font-bold text-primary/70">${inisial}</div>
                        <span class="font-bold text-on-surface">${nama}</span>
                    </div>
                </td>
                <td class="px-lg py-3 font-mono text-on-surface-variant">${NIK}</td>
                <td class="px-lg py-3 text-right">
                    <button class="btn-assign bg-primary hover:bg-primary-container text-white px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer active:scale-95 transition-all">
                        + Pilih
                    </button>
                </td>
            `;
            modalTableBody.appendChild(returnRow);
            
            // Hapus baris dari tabel utama
            targetRowToRelease.remove();
            
            // Update widget statistik & filter ulang nomor urut
            updateStatistics();
            filterTableUtama();
            
            // Tutup modal konfirmasi
            modalKonfirmasi.classList.remove('flex');
            modalKonfirmasi.classList.add('hidden');
            targetRowToRelease = null;
            targetNikToRelease = '';
        }
    });

    // ==========================================
    // 6. Fungsi Update Widget Statistik
    // ==========================================
    function updateStatistics() {
        const totalRows = tableKaryawanBody.querySelectorAll('tr').length;
        
        widgetTotalStaff.innerText = totalRows;
        widgetAktif.innerText = totalRows; // Diasumsikan aktif semua dalam demo
        widgetCuti.innerText = "0";
        
        // Jumlah karyawan dengan NIK baru "EMP-..." sebagai penanda karyawan baru
        let countBaru = 0;
        tableKaryawanBody.querySelectorAll('tr').forEach(row => {
            const nik = row.getAttribute('data-nik');
            if (nik.startsWith('EMP')) {
                countBaru++;
            }
        });
        widgetBaru.innerText = countBaru + 2; // Default 2 data baru dari awal
        
        totalEntriesTop.innerText = totalRows;
        totalCountFooter.innerText = totalRows;
    }
    
    // Panggil inisialisasi awal stat
    updateStatistics();
</script>
@endpush

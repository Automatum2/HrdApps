@extends('layouts.admin')

@section('title', 'Dashboard Manager - HRDApps')
@section('page_title', 'Dashboard Manager')

@section('content')
<!-- Summary Grid -->
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-stagger">
    <!-- Card 1: Total Karyawan -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow hover-card-float hover:border-primary/50 transition-colors flex justify-between items-start">
        <div>
            <p class="text-on-surface-variant font-medium text-sm mb-1">Total Karyawan</p>
            <h3 class="text-display-lg font-display-lg text-on-background font-bold" id="stat-total-karyawan">{{ $total_karyawan }}</h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-primary-container/10 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">group</span>
        </div>
    </div>
    <!-- Card 2: Hadir Hari Ini -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow hover-card-float hover:border-primary/50 transition-colors flex justify-between items-start">
        <div>
            <p class="text-on-surface-variant font-medium text-sm mb-1">Hadir Hari Ini</p>
            <h3 class="text-display-lg font-display-lg text-tertiary font-bold" id="stat-hadir-karyawan">{{ $hadir_hari_ini }}</h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-tertiary-container/10 flex items-center justify-center text-tertiary">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
        </div>
    </div>
    <!-- Card 3: Belum Absen -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow hover-card-float hover:border-primary/50 transition-colors flex justify-between items-start">
        <div>
            <p class="text-on-surface-variant font-medium text-sm mb-1">Belum Absen</p>
            <h3 class="text-display-lg font-display-lg text-error font-bold" id="stat-belum-absen">{{ $belum_absen }}</h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-error-container/20 flex items-center justify-center text-error">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">pending_actions</span>
        </div>
    </div>
    <!-- Card 4: Gaji Bulan Ini -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow hover-card-float hover:border-primary/50 transition-colors flex justify-between items-start">
        <div>
            <p class="text-on-surface-variant font-medium text-sm mb-1">Gaji Bulan Ini</p>
            <h3 class="text-display-lg font-display-lg text-primary font-bold" id="stat-estimasi-gaji">Rp 22.5jt</h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-primary-container/10 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">payments</span>
        </div>
    </div>
</section>

<!-- Widget Absensi Manager -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow mb-6 mt-6 flex flex-col md:flex-row items-center justify-between gap-6 animate-stagger relative overflow-hidden">
    <!-- Ornamen Background -->
    <div class="absolute right-0 top-0 w-64 h-full bg-gradient-to-l from-primary/5 to-transparent pointer-events-none"></div>
    
    <div class="flex items-center gap-5 z-10">
        <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary relative shadow-sm border border-primary/20">
            <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">fingerprint</span>
            <span class="absolute top-0 right-0 w-4 h-4 bg-error rounded-full border-2 border-white animate-pulse" id="status-dot"></span>
        </div>
        <div>
            <h4 class="font-bold text-lg text-on-background">Absensi Kehadiran</h4>
            <p class="text-xs text-on-surface-variant font-medium mt-1">Waktu Server: <span class="font-bold text-primary font-mono bg-primary/10 px-1.5 py-0.5 rounded" id="realtime-clock">--:--:-- WIB</span></p>
            <p class="text-[11px] text-error font-bold mt-1" id="status-text">Anda belum melakukan Clock In hari ini.</p>
        </div>
    </div>
    
    <div class="flex items-center gap-3 w-full md:w-auto z-10">
        <a href="{{ route('attendance.index') }}" class="flex-1 md:flex-none px-6 py-3 rounded-xl bg-primary text-white font-bold text-sm shadow-md hover:brightness-110 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 cursor-pointer btn-ripple group">
            <span class="material-symbols-outlined text-[18px] group-hover:animate-bounce">location_on</span>
            Halaman Absensi
        </a>
        <button class="flex-1 md:flex-none px-6 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-bold text-sm bg-surface-container-low transition-all flex items-center justify-center gap-2 cursor-not-allowed opacity-50" disabled id="btn-clockout">
            <span class="material-symbols-outlined text-[18px]">logout</span>
            Clock Out
        </button>
    </div>
</section>

<!-- Charts Bento Grid -->
<section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Attendance Trend -->
    <div class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow">
        <div class="flex items-center justify-between mb-8">
            <h4 class="font-title-sm text-title-sm text-on-background font-bold">Rekap Kehadiran Bulanan</h4>
            <div class="flex gap-2">
                <button class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-surface-container-low text-primary">Bulanan</button>
                <button class="text-xs font-semibold px-3 py-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">Mingguan</button>
            </div>
        </div>
        <div class="h-[280px] w-full relative flex items-end gap-1" id="chart-container">
            <!-- Custom Mock SVG Chart -->
            <svg class="w-full h-full" fill="none" viewBox="0 0 800 280" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="chartGradient" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stop-color="#0066ff" stop-opacity="0.3"></stop>
                        <stop offset="100%" stop-color="#0066ff" stop-opacity="0"></stop>
                    </linearGradient>
                </defs>
                <path d="M0 200 C 50 220, 100 180, 150 160 S 250 200, 300 150 S 400 50, 450 80 S 550 120, 600 90 S 700 110, 750 60 V 280 H 0 Z" fill="url(#chartGradient)"></path>
                <path d="M0 200 C 50 220, 100 180, 150 160 S 250 200, 300 150 S 400 50, 450 80 S 550 120, 600 90 S 700 110, 750 60" stroke="#0066ff" stroke-linecap="round" stroke-linejoin="round" stroke-width="4"></path>
                <!-- Data Points (Interactive Points with Tooltip Data) -->
                <circle cx="150" cy="160" fill="#0066ff" r="9" stroke="white" stroke-width="3" class="chart-point cursor-pointer transition-all duration-150 hover:r-12 hover:fill-white hover:stroke-primary" data-title="Minggu 1" data-value="Kehadiran: 92.5% (3 Karyawan Masuk, 0 Absen)"></circle>
                <circle cx="300" cy="150" fill="#0066ff" r="9" stroke="white" stroke-width="3" class="chart-point cursor-pointer transition-all duration-150 hover:r-12 hover:fill-white hover:stroke-primary" data-title="Minggu 2" data-value="Kehadiran: 95.0% (3 Karyawan Masuk, 0 Absen)"></circle>
                <circle cx="450" cy="80" fill="#0066ff" r="9" stroke="white" stroke-width="3" class="chart-point cursor-pointer transition-all duration-150 hover:r-12 hover:fill-white hover:stroke-primary" data-title="Minggu 3" data-value="Kehadiran: 98.2% (4 Karyawan Masuk, 0 Absen)"></circle>
                <circle cx="600" cy="90" fill="#0066ff" r="9" stroke="white" stroke-width="3" class="chart-point cursor-pointer transition-all duration-150 hover:r-12 hover:fill-white hover:stroke-primary" data-title="Minggu 4" data-value="Kehadiran: 96.5% (4 Karyawan Masuk, 0 Absen)"></circle>
                <circle cx="750" cy="60" fill="#0066ff" r="9" stroke="white" stroke-width="3" class="chart-point cursor-pointer transition-all duration-150 hover:r-12 hover:fill-white hover:stroke-primary" data-title="Evaluasi Akhir" data-value="Rata-rata Kehadiran Bulanan: 95.5%"></circle>

                <!-- Floating Statistics Badges (Sectors showing values along the line) -->
                <!-- Minggu 1 -->
                <rect x="125" y="122" width="50" height="20" rx="5" fill="#0050cb"></rect>
                <text x="150" y="136" text-anchor="middle" fill="white" class="font-bold text-[10px] font-sans" style="font-family: 'Inter', sans-serif; font-weight: 700;">92.5%</text>

                <!-- Minggu 2 -->
                <rect x="275" y="112" width="50" height="20" rx="5" fill="#0050cb"></rect>
                <text x="300" y="126" text-anchor="middle" fill="white" class="font-bold text-[10px] font-sans" style="font-family: 'Inter', sans-serif; font-weight: 700;">95.0%</text>

                <!-- Minggu 3 -->
                <rect x="425" y="42" width="50" height="20" rx="5" fill="#0050cb"></rect>
                <text x="450" y="56" text-anchor="middle" fill="white" class="font-bold text-[10px] font-sans" style="font-family: 'Inter', sans-serif; font-weight: 700;">98.2%</text>

                <!-- Minggu 4 -->
                <rect x="575" y="52" width="50" height="20" rx="5" fill="#0050cb"></rect>
                <text x="600" y="66" text-anchor="middle" fill="white" class="font-bold text-[10px] font-sans" style="font-family: 'Inter', sans-serif; font-weight: 700;">96.5%</text>

                <!-- Evaluasi Akhir -->
                <rect x="725" y="22" width="50" height="20" rx="5" fill="#0050cb"></rect>
                <text x="750" y="36" text-anchor="middle" fill="white" class="font-bold text-[10px] font-sans" style="font-family: 'Inter', sans-serif; font-weight: 700;">95.5%</text>
            </svg>
            
            <!-- Floating HTML Tooltip -->
            <div id="chart-tooltip" class="absolute bg-slate-900/90 text-white text-xs px-3 py-2 rounded-lg shadow-xl pointer-events-none transition-all duration-100 border border-white/10 hidden z-30 whitespace-nowrap">
                <span class="font-bold block text-primary-container" id="tooltip-title">Minggu 1</span>
                <span class="text-[11px] text-slate-300" id="tooltip-value">Kehadiran: 92.5%</span>
            </div>

            <div class="absolute bottom-0 left-0 w-full flex justify-between text-[10px] text-on-surface-variant font-medium px-2">
                <span class="">Minggu 1</span><span class="">Minggu 2</span><span class="">Minggu 3</span><span class="">Minggu 4</span>
            </div>
        </div>
    </div>
    <!-- Employee Status -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow flex flex-col">
        <h4 class="font-title-sm text-title-sm text-on-background font-bold mb-8">Status Karyawan</h4>
        <div class="flex-1 flex flex-col items-center justify-center relative">
            <!-- Donut Chart Mock -->
            <div class="w-48 h-48 rounded-full border-[18px] border-surface-container-low relative flex items-center justify-center">
                <div class="absolute inset-0 rounded-full border-[18px] border-primary" style="clip-path: polygon(50% 50%, 50% 0%, 100% 0%, 100% 100%, 50% 100%);"></div>
                <div class="absolute inset-0 rounded-full border-[18px] border-tertiary-fixed-dim" style="clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 20%);"></div>
                <div class="text-center">
                    <span class="text-headline-md font-display-lg block leading-none font-bold" id="donut-total">{{ $total_karyawan }}</span>
                    <span class="text-[10px] text-on-surface-variant uppercase tracking-wider">Total</span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-8 w-full">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-primary"></span>
                    <span class="text-body-sm text-on-surface-variant">Tetap (60%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-surface-container-highest"></span>
                    <span class="text-body-sm text-on-surface-variant">Kontrak (30%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-tertiary-fixed-dim"></span>
                    <span class="text-body-sm text-on-surface-variant">Magang (10%)</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Table Section -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-xl card-shadow overflow-hidden">
    <div class="px-8 py-6 flex items-center justify-between border-b border-outline-variant/10">
        <h4 class="font-title-sm text-title-sm text-on-background font-bold">Karyawan Terbaru</h4>
        <div class="flex gap-4">
            <div class="relative">
                <input class="text-sm px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 outline-none w-48 bg-white" placeholder="Cari nama..." type="text" id="search-anggota">
            </div>
            <button class="bg-primary text-on-primary px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition-opacity active:scale-95 cursor-pointer flex items-center gap-1.5 shadow" id="btn-open-modal">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                Tambah Karyawan
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left whitespace-nowrap">
            <thead>
                <tr class="bg-surface-container-low text-on-secondary-container">
                    <th class="font-table-header text-table-header px-8 py-4 uppercase tracking-wider">Nama</th>
                    <th class="font-table-header text-table-header px-8 py-4 uppercase tracking-wider">NIK</th>
                    <th class="font-table-header text-table-header px-8 py-4 uppercase tracking-wider">Jabatan</th>
                    <th class="font-table-header text-table-header px-8 py-4 uppercase tracking-wider">Department</th>
                    <th class="font-table-header text-table-header px-8 py-4 uppercase tracking-wider text-center">Status</th>
                    <th class="font-table-header text-table-header px-8 py-4 uppercase tracking-wider text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10 font-body-sm text-body-sm" id="table-anggota-body">
                @forelse($latest_employees as $emp)
                <tr class="hover:bg-primary/5 transition-colors group" data-nik="{{ $emp->nik }}" data-status="{{ $emp->status_kerja ?? 'Tetap' }}">
                    <td class="px-8 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-xs font-bold text-primary">
                                {{ strtoupper(substr($emp->nama_lengkap ?? 'U', 0, 2)) }}
                            </div>
                            <span class="font-medium text-on-background">{{ $emp->nama_lengkap }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-4 text-on-surface-variant font-mono text-sm">{{ $emp->nik }}</td>
                    <td class="px-8 py-4 text-on-surface-variant">{{ $emp->jabatan ?? 'Karyawan' }}</td>
                    <td class="px-8 py-4">
                        <span class="px-2 py-1 rounded bg-secondary-container/30 text-secondary text-xs font-semibold uppercase">{{ $emp->department->nama_departemen ?? 'Umum' }}</span>
                    </td>
                    <td class="px-8 py-4 text-center">
                        <span class="px-3 py-1 rounded-full bg-primary-container/10 text-primary text-xs font-bold">{{ $emp->status_kerja ?? 'Tetap' }}</span>
                    </td>
                    <td class="px-8 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="w-8 h-8 rounded-lg bg-surface-container-low text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-all cursor-pointer" title="Detail"><span class="material-symbols-outlined text-lg">search</span></button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-6 text-center text-slate-500">Belum ada karyawan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-8 py-4 border-t border-outline-variant/10 flex items-center justify-between">
        <p class="text-body-sm text-on-surface-variant" id="table-entries-info">Menampilkan terbaru dari {{ $total_karyawan }} entri</p>
        <div class="flex gap-1">
        </div>
    </div>
</section>
@endsection

@push('modals')
<!-- MODAL: Tambah Karyawan Baru (Assign Departemen) -->
<div class="bg-[#0b1c30]/60 backdrop-blur-sm" id="modal-tambah" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 16px;">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant flex flex-col max-h-[85vh] overflow-hidden animate-modal-pop" style="width: 100%; max-width: 640px; min-width: 280px; display: flex; flex-direction: column;">
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
<div class="bg-[#0b1c30]/60 backdrop-blur-sm" id="modal-konfirmasi" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 16px;">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant p-6 text-center animate-modal-pop" style="width: 100%; max-width: 400px; min-width: 280px; display: flex; flex-direction: column; align-items: center;">
        <div class="w-14 h-14 bg-error-container text-error rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-3xl">person_remove</span>
        </div>
        <h3 class="font-title-sm text-title-sm font-bold text-on-surface mb-2">Lepas Karyawan?</h3>
        <p class="text-body-sm text-on-surface-variant mb-6 leading-relaxed">
            Apakah Anda yakin ingin melepas <span class="font-bold text-on-surface" id="konfirmasi-nama">Nama Karyawan</span> (<span class="font-mono text-[12px]" id="konfirmasi-nik">NIK</span>) dari departemen ini? Statusnya akan kembali menjadi "Belum Ditempatkan".
        </p>
        <div class="flex gap-3 justify-center w-full">
            <button class="flex-1 border border-outline-variant hover:bg-surface-container text-on-surface-variant py-2.5 rounded-lg text-sm font-semibold cursor-pointer transition-colors" id="btn-konfirmasi-batal">
                Batal
            </button>
            <button class="flex-1 bg-error hover:bg-error/90 text-white py-2.5 rounded-lg text-sm font-semibold cursor-pointer transition-colors" id="btn-konfirmasi-lepas">
                Ya, Lepas
            </button>
        </div>
    </div>
</div>

<!-- MODAL: Form Detail Penempatan Karyawan Baru -->
<div class="bg-[#0b1c30]/60 backdrop-blur-sm" id="modal-assign-detail" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 10000; display: none; align-items: center; justify-content: center; padding: 16px;">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant flex flex-col max-h-[90vh] overflow-hidden animate-modal-pop" style="width: 100%; max-width: 480px; min-width: 280px; display: flex; flex-direction: column;">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-slate-50">
            <div>
                <h3 class="font-bold text-slate-800 text-base">Detail Penempatan Karyawan</h3>
                <p class="text-xs text-slate-500 mt-1">Lengkapi jabatan dan status kerja untuk <span class="font-bold text-primary" id="assign-nama-karyawan">Nama</span>.</p>
            </div>
            <button class="p-1 hover:bg-slate-200 rounded-full text-slate-400 cursor-pointer" id="btn-close-assign-modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <!-- Form Content -->
        <form id="form-assign-detail" class="p-6 space-y-4 overflow-y-auto">
            <!-- Jabatan Field -->
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="assign-jabatan">Jabatan Baru</label>
                <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="assign-jabatan" name="assign-jabatan" placeholder="Contoh: QA Engineer atau HR Staff" type="text" required>
            </div>
            
            <!-- Departemen Field -->
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="assign-departemen">Departemen</label>
                <div class="relative">
                    <select class="w-full appearance-none bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 cursor-pointer" id="assign-departemen" name="assign-departemen" required>
                        <option value="HRD">HRD</option>
                        <option value="IT">IT</option>
                        <option value="Finance">Finance</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Operasional">Operasional</option>
                        <option value="Legal">Legal</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                </div>
            </div>
            
            <!-- Status Hubungan Kerja Field -->
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="assign-status">Status Kerja</label>
                <div class="relative">
                    <select class="w-full appearance-none bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 cursor-pointer" id="assign-status" name="assign-status">
                        <option value="Tetap">Karyawan Tetap</option>
                        <option value="Kontrak">Karyawan Kontrak</option>
                        <option value="Magang">Magang (Internship)</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                </div>
            </div>
        </form>
        
        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-outline-variant bg-slate-50 flex justify-end gap-3">
            <button type="button" class="border border-slate-300 hover:bg-slate-100 text-slate-600 px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all" id="btn-cancel-assign-modal">
                Batal
            </button>
            <button type="submit" form="form-assign-detail" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all">
                Tempatkan Karyawan
            </button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    // Inisialisasi Elemen Modal
    const modalTambah = document.getElementById('modal-tambah');
    const modalKonfirmasi = document.getElementById('modal-konfirmasi');
    const modalAssignDetail = document.getElementById('modal-assign-detail');
    
    const btnOpenModal = document.getElementById('btn-open-modal');
    const btnCloseModal = document.getElementById('btn-close-modal');
    const btnCloseModalFooter = document.getElementById('btn-close-modal-footer');
    
    const btnCloseAssignModal = document.getElementById('btn-close-assign-modal');
    const btnCancelAssignModal = document.getElementById('btn-cancel-assign-modal');
    const formAssignDetail = document.getElementById('form-assign-detail');
    const assignNamaKaryawan = document.getElementById('assign-nama-karyawan');
    
    const btnKonfirmasiBatal = document.getElementById('btn-konfirmasi-batal');
    const btnKonfirmasiLepas = document.getElementById('btn-konfirmasi-lepas');
    
    let activeAssignData = null;
    
    // Input Pencarian
    const searchAnggota = document.getElementById('search-anggota');
    const searchModalKaryawan = document.getElementById('search-modal-karyawan');
    
    // Badan Tabel & Stat
    const tableAnggotaBody = document.getElementById('table-anggota-body');
    const modalTableBody = document.getElementById('modal-table-body');
    const modalEmptyState = document.getElementById('modal-empty-state');
    
    const statTotalKaryawan = document.getElementById('stat-total-karyawan');
    const statHadirKaryawan = document.getElementById('stat-hadir-karyawan');
    const donutTotal = document.getElementById('donut-total');
    
    // Variabel Penampung Sementara untuk Aksi Lepas
    let targetRowToRelease = null;
    let targetNikToRelease = '';

    // ==========================================
    // 1. Logika Buka/Tutup Modal
    // ==========================================
    btnOpenModal.addEventListener('click', () => {
        modalTambah.style.display = 'flex';
        searchModalKaryawan.focus();
    });
    
    const tutupModalTambah = () => {
        modalTambah.style.display = 'none';
        searchModalKaryawan.value = '';
        filterModalKaryawan('');
    };
    
    btnCloseModal.addEventListener('click', tutupModalTambah);
    btnCloseModalFooter.addEventListener('click', tutupModalTambah);

    // ==========================================
    // 2. Pencarian Real-Time Anggota Departemen
    // ==========================================
    searchAnggota.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        const rows = tableAnggotaBody.querySelectorAll('tr');
        
        rows.forEach(row => {
            const nama = row.querySelector('td:first-child').innerText.toLowerCase();
            const nik = row.getAttribute('data-nik').toLowerCase();
            if (nama.includes(query) || nik.includes(query)) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    });

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
    // Load data karyawan baru dari localStorage
    const loadKaryawanBaruDariStorage = () => {
        const listKaryawanStr = localStorage.getItem('karyawan_baru');
        if (listKaryawanStr) {
            const listKaryawan = JSON.parse(listKaryawanStr);
            listKaryawan.forEach(k => {
                if (k.departemen === 'Belum Ditempatkan') {
                    const existingInModal = modalTableBody.querySelector(`tr[data-nik="${k.nik}"]`);
                    if (!existingInModal) {
                        const inisial = k.nama.split(' ').map(n => n[0]).slice(0,2).join('').toUpperCase();
                        const modalRow = document.createElement('tr');
                        modalRow.className = 'hover:bg-primary/5 transition-colors group';
                        modalRow.setAttribute('data-nik', k.nik);
                        modalRow.setAttribute('data-nama', k.nama);
                        modalRow.innerHTML = `
                            <td class="px-lg py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-primary/5 flex items-center justify-center text-xs font-bold text-primary/70">${inisial}</div>
                                    <span class="font-bold text-on-surface">${k.nama}</span>
                                </div>
                            </td>
                            <td class="px-lg py-3 font-mono text-on-surface-variant">${k.nik}</td>
                            <td class="px-lg py-3 text-right">
                                <button class="btn-assign bg-primary hover:bg-primary-container text-white px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer active:scale-95 transition-all">
                                    + Pilih
                                </button>
                            </td>
                        `;
                        modalTableBody.appendChild(modalRow);
                    }
                } else if (k.departemen !== 'Belum Ditempatkan') {
                    const existingInTable = tableAnggotaBody.querySelector(`tr[data-nik="${k.nik}"]`);
                    if (!existingInTable) {
                        const inisial = k.nama.split(' ').map(n => n[0]).slice(0,2).join('').toUpperCase();
                        const newRow = document.createElement('tr');
                        newRow.className = 'hover:bg-primary/5 transition-colors group';
                        newRow.setAttribute('data-nik', k.nik);
                        newRow.setAttribute('data-status', k.status || 'Kontrak');
                        newRow.innerHTML = `
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-xs font-bold text-primary">${inisial}</div>
                                    <span class="font-medium text-on-background">${k.nama}</span>
                                </div>
                            </td>
                            <td class="px-8 py-4 text-on-surface-variant font-mono text-sm">${k.nik}</td>
                            <td class="px-8 py-4 text-on-surface-variant">${k.jabatan}</td>
                            <td class="px-8 py-4">
                                <span class="px-2 py-1 rounded bg-secondary-container/30 text-secondary text-xs font-semibold uppercase">${k.departemen}</span>
                            </td>
                            <td class="px-8 py-4 text-center">
                                <span class="px-3 py-1 rounded-full ${k.status === 'Tetap' ? 'bg-primary-container/10 text-primary' : (k.status === 'Kontrak' ? 'bg-surface-container-highest text-secondary' : 'bg-tertiary-container/10 text-tertiary')} text-xs font-bold">${k.status || 'Kontrak'}</span>
                            </td>
                            <td class="px-8 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button class="w-8 h-8 rounded-lg bg-surface-container-low text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-all cursor-pointer" title="Detail"><span class="material-symbols-outlined text-lg">search</span></button>
                                    <button class="btn-lepas w-8 h-8 rounded-lg bg-surface-container-low text-error flex items-center justify-center hover:bg-error hover:text-white transition-all cursor-pointer active:scale-90" title="Lepas dari Departemen" data-nama="${k.nama}" data-nik="${k.nik}"><span class="material-symbols-outlined text-lg">person_remove</span></button>
                                </div>
                            </td>
                        `;
                        tableAnggotaBody.appendChild(newRow);
                    }
                }
            });
            updateStatistics();
        }
    };
    
    // Panggil saat halaman diload
    loadKaryawanBaruDariStorage();

    modalTableBody.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-assign')) {
            const row = e.target.closest('tr');
            const nama = row.getAttribute('data-nama');
            const nik = row.getAttribute('data-nik');
            
            activeAssignData = { row, nama, nik };
            assignNamaKaryawan.innerText = nama;
            
            // Tutup modal pilihan awal
            tutupModalTambah();
            
            // Buka modal detail penempatan
            modalAssignDetail.style.display = 'flex';
            document.getElementById('assign-jabatan').focus();
        }
    });
    
    const tutupModalAssignDetail = () => {
        modalAssignDetail.style.display = 'none';
        formAssignDetail.reset();
        activeAssignData = null;
    };
    
    btnCloseAssignModal.addEventListener('click', tutupModalAssignDetail);
    btnCancelAssignModal.addEventListener('click', tutupModalAssignDetail);
    
    formAssignDetail.addEventListener('submit', (e) => {
        e.preventDefault();
        if (activeAssignData) {
            const nama = activeAssignData.nama;
            const nik = activeAssignData.nik;
            const jabatan = document.getElementById('assign-jabatan').value;
            const status = document.getElementById('assign-status').value;
            const dept = document.getElementById('assign-departemen').value;
            
            const inisial = nama.split(' ').map(n => n[0]).slice(0,2).join('').toUpperCase();
            
            const newRow = document.createElement('tr');
            newRow.className = 'hover:bg-primary/5 transition-colors group';
            newRow.setAttribute('data-nik', nik);
            newRow.setAttribute('data-status', status);
            newRow.innerHTML = `
                <td class="px-8 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-xs font-bold text-primary">${inisial}</div>
                        <span class="font-medium text-on-background">${nama}</span>
                    </div>
                </td>
                <td class="px-8 py-4 text-on-surface-variant font-mono text-sm">${nik}</td>
                <td class="px-8 py-4 text-on-surface-variant">${jabatan}</td>
                <td class="px-8 py-4">
                    <span class="px-2 py-1 rounded bg-secondary-container/30 text-secondary text-xs font-semibold uppercase">${dept}</span>
                </td>
                <td class="px-8 py-4 text-center">
                    <span class="px-3 py-1 rounded-full ${status === 'Tetap' ? 'bg-primary-container/10 text-primary' : (status === 'Kontrak' ? 'bg-surface-container-highest text-secondary' : 'bg-tertiary-container/10 text-tertiary')} text-xs font-bold">${status}</span>
                </td>
                <td class="px-8 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <button class="w-8 h-8 rounded-lg bg-surface-container-low text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-all cursor-pointer" title="Detail"><span class="material-symbols-outlined text-lg">search</span></button>
                        <button class="btn-lepas w-8 h-8 rounded-lg bg-surface-container-low text-error flex items-center justify-center hover:bg-error hover:text-white transition-all cursor-pointer active:scale-90" title="Lepas dari Departemen" data-nama="${nama}" data-nik="${nik}"><span class="material-symbols-outlined text-lg">person_remove</span></button>
                    </div>
                </td>
            `;
            
            tableAnggotaBody.appendChild(newRow);
            activeAssignData.row.remove();
            
            // Perbarui status di localStorage
            const listKaryawanStr = localStorage.getItem('karyawan_baru');
            if (listKaryawanStr) {
                const listKaryawan = JSON.parse(listKaryawanStr);
                const targetK = listKaryawan.find(item => item.nik === nik);
                if (targetK) {
                    targetK.departemen = dept;
                    targetK.jabatan = jabatan;
                    targetK.status = status;
                    localStorage.setItem('karyawan_baru', JSON.stringify(listKaryawan));
                }
            }
            
            updateStatistics();
            tutupModalAssignDetail();
            alert(`Karyawan ${nama} berhasil ditempatkan di departemen ${dept} sebagai ${jabatan}!`);
        }
    });

    // ==========================================
    // 5. Aksi Lepas Karyawan (Trigger Konfirmasi)
    // ==========================================
    tableAnggotaBody.addEventListener('click', (e) => {
        const btnLepas = e.target.closest('.btn-lepas');
        if (btnLepas) {
            const nama = btnLepas.getAttribute('data-nama');
            const nik = btnLepas.getAttribute('data-nik');
            
            targetRowToRelease = btnLepas.closest('tr');
            targetNikToRelease = nik;
            
            document.getElementById('konfirmasi-nama').innerText = nama;
            document.getElementById('konfirmasi-nik').innerText = nik;
            
            modalKonfirmasi.style.display = 'flex';
        }
    });
    
    // Batal Konfirmasi
    btnKonfirmasiBatal.addEventListener('click', () => {
        modalKonfirmasi.style.display = 'none';
        targetRowToRelease = null;
        targetNikToRelease = '';
    });
    
    // Setuju Konfirmasi (Proses Lepas secara Realtime)
    btnKonfirmasiLepas.addEventListener('click', () => {
        if (targetRowToRelease) {
            const nama = targetRowToRelease.querySelector('td:first-child span:nth-child(2)').innerText;
            const NIK = targetRowToRelease.querySelector('td:nth-child(2)').innerText;
            const jabatan = targetRowToRelease.querySelector('td:nth-child(3)').innerText;
            const dept = targetRowToRelease.querySelector('td:nth-child(4) span').innerText;
            const status = targetRowToRelease.getAttribute('data-status');
            const inisial = nama.split(' ').map(n => n[0]).slice(0,2).join('').toUpperCase();
            
            // Perbarui status di localStorage jika ada
            const listKaryawanStr = localStorage.getItem('karyawan_baru');
            let isKaryawanBaru = false;
            if (listKaryawanStr) {
                const listKaryawan = JSON.parse(listKaryawanStr);
                const targetK = listKaryawan.find(item => item.nik === NIK);
                if (targetK) {
                    targetK.departemen = 'Belum Ditempatkan';
                    targetK.jabatan = 'Belum Ditentukan';
                    localStorage.setItem('karyawan_baru', JSON.stringify(listKaryawan));
                    isKaryawanBaru = true;
                }
            }
            
            if (isKaryawanBaru) {
                const returnRow = document.createElement('tr');
                returnRow.className = 'hover:bg-primary/5 transition-colors group';
                returnRow.setAttribute('data-nik', NIK);
                returnRow.setAttribute('data-nama', nama);
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
            } else {
                // Karyawan dummy default, kembalikan dengan attribute lengkapnya
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
            }
            
            // Hapus baris dari tabel utama dashboard
            targetRowToRelease.remove();
            
            // Update widget statistik
            updateStatistics();
            
            // Tutup modal konfirmasi
            modalKonfirmasi.style.display = 'none';
            targetRowToRelease = null;
            targetNikToRelease = '';
        }
    });

    // ==========================================
    // 6. Fungsi Update Widget Statistik
    // ==========================================
    function updateStatistics() {
        const totalRows = tableAnggotaBody.querySelectorAll('tr').length;
        statTotalKaryawan.innerText = totalRows;
        statHadirKaryawan.innerText = totalRows; // Semua hadir dalam simulasi
        donutTotal.innerText = totalRows;
        
        // Gaji simulasi (Tetap = 8.5jt, Kontrak = 7jt, Magang = 4jt)
        let totalGaji = 0;
        tableAnggotaBody.querySelectorAll('tr').forEach(row => {
            const status = row.getAttribute('data-status');
            if (status === 'Tetap') totalGaji += 8.5;
            else if (status === 'Kontrak') totalGaji += 7.0;
            else totalGaji += 4.0;
        });
        
        statHadirKaryawan.innerText = totalRows;
        document.getElementById('stat-belum-absen').innerText = "0";
        document.getElementById('stat-estimasi-gaji').innerText = `Rp ${totalGaji.toFixed(1)}jt`;
        document.getElementById('table-entries-info').innerText = `Menampilkan 1 - ${totalRows} dari ${totalRows} entri`;
    }

    // ==========================================
    // 7. Logika Tooltip Interaktif untuk Chart SVG
    // ==========================================
    const chartTooltip = document.getElementById('chart-tooltip');
    const tooltipTitle = document.getElementById('tooltip-title');
    const tooltipValue = document.getElementById('tooltip-value');
    const chartContainer = document.getElementById('chart-container');

    document.querySelectorAll('.chart-point').forEach(point => {
        point.addEventListener('mouseenter', (e) => {
            const title = point.getAttribute('data-title');
            const value = point.getAttribute('data-value');
            
            tooltipTitle.innerText = title;
            tooltipValue.innerText = value;
            
            chartTooltip.classList.remove('hidden');
            
            // Dapatkan ukuran kontainer dan titik koordinat
            const rect = point.getBoundingClientRect();
            const containerRect = chartContainer.getBoundingClientRect();
            
            // Atur posisi tooltip melayang tepat di atas titik circle
            const x = rect.left - containerRect.left + (rect.width / 2) - (chartTooltip.offsetWidth / 2);
            const y = rect.top - containerRect.top - chartTooltip.offsetHeight - 10;
            
            chartTooltip.style.left = `${x}px`;
            chartTooltip.style.top = `${y}px`;
        });
        
        point.addEventListener('mouseleave', () => {
            chartTooltip.classList.add('hidden');
        });
    });

    // ==========================================
    // Real-time Clock untuk Widget Absensi
    // ==========================================
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        const clockElement = document.getElementById('realtime-clock');
        if(clockElement) {
            clockElement.textContent = `${hours}:${minutes}:${seconds} WIB`;
        }
    }
    
    // Update jam setiap detik
    setInterval(updateClock, 1000);
    updateClock(); // Initialize immediately
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Dashboard Absensi - HRDApps')
@section('page_title', 'Dashboard Absensi')

@section('content')
<!-- Top Section: Welcome Banner & Status -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Welcome Banner -->
    <div class="lg:col-span-2 relative overflow-hidden rounded-xl bg-[#1e293b] text-white p-8 flex flex-col md:flex-row items-center justify-between shadow-lg border border-outline-variant">
        <div class="relative z-10 space-y-2 text-center md:text-left">
            <p class="font-body-md opacity-80">Selamat Datang,</p>
            <h3 class="font-display-lg text-3xl font-extrabold tracking-tight">{{ session('user_name', 'Ahmad Fadillah') }}</h3>
            <div class="flex items-center gap-4 mt-4 justify-center md:justify-start">
                <span class="px-3 py-1 bg-primary text-white rounded-full text-[12px] font-bold tracking-wide uppercase">Tetap Produktif!</span>
                <span class="text-body-sm opacity-70">Employee ID : {{ session('employee_id', '00001221') }}</span>
            </div>
        </div>
        <div class="mt-6 md:mt-0 relative z-10 w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-primary/20 shadow-xl overflow-hidden bg-slate-700">
            <img class="w-full h-full object-cover" alt="Foto Profil" src="{{ session('user_photo', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBjl_7eDbZzV-YmgYaDkKXw2utynUbqir9TM--UUb9IB5uqDHpneCJ85UyHiq_h0bjtdCPpdNJzEmRA2LjpAuGHaa6rQaIofl84ZS8otUmiQgfErEdxgewajk62eB0OnWvDCxKjcO1EYGVpLNek1llzamy9omOwgryH4Ge06KawUp8yimHFRZb52LL5b-w5SF81fNjx5jA5eH0Q4ZY4-16Tnrr32TDUK67uafVgmTcyeaLabciJ51Pe') }}">
        </div>
        <!-- Background Decoration -->
        <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
        <div class="absolute -left-10 -top-10 w-48 h-48 bg-primary-container/5 rounded-full blur-2xl"></div>
    </div>
    
    <!-- Clock Status Card -->
    <div class="bg-white rounded-xl border border-outline-variant p-6 flex flex-col justify-between shadow-sm" id="absensi-status-card">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-title-sm text-title-sm text-on-surface font-semibold text-lg">Status Absensi</h4>
            <span class="material-symbols-outlined text-outline" id="status-icon-badge">verified</span>
        </div>
        <div class="space-y-4">
            <!-- Box status (Default: Belum Absen) -->
            <div class="flex items-center gap-4 p-4 bg-slate-100 rounded-xl border border-slate-200 transition-all duration-300" id="status-box">
                <div class="w-12 h-12 rounded-lg bg-slate-400 flex items-center justify-center text-white transition-all duration-300" id="status-icon-container">
                    <span class="material-symbols-outlined text-2xl" id="status-icon">pending_actions</span>
                </div>
                <div>
                    <p class="text-body-sm text-slate-700 font-bold leading-tight transition-all duration-300" id="status-text">Belum Absen Masuk</p>
                    <p class="text-[12px] text-on-surface-variant" id="status-desc">Silakan lakukan absensi hari ini</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 text-center">
                <div class="p-3 bg-surface-container-low rounded-lg border border-outline-variant/50">
                    <p class="text-[10px] uppercase tracking-wider text-outline mb-1 font-bold">Jam Masuk</p>
                    <p class="font-bold text-on-surface text-lg transition-all duration-300" id="clock-in-time">--:--</p>
                </div>
                <div class="p-3 bg-surface-container-low rounded-lg border border-outline-variant/50">
                    <p class="text-[10px] uppercase tracking-wider text-outline mb-1 font-bold">Jam Keluar</p>
                    <p class="font-bold text-on-surface text-lg transition-all duration-300" id="clock-out-time">--:--</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons Section (Bento Float) -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <button class="hover-card-float group flex items-center justify-center gap-4 py-6 bg-primary text-white rounded-xl font-bold text-lg shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all active:scale-[0.98] cursor-pointer" id="btn-clock-in">
        <span class="material-symbols-outlined text-3xl group-hover:rotate-12 transition-transform">schedule</span>
        <span>Clock In Sekarang</span>
    </button>
    <button class="hover-card-float group flex items-center justify-center gap-4 py-6 bg-[#1e293b] text-white rounded-xl font-bold text-lg shadow-lg hover:bg-slate-800 transition-all active:scale-[0.98] opacity-50 cursor-not-allowed" id="btn-clock-out" disabled>
        <span class="material-symbols-outlined text-3xl group-hover:-rotate-12 transition-transform">logout</span>
        <span>Clock Out Sekarang</span>
    </button>
</div>

<!-- Middle Section: Summary & Calendar -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Monthly Summary Bento -->
    <div class="lg:col-span-4 space-y-6">
        <h4 class="font-title-sm text-title-sm font-semibold text-lg">Ringkasan Bulan Ini</h4>
        <div class="grid grid-cols-2 gap-4">
            <div class="hover-card-float p-4 bg-white rounded-xl border border-outline-variant shadow-sm hover:border-primary/30 transition-colors">
                <p class="text-label-uppercase text-outline mb-2 text-xs font-bold tracking-wider">HADIR</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-tertiary-container" id="summary-hadir">14</span>
                    <span class="text-body-sm text-outline text-xs">Hari</span>
                </div>
                <div class="mt-3 w-full bg-slate-100 rounded-full h-1.5">
                    <div class="bg-tertiary-container h-1.5 rounded-full transition-all duration-500" style="width: 70%" id="summary-hadir-progress"></div>
                </div>
            </div>
            <div class="hover-card-float p-4 bg-white rounded-xl border border-outline-variant shadow-sm hover:border-primary/30 transition-colors">
                <p class="text-label-uppercase text-outline mb-2 text-xs font-bold tracking-wider">IZIN</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-primary">01</span>
                    <span class="text-body-sm text-outline text-xs">Hari</span>
                </div>
                <div class="mt-3 w-full bg-slate-100 rounded-full h-1.5">
                    <div class="bg-primary h-1.5 rounded-full" style="width: 5%"></div>
                </div>
            </div>
            <div class="hover-card-float p-4 bg-white rounded-xl border border-outline-variant shadow-sm hover:border-primary/30 transition-colors">
                <p class="text-label-uppercase text-outline mb-2 text-xs font-bold tracking-wider">SAKIT</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-secondary">00</span>
                    <span class="text-body-sm text-outline text-xs">Hari</span>
                </div>
                <div class="mt-3 w-full bg-slate-100 rounded-full h-1.5">
                    <div class="bg-secondary h-1.5 rounded-full" style="width: 0%"></div>
                </div>
            </div>
            <div class="hover-card-float p-4 bg-white rounded-xl border border-outline-variant shadow-sm hover:border-primary/30 transition-colors">
                <p class="text-label-uppercase text-outline mb-2 text-xs font-bold tracking-wider">ALPHA</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-error">00</span>
                    <span class="text-body-sm text-outline text-xs">Hari</span>
                </div>
                <div class="mt-3 w-full bg-slate-100 rounded-full h-1.5">
                    <div class="bg-error h-1.5 rounded-full" style="width: 0%"></div>
                </div>
            </div>
        </div>
        
        <!-- Additional Widget: Next Holiday -->
        <div class="hover-card-float p-6 bg-primary/5 border border-primary/20 rounded-xl relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-label-uppercase text-primary font-bold mb-1 text-xs tracking-wider">LIBUR BERIKUTNYA</p>
                <h5 class="text-title-sm font-bold text-slate-800 text-lg">Idul Adha 1447 H</h5>
                <p class="text-body-sm text-on-surface-variant text-sm mt-1">Selasa, 16 Juni 2026</p>
            </div>
            <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-7xl text-primary/5 group-hover:scale-110 transition-transform">event_upcoming</span>
        </div>
    </div>
    
    <!-- Monthly Calendar -->
    <div class="lg:col-span-8 bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden flex flex-col">
        <div class="p-6 border-b border-outline-variant flex items-center justify-between bg-surface-container-low/30">
            <div>
                <h4 class="font-title-sm text-title-sm font-semibold text-lg">Kalender Absensi</h4>
                <p class="text-body-sm text-on-surface-variant text-sm mt-1">Rekam kehadiran Juni 2026</p>
            </div>
            <div class="flex items-center gap-2">
                <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-slate-50 active:scale-95 transition-all"><span class="material-symbols-outlined">chevron_left</span></button>
                <span class="px-4 py-2 font-bold text-on-surface">Juni 2026</span>
                <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-slate-50 active:scale-95 transition-all"><span class="material-symbols-outlined">chevron_right</span></button>
            </div>
        </div>
        <div class="p-6 flex-1">
            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 mb-4 font-bold text-xs uppercase text-center">
                <div class="text-error py-2">Min</div>
                <div class="text-slate-500 py-2">Sen</div>
                <div class="text-slate-500 py-2">Sel</div>
                <div class="text-slate-500 py-2">Rab</div>
                <div class="text-slate-500 py-2">Kam</div>
                <div class="text-slate-500 py-2">Jum</div>
                <div class="text-slate-500 py-2">Jum</div>
            </div>
            <div class="grid grid-cols-7 gap-px bg-slate-200 rounded-lg overflow-hidden border border-slate-200">
                <!-- 1 Juni 2026 - 15 Juni 2026 -->
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors relative">
                    <span class="text-body-sm text-slate-400">1</span>
                    <div class="absolute bottom-2 right-2 w-2.5 h-2.5 rounded-full bg-tertiary-container"></div>
                </div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors relative">
                    <span class="text-body-sm text-slate-400">2</span>
                    <div class="absolute bottom-2 right-2 w-2.5 h-2.5 rounded-full bg-tertiary-container"></div>
                </div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors relative">
                    <span class="text-body-sm text-slate-400">3</span>
                    <div class="absolute bottom-2 right-2 w-2.5 h-2.5 rounded-full bg-primary"></div>
                </div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors relative">
                    <span class="text-body-sm text-slate-400">4</span>
                    <div class="absolute bottom-2 right-2 w-2.5 h-2.5 rounded-full bg-tertiary-container"></div>
                </div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors relative">
                    <span class="text-body-sm text-slate-400">5</span>
                    <div class="absolute bottom-2 right-2 w-2.5 h-2.5 rounded-full bg-tertiary-container"></div>
                </div>
                <div class="bg-slate-100 aspect-square p-2"><span class="text-body-sm text-slate-300">6</span></div>
                <div class="bg-slate-100 aspect-square p-2"><span class="text-body-sm text-slate-300">7</span></div>
                
                <!-- Minggu 2 -->
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors relative">
                    <span class="text-body-sm text-slate-400">8</span>
                    <div class="absolute bottom-2 right-2 w-2.5 h-2.5 rounded-full bg-tertiary-container"></div>
                </div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors relative">
                    <span class="text-body-sm text-slate-400">9</span>
                    <div class="absolute bottom-2 right-2 w-2.5 h-2.5 rounded-full bg-tertiary-container"></div>
                </div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors relative">
                    <span class="text-body-sm text-slate-400">10</span>
                    <div class="absolute bottom-2 right-2 w-2.5 h-2.5 rounded-full bg-tertiary-container"></div>
                </div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors relative">
                    <span class="text-body-sm text-slate-400">11</span>
                    <div class="absolute bottom-2 right-2 w-2.5 h-2.5 rounded-full bg-tertiary-container"></div>
                </div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors relative">
                    <span class="text-body-sm text-slate-400">12</span>
                    <div class="absolute bottom-2 right-2 w-2.5 h-2.5 rounded-full bg-tertiary-container"></div>
                </div>
                <div class="bg-slate-100 aspect-square p-2"><span class="text-body-sm text-slate-300">13</span></div>
                <div class="bg-slate-100 aspect-square p-2"><span class="text-body-sm text-slate-300">14</span></div>
                
                <!-- Hari Ini (15 Juni) -->
                <div class="bg-primary/5 aspect-square p-2 group border-2 border-primary relative" id="calendar-today">
                    <span class="text-body-sm font-bold text-primary">15</span>
                    <div class="absolute bottom-2 right-2 flex gap-1" id="calendar-today-indicator">
                        <!-- Indikator dot hijau jika sudah absen -->
                    </div>
                </div>
                
                <!-- Hari Seterusnya -->
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors"><span class="text-body-sm">16</span></div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors"><span class="text-body-sm">17</span></div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors"><span class="text-body-sm">18</span></div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors"><span class="text-body-sm">19</span></div>
                <div class="bg-slate-100 aspect-square p-2"><span class="text-body-sm text-slate-300">20</span></div>
                <div class="bg-slate-100 aspect-square p-2"><span class="text-body-sm text-slate-300">21</span></div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors"><span class="text-body-sm">22</span></div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors"><span class="text-body-sm">23</span></div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors"><span class="text-body-sm">24</span></div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors"><span class="text-body-sm">25</span></div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors"><span class="text-body-sm">26</span></div>
                <div class="bg-slate-100 aspect-square p-2"><span class="text-body-sm text-slate-300">27</span></div>
                <div class="bg-slate-100 aspect-square p-2"><span class="text-body-sm text-slate-300">28</span></div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors"><span class="text-body-sm">29</span></div>
                <div class="bg-white aspect-square p-2 group hover:bg-slate-50 transition-colors"><span class="text-body-sm">30</span></div>
                
                <!-- Padding grid sisa -->
                <div class="bg-slate-100 aspect-square p-2 opacity-30"><span class="text-body-sm text-slate-300">1</span></div>
                <div class="bg-slate-100 aspect-square p-2 opacity-30"><span class="text-body-sm text-slate-300">2</span></div>
                <div class="bg-slate-100 aspect-square p-2 opacity-30"><span class="text-body-sm text-slate-300">3</span></div>
                <div class="bg-slate-100 aspect-square p-2 opacity-30"><span class="text-body-sm text-slate-300">4</span></div>
                <div class="bg-slate-100 aspect-square p-2 opacity-30"><span class="text-body-sm text-slate-300">5</span></div>
            </div>
            
            <div class="mt-6 flex flex-wrap gap-4 items-center justify-center text-[12px]">
                <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-tertiary-container"></span><span class="text-outline text-slate-600 font-semibold">Hadir (WFO/WFH)</span></div>
                <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-primary"></span><span class="text-outline text-slate-600 font-semibold">Izin/Cuti</span></div>
                <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-secondary"></span><span class="text-outline text-slate-600 font-semibold">Sakit</span></div>
                <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-error"></span><span class="text-outline text-slate-600 font-semibold">Alpha</span></div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: Attendance History Table -->
<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden flex flex-col">
    <div class="p-6 border-b border-outline-variant flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h4 class="font-title-sm text-title-sm font-semibold text-lg">Riwayat Absensi Terakhir</h4>
            <p class="text-body-sm text-on-surface-variant text-sm mt-1">Menampilkan 5 rekaman terakhir aktivitas Anda</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2 text-body-sm font-bold border border-outline-variant rounded-lg hover:bg-slate-100 transition-all flex items-center gap-2 cursor-pointer">
                <span class="material-symbols-outlined text-sm">filter_list</span>
                <span>Filter</span>
            </button>
            <button class="px-4 py-2 text-body-sm font-bold text-primary border border-primary/20 bg-primary/5 rounded-lg hover:bg-primary/10 transition-all cursor-pointer">
                <span>Lihat Semua Riwayat</span>
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 font-semibold text-slate-500 uppercase tracking-wider text-xs border-b border-slate-200">Tanggal</th>
                    <th class="px-6 py-4 font-semibold text-slate-500 uppercase tracking-wider text-xs border-b border-slate-200">Jam Masuk</th>
                    <th class="px-6 py-4 font-semibold text-slate-500 uppercase tracking-wider text-xs border-b border-slate-200">Jam Keluar</th>
                    <th class="px-6 py-4 font-semibold text-slate-500 uppercase tracking-wider text-xs border-b border-slate-200">Status</th>
                    <th class="px-6 py-4 font-semibold text-slate-500 uppercase tracking-wider text-xs border-b border-slate-200">Lokasi / Catatan</th>
                    <th class="px-6 py-4 font-semibold text-slate-500 uppercase tracking-wider text-xs border-b border-slate-200 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm font-medium" id="attendance-history-table">
                <!-- Data absen hari ini secara dinamis dimasukkan ke sini -->
                <tr class="hover:bg-primary/5 transition-colors group hidden" id="today-row-real">
                </tr>
                
                <!-- Riwayat sebelumnya -->
                <tr class="hover:bg-primary/5 transition-colors group">
                    <td class="px-6 py-4 text-slate-800">
                        <div class="flex flex-col">
                            <span class="font-bold">12 Jun 2026</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-medium text-tertiary-container">07:55:10</td>
                    <td class="px-6 py-4 font-medium text-tertiary-container">17:05:44</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-[12px] font-bold">Hadir - WFO</span>
                    </td>
                    <td class="px-6 py-4 text-slate-600">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-primary">location_on</span>
                            <span>Kantor Pusat, Jakarta</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button class="w-8 h-8 rounded-lg hover:bg-white hover:shadow-md transition-all text-slate-400 hover:text-primary active:scale-90 cursor-pointer" onclick="alert('Detail absensi 12 Juni 2026: Waktu masuk: 07:55:10, Waktu keluar: 17:05:44. Lokasi: Kantor Pusat, Jakarta.')">
                            <span class="material-symbols-outlined text-[20px]">info</span>
                        </button>
                    </td>
                </tr>
                <tr class="hover:bg-primary/5 transition-colors group">
                    <td class="px-6 py-4 text-slate-800">
                        <div class="flex flex-col">
                            <span class="font-bold">11 Jun 2026</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-medium text-tertiary-container">08:02:15</td>
                    <td class="px-6 py-4 font-medium text-tertiary-container">17:01:03</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-[12px] font-bold">Hadir - WFH</span>
                    </td>
                    <td class="px-6 py-4 text-slate-600">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-primary">home_work</span>
                            <span>Rumah (Remote)</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button class="w-8 h-8 rounded-lg hover:bg-white hover:shadow-md transition-all text-slate-400 hover:text-primary active:scale-90 cursor-pointer" onclick="alert('Detail absensi 11 Juni 2026: Waktu masuk: 08:02:15, Waktu keluar: 17:01:03. Lokasi: Rumah (Remote).')">
                            <span class="material-symbols-outlined text-[20px]">info</span>
                        </button>
                    </td>
                </tr>
                <tr class="hover:bg-primary/5 transition-colors group">
                    <td class="px-6 py-4 text-slate-800">10 Jun 2026</td>
                    <td class="px-6 py-4 font-medium text-tertiary-container">08:05:55</td>
                    <td class="px-6 py-4 font-medium text-tertiary-container">17:02:12</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-[12px] font-bold">Hadir - WFO</span>
                    </td>
                    <td class="px-6 py-4 text-slate-400 italic">N/A</td>
                    <td class="px-6 py-4 text-center">
                        <button class="w-8 h-8 rounded-lg hover:bg-white hover:shadow-md transition-all text-slate-400 hover:text-primary active:scale-90 cursor-pointer" onclick="alert('Detail absensi 10 Juni 2026: Waktu masuk: 08:05:55, Waktu keluar: 17:02:12. Lokasi: N/A.')">
                            <span class="material-symbols-outlined text-[20px]">info</span>
                        </button>
                    </td>
                </tr>
                <tr class="hover:bg-primary/5 transition-colors group">
                    <td class="px-6 py-4 text-slate-800">09 Jun 2026</td>
                    <td class="px-6 py-4 font-medium text-tertiary-container">08:12:44</td>
                    <td class="px-6 py-4 font-medium text-tertiary-container">17:00:01</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-[12px] font-bold">Hadir - WFO</span>
                    </td>
                    <td class="px-6 py-4 text-slate-400 italic">N/A</td>
                    <td class="px-6 py-4 text-center">
                        <button class="w-8 h-8 rounded-lg hover:bg-white hover:shadow-md transition-all text-slate-400 hover:text-primary active:scale-90 cursor-pointer" onclick="alert('Detail absensi 09 Juni 2026: Waktu masuk: 08:12:44, Waktu keluar: 17:00:01. Lokasi: N/A.')">
                            <span class="material-symbols-outlined text-[20px]">info</span>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnClockIn = document.getElementById('btn-clock-in');
        const btnClockOut = document.getElementById('btn-clock-out');
        const clockInTimeEl = document.getElementById('clock-in-time');
        const clockOutTimeEl = document.getElementById('clock-out-time');
        const statusBox = document.getElementById('status-box');
        const statusIcon = document.getElementById('status-icon');
        const statusIconContainer = document.getElementById('status-icon-container');
        const statusText = document.getElementById('status-text');
        const statusDesc = document.getElementById('status-desc');
        const statusIconBadge = document.getElementById('status-icon-badge');
        
        const summaryHadir = document.getElementById('summary-hadir');
        const summaryHadirProgress = document.getElementById('summary-hadir-progress');
        const calendarTodayIndicator = document.getElementById('calendar-today-indicator');
        const todayRowReal = document.getElementById('today-row-real');
        
        let checkedInTime = null;
        
        // Fungsi pembantu untuk memformat waktu 2 digit
        const pad = (num) => String(num).padStart(2, '0');
        
        // 1. Logika Clock In
        btnClockIn.addEventListener('click', () => {
            const now = new Date();
            const timeStr = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
            const timeShortStr = `${pad(now.getHours())}:${pad(now.getMinutes())}`;
            
            checkedInTime = timeStr;
            
            // Perbarui Visual Jam Masuk
            clockInTimeEl.innerText = timeShortStr;
            clockInTimeEl.classList.add('text-tertiary-container');
            
            // Perbarui Box Status Kehadiran
            statusBox.className = 'flex items-center gap-4 p-4 bg-green-50 rounded-xl border border-green-200 transition-all duration-300';
            statusIconContainer.className = 'w-12 h-12 rounded-lg bg-green-600 flex items-center justify-center text-white transition-all duration-300';
            statusIcon.innerText = 'check_circle';
            statusText.className = 'text-body-sm text-green-700 font-bold leading-tight transition-all duration-300';
            statusText.innerText = 'Sudah Absen Masuk';
            statusDesc.innerText = `${timeShortStr} WIB • Kantor Pusat`;
            
            statusIconBadge.className = 'material-symbols-outlined text-green-600';
            
            // Disable Clock In, Enable Clock Out
            btnClockIn.classList.add('opacity-50', 'cursor-not-allowed');
            btnClockIn.disabled = true;
            btnClockIn.classList.remove('hover-card-float');
            
            btnClockOut.classList.remove('opacity-50', 'cursor-not-allowed');
            btnClockOut.disabled = false;
            btnClockOut.classList.add('hover-card-float');
            
            // Update widget ringkasan bulan ini
            const currentHadir = parseInt(summaryHadir.innerText);
            const newHadir = currentHadir + 1;
            summaryHadir.innerText = pad(newHadir);
            summaryHadirProgress.style.width = `${(newHadir / 20) * 100}%`;
            
            // Tambahkan dot hijau di kalender hari ini
            calendarTodayIndicator.innerHTML = '<div class="w-2.5 h-2.5 rounded-full bg-tertiary-container animate-ping absolute"></div><div class="w-2.5 h-2.5 rounded-full bg-tertiary-container relative"></div>';
            
            // Tambahkan data baris ke tabel riwayat absensi teratas
            const today = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            
            todayRowReal.innerHTML = `
                <td class="px-6 py-4 text-slate-800">
                    <div class="flex flex-col">
                        <span class="font-bold text-primary">${today}</span>
                        <span class="text-[11px] text-primary uppercase font-bold">Hari Ini</span>
                    </div>
                </td>
                <td class="px-6 py-4 font-bold text-tertiary-container" id="table-row-in-time">${timeStr}</td>
                <td class="px-6 py-4 text-slate-400 font-bold" id="table-row-out-time">--:--:--</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-[12px] font-bold" id="table-row-badge">Hadir - WFO</span>
                </td>
                <td class="px-6 py-4 text-slate-600">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-primary">location_on</span>
                        <span>Kantor Pusat, Jakarta</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <button class="w-8 h-8 rounded-lg hover:bg-white hover:shadow-md transition-all text-slate-400 hover:text-primary active:scale-90 cursor-pointer" id="btn-info-today">
                        <span class="material-symbols-outlined text-[20px]">info</span>
                    </button>
                </td>
            `;
            
            todayRowReal.classList.remove('hidden');
            
            document.getElementById('btn-info-today').addEventListener('click', () => {
                const outTime = document.getElementById('table-row-out-time').innerText;
                alert(`Detail absensi Hari Ini: Waktu masuk: ${timeStr}, Waktu keluar: ${outTime}. Lokasi: Kantor Pusat, Jakarta.`);
            });
            
            alert('Absen Masuk (Clock In) berhasil dicatat!');
        });
        
        // 2. Logika Clock Out
        btnClockOut.addEventListener('click', () => {
            const now = new Date();
            const timeStr = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
            const timeShortStr = `${pad(now.getHours())}:${pad(now.getMinutes())}`;
            
            // Perbarui Visual Jam Keluar
            clockOutTimeEl.innerText = timeShortStr;
            clockOutTimeEl.classList.add('text-primary');
            
            // Perbarui Box Status Kehadiran
            statusBox.className = 'flex items-center gap-4 p-4 bg-primary/5 rounded-xl border border-primary/20 transition-all duration-300';
            statusIconContainer.className = 'w-12 h-12 rounded-lg bg-primary flex items-center justify-center text-white transition-all duration-300';
            statusIcon.innerText = 'check_circle';
            statusText.className = 'text-body-sm text-primary font-bold leading-tight transition-all duration-300';
            statusText.innerText = 'Sudah Absen Pulang';
            statusDesc.innerText = `Keluar pukul ${timeShortStr} WIB`;
            
            statusIconBadge.className = 'material-symbols-outlined text-primary';
            
            // Disable Clock Out
            btnClockOut.classList.add('opacity-50', 'cursor-not-allowed');
            btnClockOut.disabled = true;
            btnClockOut.classList.remove('hover-card-float');
            
            // Update Jam Keluar di tabel riwayat
            document.getElementById('table-row-out-time').innerText = timeStr;
            document.getElementById('table-row-out-time').className = 'px-6 py-4 font-bold text-primary';
            document.getElementById('table-row-badge').innerText = 'Selesai Kerja';
            document.getElementById('table-row-badge').className = 'px-3 py-1 bg-primary/5 text-primary border border-primary/20 rounded-full text-[12px] font-bold';
            
            alert('Absen Keluar (Clock Out) berhasil dicatat! Selamat beristirahat.');
        });
    });
</script>
@endpush

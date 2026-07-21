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
            <img class="w-full h-full object-cover" alt="Foto Profil" src="{{ session('user_photo') ?: asset('images/avatar.svg') }}">
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
            @if(!$todayAttendance || !$todayAttendance->jam_masuk)
            <div class="flex items-center gap-4 p-4 bg-slate-100 rounded-xl border border-slate-200 transition-all duration-300" id="status-box">
                <div class="w-12 h-12 rounded-lg bg-slate-400 flex items-center justify-center text-white transition-all duration-300" id="status-icon-container">
                    <span class="material-symbols-outlined text-2xl" id="status-icon">pending_actions</span>
                </div>
                <div>
                    <p class="text-body-sm text-slate-700 font-bold leading-tight transition-all duration-300" id="status-text">Belum Absen Masuk</p>
                    <p class="text-[12px] text-on-surface-variant" id="status-desc">Silakan lakukan absensi hari ini</p>
                </div>
            </div>
            @elseif(!$todayAttendance->jam_keluar)
            <div class="flex items-center gap-4 p-4 bg-green-50 rounded-xl border border-green-200 transition-all duration-300" id="status-box">
                <div class="w-12 h-12 rounded-lg bg-green-600 flex items-center justify-center text-white transition-all duration-300" id="status-icon-container">
                    <span class="material-symbols-outlined text-2xl" id="status-icon">check_circle</span>
                </div>
                <div>
                    <p class="text-body-sm text-green-700 font-bold leading-tight transition-all duration-300" id="status-text">Sudah Absen Masuk</p>
                    <p class="text-[12px] text-on-surface-variant" id="status-desc">{{ substr($todayAttendance->jam_masuk, 0, 5) }} WIB • {{ $todayAttendance->status_kerja }}</p>
                </div>
            </div>
            @else
            <div class="flex items-center gap-4 p-4 bg-primary/5 rounded-xl border border-primary/20 transition-all duration-300" id="status-box">
                <div class="w-12 h-12 rounded-lg bg-primary flex items-center justify-center text-white transition-all duration-300" id="status-icon-container">
                    <span class="material-symbols-outlined text-2xl" id="status-icon">check_circle</span>
                </div>
                <div>
                    <p class="text-body-sm text-primary font-bold leading-tight transition-all duration-300" id="status-text">Sudah Absen Pulang</p>
                    <p class="text-[12px] text-on-surface-variant" id="status-desc">Keluar pukul {{ substr($todayAttendance->jam_keluar, 0, 5) }} WIB</p>
                </div>
            </div>
            @endif
            <div class="grid grid-cols-2 gap-3 text-center">
                <div class="p-3 bg-surface-container-low rounded-lg border border-outline-variant/50">
                    <p class="text-[10px] uppercase tracking-wider text-outline mb-1 font-bold">Jam Masuk</p>
                    <p class="font-bold {{ $todayAttendance && $todayAttendance->jam_masuk ? 'text-tertiary-container' : 'text-on-surface' }} text-lg transition-all duration-300" id="clock-in-time">
                        {{ $todayAttendance && $todayAttendance->jam_masuk ? substr($todayAttendance->jam_masuk, 0, 5) : '--:--' }}
                    </p>
                </div>
                <div class="p-3 bg-surface-container-low rounded-lg border border-outline-variant/50">
                    <p class="text-[10px] uppercase tracking-wider text-outline mb-1 font-bold">Jam Keluar</p>
                    <p class="font-bold {{ $todayAttendance && $todayAttendance->jam_keluar ? 'text-primary' : 'text-on-surface' }} text-lg transition-all duration-300" id="clock-out-time">
                        {{ $todayAttendance && $todayAttendance->jam_keluar ? substr($todayAttendance->jam_keluar, 0, 5) : '--:--' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons Section (Bento Float) -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @if(!$todayAttendance || !$todayAttendance->jam_masuk)
        <!-- Belum Clock In -> Tampilkan Clock In -->
        <a href="{{ route('attendance.index') }}" class="hover-card-float bg-primary shadow-primary/20 hover:bg-primary/90 cursor-pointer group flex items-center justify-center gap-4 py-6 text-white rounded-xl font-bold text-lg shadow-lg transition-all active:scale-[0.98]" id="btn-clock-in">
            <span class="material-symbols-outlined text-3xl group-hover:rotate-12 transition-transform">schedule</span>
            <span>Clock In Sekarang</span>
        </a>
    @elseif($todayAttendance && $todayAttendance->jam_masuk && !$todayAttendance->jam_keluar)
        <!-- Sudah Clock In, Belum Clock Out -> Tampilkan Clock Out -->
        <a href="{{ route('attendance.index') }}" onclick="return confirmEarlyClockOut(event)" class="hover-card-float bg-[#1e293b] shadow-slate-800/20 hover:bg-slate-800 cursor-pointer group flex items-center justify-center gap-4 py-6 text-white rounded-xl font-bold text-lg shadow-lg transition-all active:scale-[0.98]" id="btn-clock-out">
            <span class="material-symbols-outlined text-3xl group-hover:-rotate-12 transition-transform">logout</span>
            <span>Clock Out Sekarang</span>
        </a>
    @else
        <!-- Sudah Clock In dan Clock Out -> Selesai -->
        <div class="opacity-50 bg-slate-400 group flex items-center justify-center gap-4 py-6 text-white rounded-xl font-bold text-lg shadow-lg" style="pointer-events: none;">
            <span class="material-symbols-outlined text-3xl">check_circle</span>
            <span>Absensi Hari Ini Selesai</span>
        </div>
    @endif

    <button class="hover-card-float group flex items-center justify-center gap-4 py-6 rounded-xl font-bold text-lg shadow-lg shadow-amber-500/20 transition-all active:scale-[0.98] cursor-pointer" style="background-color: #f59e0b; color: #ffffff;" id="btn-request-leave">
        <span class="material-symbols-outlined text-3xl group-hover:scale-110 transition-transform">event_note</span>
        <span>Ajukan Cuti / Izin</span>
    </button>
</div>

<!-- Inline Request Leave Form -->
<div id="form-request-leave" class="hidden mt-6 bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden transition-all duration-300 transform origin-top">
    <div class="px-6 py-4 border-b border-outline-variant flex items-center justify-between bg-slate-50">
        <h3 class="font-title-sm text-lg font-bold text-slate-800 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500">event_note</span>
            Formulir Pengajuan Cuti / Izin
        </h3>
        <button type="button" class="text-slate-400 hover:text-slate-600 cursor-pointer" onclick="document.getElementById('form-request-leave').classList.add('hidden')">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <form action="{{ route('attendance.leave') }}" method="POST" class="p-6">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-body-sm font-bold text-slate-700 mb-1">Tipe Pengajuan</label>
                    <select name="tipe" required class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary bg-white">
                        <option value="">Pilih Tipe...</option>
                        <option value="cuti">Cuti</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-body-sm font-bold text-slate-700 mb-1">Tgl Mulai</label>
                        <input type="date" name="tanggal_mulai" required class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div id="container-tanggal-selesai">
                        <label class="block text-body-sm font-bold text-slate-700 mb-1">Tgl Selesai</label>
                        <input type="date" name="tanggal_selesai" required class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                </div>
            </div>
            <div class="lg:col-span-2 flex flex-col justify-between h-full">
                <div>
                    <label class="block text-body-sm font-bold text-slate-700 mb-1">Keterangan / Alasan</label>
                    <textarea name="keterangan" rows="4" required class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" placeholder="Tuliskan keterangan secara singkat..."></textarea>
                </div>
                <div class="mt-4 flex items-center justify-end gap-3">
                    <button type="button" class="px-6 py-2 text-sm font-bold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer" onclick="document.getElementById('form-request-leave').classList.add('hidden')">
                        Tutup
                    </button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold bg-amber-500 text-white hover:bg-amber-600 rounded-lg shadow-md transition-colors cursor-pointer">
                        Kirim Pengajuan
                    </button>
                </div>
            </div>
        </div>
    </form>
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
                    <span class="text-3xl font-bold text-tertiary-container" id="summary-hadir">{{ sprintf('%02d', $hadirCount) }}</span>
                    <span class="text-body-sm text-outline text-xs">Hari</span>
                </div>
                <div class="mt-3 w-full bg-slate-100 rounded-full h-1.5">
                    <div class="bg-tertiary-container h-1.5 rounded-full transition-all duration-500" style="width: {{ min(($hadirCount / 20) * 100, 100) }}%" id="summary-hadir-progress"></div>
                </div>
            </div>
            <div class="hover-card-float p-4 bg-white rounded-xl border border-outline-variant shadow-sm hover:border-primary/30 transition-colors">
                <p class="text-label-uppercase text-outline mb-2 text-xs font-bold tracking-wider">IZIN</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-primary" id="summary-izin">{{ sprintf('%02d', $izinCount) }}</span>
                    <span class="text-body-sm text-outline text-xs">Hari</span>
                </div>
                <div class="mt-3 w-full bg-slate-100 rounded-full h-1.5">
                    <div class="bg-primary h-1.5 rounded-full transition-all duration-500" style="width: {{ min(($izinCount / 20) * 100, 100) }}%" id="summary-izin-progress"></div>
                </div>
            </div>
            <div class="hover-card-float p-4 bg-white rounded-xl border border-outline-variant shadow-sm hover:border-primary/30 transition-colors">
                <p class="text-label-uppercase text-outline mb-2 text-xs font-bold tracking-wider">SAKIT</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-secondary" id="summary-sakit">{{ sprintf('%02d', $sakitCount) }}</span>
                    <span class="text-body-sm text-outline text-xs">Hari</span>
                </div>
                <div class="mt-3 w-full bg-slate-100 rounded-full h-1.5">
                    <div class="bg-secondary h-1.5 rounded-full transition-all duration-500" style="width: {{ min(($sakitCount / 20) * 100, 100) }}%" id="summary-sakit-progress"></div>
                </div>
            </div>
            <div class="hover-card-float p-4 bg-white rounded-xl border border-outline-variant shadow-sm hover:border-primary/30 transition-colors">
                <p class="text-label-uppercase text-outline mb-2 text-xs font-bold tracking-wider">ALPHA</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-error">{{ sprintf('%02d', $alphaCount) }}</span>
                    <span class="text-body-sm text-outline text-xs">Hari</span>
                </div>
                <div class="mt-3 w-full bg-slate-100 rounded-full h-1.5">
                    <div class="bg-error h-1.5 rounded-full" style="width: {{ min(($alphaCount / 20) * 100, 100) }}%"></div>
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
                @forelse($historyAttendances as $att)
                <tr class="hover:bg-primary/5 transition-colors group">
                    <td class="px-6 py-4 text-slate-800">
                        <div class="flex flex-col">
                            <span class="font-bold">{{ \Carbon\Carbon::parse($att->tanggal)->format('d M Y') }}</span>
                            @if($att->tanggal === \Carbon\Carbon::today()->toDateString())
                                <span class="text-[11px] text-primary uppercase font-bold">Hari Ini</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 font-medium text-tertiary-container">{{ $att->jam_masuk ?: '--:--:--' }}</td>
                    <td class="px-6 py-4 font-medium {{ $att->jam_keluar ? 'text-tertiary-container' : 'text-slate-400' }}">{{ $att->jam_keluar ?: '--:--:--' }}</td>
                    <td class="px-6 py-4">
                        @if($att->status_kehadiran === 'hadir')
                            <span class="px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-[12px] font-bold">Hadir - {{ $att->status_kerja }}</span>
                        @elseif($att->status_kehadiran === 'izin')
                            <span class="px-3 py-1 bg-primary/10 text-primary border border-primary/20 rounded-full text-[12px] font-bold">Izin</span>
                        @else
                            <span class="px-3 py-1 bg-slate-100 text-slate-700 border border-slate-200 rounded-full text-[12px] font-bold">{{ ucfirst($att->status_kehadiran) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-600">
                        @if($att->lokasi_masuk)
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-primary">location_on</span>
                                <span class="truncate max-w-[200px]">{{ $att->lokasi_masuk }}</span>
                            </div>
                        @else
                            <span class="text-slate-400 italic">N/A</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button class="w-8 h-8 rounded-lg hover:bg-white hover:shadow-md transition-all text-slate-400 hover:text-primary active:scale-90 cursor-pointer" onclick="alert('Detail absensi {{ $att->tanggal }}: Masuk: {{ $att->jam_masuk }}, Keluar: {{ $att->jam_keluar }}. Lokasi: {{ addslashes($att->lokasi_masuk) }}')">
                            <span class="material-symbols-outlined text-[20px]">info</span>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada riwayat absensi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmEarlyClockOut(event) {
        const currentHour = new Date().getHours();
        // Asumsi jam pulang normal adalah jam 17:00 (5 PM)
        if (currentHour < 17) {
            const confirmed = confirm("Peringatan: Saat ini belum waktunya pulang kerja (17:00). Anda yakin ingin melakukan Clock Out lebih awal?");
            if (!confirmed) {
                event.preventDefault(); // Batalkan perpindahan halaman jika pengguna membatalkan
                return false;
            }
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const btnRequestLeave = document.getElementById('btn-request-leave');
        const formRequestLeave = document.getElementById('form-request-leave');
        if (btnRequestLeave && formRequestLeave) {
            btnRequestLeave.addEventListener('click', () => {
                formRequestLeave.classList.toggle('hidden');
            });
        }

        const tipeSelect = document.querySelector('select[name="tipe"]');
        const containerTanggalSelesai = document.getElementById('container-tanggal-selesai');
        const inputTanggalSelesai = document.querySelector('input[name="tanggal_selesai"]');
        const containerMulai = document.querySelector('input[name="tanggal_mulai"]').parentElement;

        if (tipeSelect && containerTanggalSelesai && inputTanggalSelesai) {
            tipeSelect.addEventListener('change', (e) => {
                if (e.target.value === 'sakit') {
                    containerTanggalSelesai.classList.add('hidden');
                    inputTanggalSelesai.removeAttribute('required');
                    inputTanggalSelesai.value = ''; // Reset the value
                    containerMulai.classList.add('col-span-2'); // Make start date span full width
                } else {
                    containerTanggalSelesai.classList.remove('hidden');
                    inputTanggalSelesai.setAttribute('required', 'required');
                    containerMulai.classList.remove('col-span-2');
                }
            });
        }
    });
</script>
@endpush

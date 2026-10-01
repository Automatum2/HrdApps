@extends('layouts.admin')

@section('title', 'Dashboard Absensi - HRDApps')
@section('page_title', 'Dashboard Absensi')

@section('content')
<!-- Top Section: Welcome Banner & Status -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
    <!-- Welcome Banner -->
    <div class="lg:col-span-2 relative overflow-hidden rounded-2xl bg-[#1e293b] text-white p-5 sm:p-8 flex flex-col md:flex-row items-center justify-between shadow-lg border border-slate-700">
        <div class="relative z-10 space-y-2 text-center md:text-left">
            <p class="text-xs sm:text-sm text-slate-300 font-medium">Selamat Datang,</p>
            <h3 class="text-xl sm:text-3xl font-extrabold tracking-tight">{{ auth()->check() && auth()->user()->employee ? auth()->user()->employee->nama_lengkap : session('user_name', 'Ahmad Fadillah') }}</h3>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-3 justify-center md:justify-start text-xs">
                <span class="px-2.5 py-1 bg-primary text-white rounded-full text-[11px] font-bold tracking-wide uppercase">Tetap Produktif!</span>
                <span class="text-slate-300 font-mono">ID: {{ auth()->check() && auth()->user()->employee ? auth()->user()->employee->nik : session('employee_id', '00001221') }}</span>
                @if(isset($masaKerja))
                <span class="text-slate-300 hidden sm:inline">&bull; Masa Kerja: <strong class="text-white">{{ $masaKerja }}</strong></span>
                @endif
                @if(isset($sisaCutiTahunan))
                <span class="text-slate-300">&bull; Sisa Cuti: <strong class="text-amber-400 font-bold">{{ $sisaCutiTahunan }} Hari</strong></span>
                @endif
            </div>
        </div>
        <div class="mt-4 md:mt-0 relative z-10 w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-primary/20 shadow-xl overflow-hidden bg-slate-700 shrink-0">
            @php
                $user = \Illuminate\Support\Facades\Auth::user();
                $dashboardPhoto = asset('images/avatar.svg');
                if ($user && $user->employee && $user->employee->foto) {
                    $dashboardPhoto = asset('storage/' . $user->employee->foto);
                }
            @endphp
            <img class="w-full h-full object-cover" alt="Foto Profil" src="{{ $dashboardPhoto }}">
        </div>
        <!-- Background Decoration -->
        <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-48 h-48 bg-primary-container/5 rounded-full blur-2xl pointer-events-none"></div>
    </div>
    
    <!-- Clock Status Card -->
    <div class="bg-white rounded-2xl border border-outline-variant p-5 sm:p-6 flex flex-col justify-between shadow-sm" id="absensi-status-card">
        <div class="flex items-center justify-between mb-3">
            <h4 class="font-title-sm text-title-sm text-on-surface font-bold text-base sm:text-lg">Status Presensi</h4>
            <span class="material-symbols-outlined text-outline" id="status-icon-badge">verified</span>
        </div>
        <div class="space-y-3 sm:space-y-4">
            <!-- Box status -->
            @if($todayAttendance && in_array($todayAttendance->status_kehadiran, ['izin', 'cuti', 'sakit']))
            <div class="flex items-center gap-3 p-3.5 bg-emerald-50 rounded-xl border border-emerald-200 transition-all duration-300" id="status-box">
                <div class="w-10 h-10 rounded-lg bg-emerald-600 flex items-center justify-center text-white shrink-0" id="status-icon-container">
                    <span class="material-symbols-outlined text-xl" id="status-icon">event_available</span>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-emerald-800 leading-tight uppercase truncate" id="status-text">Status: {{ $todayAttendance->status_kehadiran }} (DISETUJUI)</p>
                    <p class="text-[11px] text-slate-600 truncate mt-0.5" id="status-desc">{!! strip_tags($todayAttendance->keterangan ?? 'Tidak ada keterangan') !!}</p>
                </div>
            </div>
            @elseif(!$todayAttendance || !$todayAttendance->jam_masuk)
            <div class="flex items-center gap-3 p-3.5 bg-slate-100 rounded-xl border border-slate-200 transition-all duration-300" id="status-box">
                <div class="w-10 h-10 rounded-lg bg-slate-400 flex items-center justify-center text-white shrink-0" id="status-icon-container">
                    <span class="material-symbols-outlined text-xl" id="status-icon">pending_actions</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-700 leading-tight" id="status-text">Belum Absen Masuk</p>
                    <p class="text-[11px] text-slate-500 mt-0.5" id="status-desc">Silakan lakukan presensi hari ini</p>
                </div>
            </div>
            @elseif(!$todayAttendance->jam_keluar)
            <div class="flex items-center gap-3 p-3.5 bg-green-50 rounded-xl border border-green-200 transition-all duration-300" id="status-box">
                <div class="w-10 h-10 rounded-lg bg-green-600 flex items-center justify-center text-white shrink-0" id="status-icon-container">
                    <span class="material-symbols-outlined text-xl" id="status-icon">check_circle</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-green-700 leading-tight" id="status-text">Sudah Absen Masuk</p>
                    <p class="text-[11px] text-slate-600 mt-0.5 font-mono">{{ substr($todayAttendance->jam_masuk, 0, 5) }} WIB &bull; {{ $todayAttendance->status_kerja }}</p>
                </div>
            </div>
            @else
            <div class="flex items-center gap-3 p-3.5 bg-primary/5 rounded-xl border border-primary/20 transition-all duration-300" id="status-box">
                <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center text-white shrink-0" id="status-icon-container">
                    <span class="material-symbols-outlined text-xl" id="status-icon">check_circle</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-primary leading-tight" id="status-text">Presensi Selesai</p>
                    <p class="text-[11px] text-slate-600 mt-0.5 font-mono">Keluar pukul {{ substr($todayAttendance->jam_keluar, 0, 5) }} WIB</p>
                </div>
            </div>
            @endif
            <div class="grid grid-cols-2 gap-2.5 text-center">
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Jam Masuk</p>
                    <p class="font-bold {{ $todayAttendance && $todayAttendance->jam_masuk ? 'text-primary' : 'text-slate-700' }} text-base font-mono mt-0.5" id="clock-in-time">
                        {{ $todayAttendance && $todayAttendance->jam_masuk ? substr($todayAttendance->jam_masuk, 0, 5) : '--:--' }}
                    </p>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Jam Keluar</p>
                    <p class="font-bold {{ $todayAttendance && $todayAttendance->jam_keluar ? 'text-primary' : 'text-slate-700' }} text-base font-mono mt-0.5" id="clock-out-time">
                        {{ $todayAttendance && $todayAttendance->jam_keluar ? substr($todayAttendance->jam_keluar, 0, 5) : '--:--' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons Section -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-6 mt-4 sm:mt-6">
    @if($todayAttendance && in_array($todayAttendance->status_kehadiran, ['izin', 'cuti', 'sakit']))
        <!-- Sedang Cuti/Izin/Sakit -->
        <div class="opacity-80 bg-primary/20 group flex items-center justify-center gap-4 py-6 text-primary rounded-xl font-bold text-lg shadow-lg border border-primary/30" style="pointer-events: none;">
            <span class="material-symbols-outlined text-3xl">event_available</span>
            <span class="uppercase">Status Hari Ini: {{ $todayAttendance->status_kehadiran }}</span>
        </div>
    @elseif(!$todayAttendance || !$todayAttendance->jam_masuk)
        <!-- Belum Clock In -> Tampilkan Update Perencanaan Harian -->
        <a href="{{ route('attendance.index') }}" class="hover-card-float bg-primary shadow-primary/20 hover:bg-primary/90 cursor-pointer group flex items-center justify-center gap-4 py-6 text-white rounded-xl font-bold text-lg shadow-lg transition-all active:scale-[0.98]" id="btn-clock-in">
            <span class="material-symbols-outlined text-3xl group-hover:rotate-12 transition-transform">schedule</span>
            <span>Update Perencanaan Harian</span>
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
        <span class="material-symbols-outlined text-3xl group-hover:rotate-12 transition-transform">event_busy</span>
        <span>Lapor Ketidakhadiran</span>
    </button>
</div>

<!-- Inline Request Leave Form -->
<div id="form-request-leave" class="hidden mt-6 bg-white rounded-2xl border border-outline-variant shadow-sm overflow-hidden transition-all duration-300 transform origin-top">
    <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between bg-slate-50">
        <h3 class="font-bold text-sm sm:text-base text-slate-800 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-lg sm:text-xl">edit_document</span>
            Lapor Ketidakhadiran (Cuti/Izin/Sakit)
        </h3>
        <button type="button" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1" onclick="document.getElementById('form-request-leave').classList.add('hidden')">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <form action="{{ route('attendance.leave') }}" method="POST" class="p-4 sm:p-6" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tipe Pengajuan <span class="text-error">*</span></label>
                    <select name="tipe" id="karyawan-leave-tipe" required class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 bg-white" onchange="handleLeaveTypeChange(this.value)">
                        <option value="">Pilih Tipe...</option>
                        <option value="cuti">Cuti Tahunan</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit (Surat Dokter)</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tgl Mulai <span class="text-error">*</span></label>
                        <input type="date" name="tanggal_mulai" id="karyawan-tgl-mulai" required class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs sm:text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                    </div>
                    <div id="container-tanggal-selesai">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tgl Selesai <span class="text-error">*</span></label>
                        <input type="date" name="tanggal_selesai" id="karyawan-tgl-selesai" required class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs sm:text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                    </div>
                </div>
                <div id="container-dokumen">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" id="label-dokumen-pendukung">
                        Dokumen Pendukung <span class="text-slate-400 font-normal lowercase">(surat dokter/foto)</span>
                    </label>
                    <input type="file" name="dokumen_pendukung" id="input-dokumen-pendukung" accept="image/*,.pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer border border-slate-200 rounded-xl p-1 bg-slate-50">
                </div>
            </div>
            <div class="lg:col-span-2 flex flex-col justify-between h-full space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Keterangan / Alasan <span class="text-error">*</span></label>
                    <textarea name="keterangan" rows="5" required class="w-full border border-slate-300 rounded-xl p-3 text-xs sm:text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="Jelaskan alasan pengajuan izin/cuti atau keterangan istirahat dari dokter..."></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" class="px-5 py-2.5 text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer" onclick="document.getElementById('form-request-leave').classList.add('hidden')">
                        Tutup
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-xs sm:text-sm font-bold text-white rounded-xl shadow transition-all cursor-pointer active:scale-95" style="background-color: #f59e0b;">
                        Kirim Pengajuan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Middle Section: Summary & Payroll -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Monthly Summary Bento -->
    <div class="lg:col-span-2 space-y-4">
        <h4 class="font-title-sm text-title-sm font-semibold text-lg">Ringkasan Bulan Ini</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
    </div>
    
    @if(isset($lastPayroll))
    <div class="space-y-4">
        <h4 class="font-title-sm text-title-sm font-semibold text-lg opacity-0 hidden lg:block">&nbsp;</h4>
        <!-- Additional Widget: Last Payroll -->
        <div class="hover-card-float p-6 bg-white border border-outline-variant rounded-xl relative overflow-hidden group shadow-sm hover:border-primary/30 transition-colors h-[calc(100%-2rem)]">
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-2">
                    <p class="text-label-uppercase text-outline font-bold mb-1 text-xs tracking-wider">GAJI TERAKHIR ({{ $lastMonth->translatedFormat('F Y') }})</p>
                    <a href="{{ route('backoffice.penggajian') }}" class="text-primary hover:text-primary/70 transition-colors" title="Lihat Slip Gaji Lengkap">
                        <span class="material-symbols-outlined text-xl">open_in_new</span>
                    </a>
                </div>
                <h5 class="text-title-sm font-bold text-2xl text-slate-800">Rp {{ number_format($lastPayroll['gajiBersih'], 0, ',', '.') }}</h5>
                <div class="flex gap-4 mt-4 pt-4 border-t border-outline-variant/30">
                    <div>
                        <p class="text-[10px] text-outline uppercase tracking-wider font-semibold">Pendapatan</p>
                        <p class="text-sm font-bold text-green-500 mt-0.5">+Rp {{ number_format($lastPayroll['gajiKotor'], 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-outline uppercase tracking-wider font-semibold">Potongan</p>
                        <p class="text-sm font-bold text-red-500 mt-0.5">-Rp {{ number_format($lastPayroll['totalPotongan'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <span class="material-symbols-outlined absolute -right-6 -bottom-6 text-[100px] text-slate-100 group-hover:scale-110 transition-transform">payments</span>
        </div>
    </div>
    @endif
</div>

<!-- Bottom Section: Calendar -->
<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden flex flex-col mb-6">
    <div class="p-6 border-b border-outline-variant flex flex-col md:flex-row items-center justify-between bg-surface-container-low/30 gap-4">
            <div>
                <h4 class="font-title-sm text-title-sm font-semibold text-lg">Kalender Absensi</h4>
                <p class="text-body-sm text-on-surface-variant text-sm mt-1">Rekam kehadiran {{ \Carbon\Carbon::create($currentYear, $currentMonth, 1)->translatedFormat('F Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                @php
                    $prevMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
                    $prevYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
                    $nextMonth = $currentMonth == 12 ? 1 : $currentMonth + 1;
                    $nextYear = $currentMonth == 12 ? $currentYear + 1 : $currentYear;
                @endphp
                <a href="{{ route('backoffice.dashboard', ['month' => $prevMonth, 'year' => $prevYear]) }}" class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-slate-50 active:scale-95 transition-all"><span class="material-symbols-outlined">chevron_left</span></a>
                <span class="px-4 py-2 font-bold text-on-surface">{{ \Carbon\Carbon::create($currentYear, $currentMonth, 1)->translatedFormat('F Y') }}</span>
                <a href="{{ route('backoffice.dashboard', ['month' => $nextMonth, 'year' => $nextYear]) }}" class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-slate-50 active:scale-95 transition-all"><span class="material-symbols-outlined">chevron_right</span></a>
            </div>
        </div>
        <div class="p-6 flex-1">
            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 mb-4 font-bold text-xs uppercase text-center">
                <div class="text-slate-500 py-2">Sen</div>
                <div class="text-slate-500 py-2">Sel</div>
                <div class="text-slate-500 py-2">Rab</div>
                <div class="text-slate-500 py-2">Kam</div>
                <div class="text-slate-500 py-2">Jum</div>
                <div class="text-slate-500 py-2">Sab</div>
                <div class="text-error py-2">Min</div>
            </div>
            <div class="grid grid-cols-7 gap-px bg-slate-200 rounded-lg overflow-hidden border border-slate-200">
                @php
                    $today = \Carbon\Carbon::today();
                @endphp
                
                {{-- Padding empty cells for the start of the month --}}
                @for ($i = 1; $i < $firstDayOfMonth; $i++)
                    <div class="bg-slate-100 aspect-square p-2 opacity-30"><span class="text-body-sm text-slate-300"></span></div>
                @endfor
                
                {{-- Days of the month --}}
                @for ($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $currentDate = \Carbon\Carbon::create($currentYear, $currentMonth, $day);
                        $dateString = $currentDate->toDateString();
                        $isToday = $currentDate->isSameDay($today);
                        $isWeekend = $currentDate->isWeekend();
                        
                        $attendance = $monthlyAttendances->get($dateString);
                        
                        $hasTraining = false;
                        $trainingTitles = [];
                        if (isset($monthlyTrainings)) {
                            foreach ($monthlyTrainings as $t) {
                                $start = \Carbon\Carbon::parse($t->tanggal_mulai)->startOfDay();
                                $end = \Carbon\Carbon::parse($t->tanggal_selesai)->endOfDay();
                                if ($currentDate->between($start, $end)) {
                                    $hasTraining = true;
                                    $trainingTitles[] = $t->nama_training ?: "Pelatihan " . $t->skill_dipelajari;
                                }
                            }
                        }
                        
                        $bgColor = $isToday ? 'bg-primary/5 border-2 border-primary' : ($isWeekend ? 'bg-slate-100' : 'bg-white');
                        $textColor = $isToday ? 'text-primary font-bold' : ($isWeekend ? 'text-slate-400' : 'text-slate-500');
                    @endphp
                    
                    <div class="{{ $bgColor }} aspect-square p-2 group {{ !$isWeekend ? 'hover:bg-slate-50 transition-colors' : '' }} relative">
                        <span class="text-body-sm {{ $textColor }}">{{ $day }}</span>
                        
                        @if ($attendance || $hasTraining)
                            <div class="absolute bottom-2 right-2 flex gap-1 flex-wrap justify-end">
                                @if ($attendance)
                                    @if ($attendance->status_kehadiran == 'hadir')
                                        <div class="w-2.5 h-2.5 rounded-full bg-tertiary-container" title="Hadir"></div>
                                    @elseif (in_array($attendance->status_kehadiran, ['izin', 'cuti']))
                                        <div class="w-2.5 h-2.5 rounded-full bg-primary" title="{{ ucfirst($attendance->status_kehadiran) }}"></div>
                                    @elseif ($attendance->status_kehadiran == 'sakit')
                                        <div class="w-2.5 h-2.5 rounded-full bg-secondary" title="Sakit"></div>
                                    @elseif ($attendance->status_kehadiran == 'alpha')
                                        <div class="w-2.5 h-2.5 rounded-full bg-error" title="Alpha"></div>
                                    @endif
                                @endif
                                
                                @if ($hasTraining)
                                    <div class="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-sm" title="{{ implode(', ', $trainingTitles) }}"></div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endfor
                
                {{-- Padding empty cells for the end of the month --}}
                @php
                    $lastDayOfWeek = \Carbon\Carbon::create($currentYear, $currentMonth, $daysInMonth)->dayOfWeekIso;
                @endphp
                @for ($i = $lastDayOfWeek; $i < 7; $i++)
                    <div class="bg-slate-100 aspect-square p-2 opacity-30"><span class="text-body-sm text-slate-300"></span></div>
                @endfor
            </div>
            
            <div class="mt-6 flex flex-wrap gap-4 items-center justify-center text-[12px]">
                <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-tertiary-container"></span><span class="text-outline text-slate-600 font-semibold">Hadir (WFO/WFH)</span></div>
                <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-primary"></span><span class="text-outline text-slate-600 font-semibold">Izin/Cuti</span></div>
                <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-secondary"></span><span class="text-outline text-slate-600 font-semibold">Sakit</span></div>
                <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-error"></span><span class="text-outline text-slate-600 font-semibold">Alpha</span></div>
                <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span><span class="text-outline text-slate-600 font-semibold">Pelatihan/Training</span></div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: Attendance History Table -->
<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden flex flex-col">
    <div class="p-6 border-b border-outline-variant flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h4 class="font-title-sm text-title-sm font-semibold text-lg">Riwayat Absensi</h4>
            <p class="text-body-sm text-on-surface-variant text-sm mt-1">Menampilkan aktivitas absensi pada bulan {{ \Carbon\Carbon::create($currentYear, $currentMonth, 1)->translatedFormat('F Y') }}</p>
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
                        <button type="button" class="w-8 h-8 rounded-lg hover:bg-slate-100 transition-all text-slate-400 hover:text-primary active:scale-90 cursor-pointer flex items-center justify-center mx-auto" onclick="showAttDetailModal('{{ \Carbon\Carbon::parse($att->tanggal)->translatedFormat('d F Y') }}', '{{ $att->jam_masuk ? substr($att->jam_masuk, 0, 5) : '--:--' }}', '{{ $att->jam_keluar ? substr($att->jam_keluar, 0, 5) : '--:--' }}', '{{ $att->total_jam_kerja ?? '--' }}', '{{ ucfirst($att->status_kehadiran) }}', '{{ $att->status_kerja ?? 'WFO' }}', '{{ $att->foto_masuk ? asset('storage/' . $att->foto_masuk) : '' }}', '{{ $att->foto_keluar ? asset('storage/' . $att->foto_keluar) : '' }}', '{{ addslashes($att->lokasi_masuk ?? '') }}', '{{ addslashes($att->lokasi_keluar ?? '') }}', '{{ addslashes(strip_tags($att->keterangan ?? 'Tidak ada catatan.')) }}')">
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

<!-- Modal Detail Absensi Karyawan -->
<div id="modal-att-detail" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-outline-variant w-full max-w-xl overflow-hidden animate-modal-pop">
        <div class="px-6 py-4 border-b border-outline-variant flex items-center justify-between bg-slate-50">
            <div>
                <h3 class="font-bold text-slate-800 text-base" id="att-detail-date">Detail Kehadiran</h3>
                <p class="text-xs text-slate-500 mt-0.5" id="att-detail-status-badge">Status Kehadiran</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1" onclick="document.getElementById('modal-att-detail').classList.add('hidden')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
            <!-- Summary Info -->
            <div class="grid grid-cols-3 gap-3 text-center bg-slate-50 p-3 rounded-xl border border-slate-200">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400">Jam Masuk</p>
                    <p class="text-sm font-bold text-slate-800 mt-0.5" id="att-detail-in">--:--</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400">Jam Keluar</p>
                    <p class="text-sm font-bold text-slate-800 mt-0.5" id="att-detail-out">--:--</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400">Total Durasi</p>
                    <p class="text-sm font-bold text-primary mt-0.5" id="att-detail-duration">-- Jam</p>
                </div>
            </div>

            <!-- Foto & Lokasi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <p class="text-xs font-bold text-slate-700">Foto & Lokasi Masuk</p>
                    <div class="w-full h-36 bg-slate-100 rounded-lg overflow-hidden border border-slate-200 flex items-center justify-center">
                        <img id="att-detail-photo-in" src="" class="w-full h-full object-cover hidden" alt="Foto Masuk">
                        <span id="att-detail-photo-in-none" class="text-xs text-slate-400">Tidak ada foto</span>
                    </div>
                    <p class="text-[11px] text-slate-600 font-mono break-words leading-tight" id="att-detail-loc-in"></p>
                </div>
                <div class="space-y-1.5">
                    <p class="text-xs font-bold text-slate-700">Foto & Lokasi Keluar</p>
                    <div class="w-full h-36 bg-slate-100 rounded-lg overflow-hidden border border-slate-200 flex items-center justify-center">
                        <img id="att-detail-photo-out" src="" class="w-full h-full object-cover hidden" alt="Foto Keluar">
                        <span id="att-detail-photo-out-none" class="text-xs text-slate-400">Tidak ada foto</span>
                    </div>
                    <p class="text-[11px] text-slate-600 font-mono break-words leading-tight" id="att-detail-loc-out"></p>
                </div>
            </div>

            <!-- Catatan Laporan -->
            <div class="space-y-1 pt-2 border-t border-slate-100">
                <p class="text-xs font-bold text-slate-700">Laporan Singkat / Keterangan</p>
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700 max-h-28 overflow-y-auto" id="att-detail-notes">
                    Tidak ada keterangan.
                </div>
            </div>
        </div>
        <div class="px-6 py-3 border-t border-slate-100 bg-slate-50 flex justify-end">
            <button type="button" class="px-4 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-xs font-bold transition-colors cursor-pointer" onclick="document.getElementById('modal-att-detail').classList.add('hidden')">
                Tutup
            </button>
        </div>
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

        const inputTanggalMulai = document.getElementById('karyawan-tgl-mulai');
        const inputTanggalSelesai = document.getElementById('karyawan-tgl-selesai');

        if (inputTanggalMulai && inputTanggalSelesai) {
            // Set min tanggal selesai mengikuti tanggal mulai
            inputTanggalMulai.addEventListener('change', (e) => {
                inputTanggalSelesai.min = e.target.value;
                if (!inputTanggalSelesai.value || inputTanggalSelesai.value < e.target.value) {
                    inputTanggalSelesai.value = e.target.value;
                }
            });
        }
    });

    function handleLeaveTypeChange(type) {
        const labelDokumen = document.getElementById('label-dokumen-pendukung');
        const inputDokumen = document.getElementById('input-dokumen-pendukung');
        
        if (type === 'sakit') {
            if (labelDokumen) labelDokumen.innerHTML = 'Surat Keterangan Dokter <span class="text-error font-bold">*</span> <span class="text-xs text-error lowercase">(wajib jika sakit)</span>';
            if (inputDokumen) inputDokumen.required = true;
        } else {
            if (labelDokumen) labelDokumen.innerHTML = 'Dokumen Pendukung <span class="text-slate-400 font-normal lowercase">(opsional)</span>';
            if (inputDokumen) inputDokumen.required = false;
        }
    }

    function showAttDetailModal(tanggal, masuk, keluar, durasi, status, kerja, fotoIn, fotoOut, locIn, locOut, notes) {
        document.getElementById('att-detail-date').innerText = 'Presensi: ' + tanggal;
        document.getElementById('att-detail-status-badge').innerText = status + ' (' + kerja + ')';
        document.getElementById('att-detail-in').innerText = masuk;
        document.getElementById('att-detail-out').innerText = keluar;
        document.getElementById('att-detail-duration').innerText = durasi + (durasi !== '--' ? ' Jam' : '');
        document.getElementById('att-detail-notes').innerText = notes || 'Tidak ada catatan.';

        const pIn = document.getElementById('att-detail-photo-in');
        const pInNone = document.getElementById('att-detail-photo-in-none');
        if (fotoIn && fotoIn.trim() !== '') {
            pIn.src = fotoIn;
            pIn.classList.remove('hidden');
            pInNone.classList.add('hidden');
        } else {
            pIn.src = '';
            pIn.classList.add('hidden');
            pInNone.classList.remove('hidden');
        }

        const pOut = document.getElementById('att-detail-photo-out');
        const pOutNone = document.getElementById('att-detail-photo-out-none');
        if (fotoOut && fotoOut.trim() !== '') {
            pOut.src = fotoOut;
            pOut.classList.remove('hidden');
            pOutNone.classList.add('hidden');
        } else {
            pOut.src = '';
            pOut.classList.add('hidden');
            pOutNone.classList.remove('hidden');
        }

        document.getElementById('att-detail-loc-in').innerText = locIn || '-';
        document.getElementById('att-detail-loc-out').innerText = locOut || '-';

        document.getElementById('modal-att-detail').classList.remove('hidden');
    }
</script>
@endpush

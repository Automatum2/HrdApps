@extends('layouts.admin')

@section('title', 'Dashboard Manager - HRDApps')
@section('page_title', 'Dashboard Manager')

@section('content')
<!-- Summary Grid -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 animate-stagger">
    <!-- Card 1: Total Karyawan -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 sm:p-6 card-shadow hover-card-float hover:border-primary/50 transition-colors flex justify-between items-start">
        <div>
            <p class="text-on-surface-variant font-semibold text-xs sm:text-sm mb-1 uppercase tracking-wider">Total Karyawan</p>
            <h3 class="text-2xl sm:text-display-lg font-bold text-on-background" id="stat-total-karyawan">{{ $total_karyawan }}</h3>
        </div>
        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-primary-container/10 flex items-center justify-center text-primary shrink-0">
            <span class="material-symbols-outlined text-2xl sm:text-3xl" style="font-variation-settings: 'FILL' 1;">group</span>
        </div>
    </div>
    <!-- Card 2: Hadir Hari Ini -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 sm:p-6 card-shadow hover-card-float hover:border-primary/50 transition-colors flex justify-between items-start">
        <div>
            <p class="text-on-surface-variant font-semibold text-xs sm:text-sm mb-1 uppercase tracking-wider">Hadir Hari Ini</p>
            <h3 class="text-2xl sm:text-display-lg font-bold text-tertiary" id="stat-hadir-karyawan">{{ $hadir_hari_ini }}</h3>
        </div>
        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-tertiary-container/10 flex items-center justify-center text-tertiary shrink-0">
            <span class="material-symbols-outlined text-2xl sm:text-3xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
        </div>
    </div>
    <!-- Card 3: Belum Absen -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 sm:p-6 card-shadow hover-card-float hover:border-primary/50 transition-colors flex justify-between items-start">
        <div>
            <p class="text-on-surface-variant font-semibold text-xs sm:text-sm mb-1 uppercase tracking-wider">Belum Absen</p>
            <h3 class="text-2xl sm:text-display-lg font-bold text-error" id="stat-belum-absen">{{ $belum_absen }}</h3>
        </div>
        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-error-container/20 flex items-center justify-center text-error shrink-0">
            <span class="material-symbols-outlined text-2xl sm:text-3xl" style="font-variation-settings: 'FILL' 1;">pending_actions</span>
        </div>
    </div>
    <!-- Card 4: Gaji Bulan Ini -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 sm:p-6 card-shadow hover-card-float hover:border-primary/50 transition-colors flex justify-between items-start">
        <div>
            <p class="text-on-surface-variant font-semibold text-xs sm:text-sm mb-1 uppercase tracking-wider">Gaji Bulan Ini</p>
            <h3 class="text-xl sm:text-display-lg font-bold text-primary truncate max-w-[160px] sm:max-w-none" id="stat-estimasi-gaji">Rp {{ number_format($total_gaji_bulan_ini ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-primary-container/10 flex items-center justify-center text-primary shrink-0">
            <span class="material-symbols-outlined text-2xl sm:text-3xl" style="font-variation-settings: 'FILL' 1;">payments</span>
        </div>
    </div>
</section>

<!-- Widget Absensi Manager -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-4 sm:p-6 card-shadow mb-4 sm:mb-6 mt-4 sm:mt-6 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 sm:gap-6 animate-stagger relative overflow-hidden">
    <!-- Ornamen Background -->
    <div class="absolute right-0 top-0 w-64 h-full bg-gradient-to-l from-primary/5 to-transparent pointer-events-none"></div>
    
    <div class="flex items-center gap-4 sm:gap-5 z-10">
        <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl {{ $todayAttendance && in_array($todayAttendance->status_kehadiran, ['izin', 'cuti', 'sakit']) ? 'bg-green-500/10 text-green-600 border border-green-200' : 'bg-primary/10 text-primary border border-primary/20' }} flex items-center justify-center relative shadow-sm shrink-0">
            <span class="material-symbols-outlined text-3xl sm:text-4xl" style="font-variation-settings: 'FILL' 1;">{{ $todayAttendance && in_array($todayAttendance->status_kehadiran, ['izin', 'cuti', 'sakit']) ? 'event_available' : 'fingerprint' }}</span>
            @if($todayAttendance && in_array($todayAttendance->status_kehadiran, ['izin', 'cuti', 'sakit']))
                <span class="absolute top-0 right-0 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white" id="status-dot"></span>
            @elseif($todayAttendance && $todayAttendance->jam_masuk)
                <span class="absolute top-0 right-0 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white" id="status-dot"></span>
            @else
                <span class="absolute top-0 right-0 w-3.5 h-3.5 bg-error rounded-full border-2 border-white animate-pulse" id="status-dot"></span>
            @endif
        </div>
        <div class="min-w-0">
            <h4 class="font-bold text-base sm:text-lg text-on-background">Presensi Kehadiran</h4>
            <p class="text-xs text-on-surface-variant font-medium mt-0.5">Waktu Server: <span class="font-bold text-primary font-mono bg-primary/10 px-1.5 py-0.5 rounded" id="realtime-clock">--:--:-- WIB</span></p>
            @if($todayAttendance && in_array($todayAttendance->status_kehadiran, ['izin', 'cuti', 'sakit']))
                <div class="mt-1">
                    <span class="px-2.5 py-0.5 rounded-md bg-green-100 text-green-800 text-[11px] font-bold uppercase tracking-wider inline-flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px]">check_circle</span>
                        STATUS HARI INI: {{ strtoupper($todayAttendance->status_kehadiran) }} (DISETUJUI)
                    </span>
                    @if($todayAttendance->keterangan)
                        <p class="text-[11px] text-slate-500 mt-0.5 font-medium truncate max-w-xs sm:max-w-md">Keterangan: {{ $todayAttendance->keterangan }}</p>
                    @endif
                </div>
            @elseif($todayAttendance && $todayAttendance->jam_masuk && !$todayAttendance->jam_keluar)
                <p class="text-[11px] text-green-600 font-bold mt-1" id="status-text">Sudah Clock In pukul {{ substr($todayAttendance->jam_masuk, 0, 5) }} WIB ({{ $todayAttendance->status_kerja }})</p>
            @elseif($todayAttendance && $todayAttendance->jam_masuk && $todayAttendance->jam_keluar)
                <p class="text-[11px] text-primary font-bold mt-1" id="status-text">Presensi Selesai (Keluar pukul {{ substr($todayAttendance->jam_keluar, 0, 5) }} WIB)</p>
            @else
                <p class="text-[11px] text-error font-bold mt-1" id="status-text">Anda belum melakukan Clock In hari ini.</p>
            @endif
        </div>
    </div>
    
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3 w-full md:w-auto z-10">
        @if($todayAttendance && in_array($todayAttendance->status_kehadiran, ['izin', 'cuti', 'sakit']))
            <div class="opacity-90 bg-primary/15 text-primary border border-primary/30 px-5 py-2.5 sm:py-3 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2 select-none shadow-sm cursor-default" style="pointer-events: none;">
                <span class="material-symbols-outlined text-[18px]">event_available</span>
                <span class="uppercase">STATUS HARI INI: {{ $todayAttendance->status_kehadiran }}</span>
            </div>
            <button type="button" onclick="toggleHrLeaveForm()" class="px-4 py-2.5 sm:py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs sm:text-sm shadow transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                <span class="material-symbols-outlined text-[18px]">event_busy</span>
                <span>Ajukan Cuti/Izin</span>
            </button>
        @else
            <button type="button" onclick="toggleHrLeaveForm()" class="px-4 py-2.5 sm:py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs sm:text-sm shadow transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                <span class="material-symbols-outlined text-[18px]">event_busy</span>
                <span>Ajukan Cuti/Izin</span>
            </button>
            @if(!$todayAttendance || !$todayAttendance->jam_masuk)
                <a href="{{ route('attendance.index') }}" class="px-5 py-2.5 sm:py-3 rounded-xl bg-primary hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">location_on</span>
                    <span>Halaman Presensi</span>
                </a>
            @elseif($todayAttendance && $todayAttendance->jam_masuk && !$todayAttendance->jam_keluar)
                <a href="{{ route('attendance.index') }}" class="px-5 py-2.5 sm:py-3 rounded-xl bg-[#1e293b] hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span>Clock Out Presensi</span>
                </a>
            @else
                <div class="px-5 py-2.5 sm:py-3 rounded-xl bg-slate-200 text-slate-600 font-bold text-xs sm:text-sm flex items-center justify-center gap-2 cursor-default select-none">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span>Presensi Selesai</span>
                </div>
            @endif
        @endif
    </div>
</section>

<!-- Inline Request Leave Form HR Manager -->
<div id="form-request-leave-hr" class="hidden mb-6 bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden transition-all duration-300">
    <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between bg-slate-50">
        <h3 class="font-bold text-sm sm:text-base text-slate-800 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-lg sm:text-xl">edit_document</span>
            Pengajuan Cuti / Izin HR Manager (Ke Super Admin)
        </h3>
        <button type="button" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1" onclick="document.getElementById('form-request-leave-hr').classList.add('hidden')">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <form action="{{ route('attendance.leave') }}" method="POST" class="p-4 sm:p-6" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tipe Pengajuan <span class="text-error">*</span></label>
                    <select name="tipe" id="hr-leave-tipe" required class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 bg-white" onchange="handleHrLeaveType(this.value)">
                        <option value="">Pilih Tipe...</option>
                        <option value="cuti">Cuti Tahunan</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit (Surat Dokter)</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tgl Mulai <span class="text-error">*</span></label>
                        <input type="date" name="tanggal_mulai" id="hr-tgl-mulai" required class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs sm:text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tgl Selesai <span class="text-error">*</span></label>
                        <input type="date" name="tanggal_selesai" id="hr-tgl-selesai" required class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs sm:text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" id="hr-label-dokumen">Dokumen Pendukung <span class="text-slate-400 font-normal lowercase">(opsional)</span></label>
                    <input type="file" name="dokumen_pendukung" id="hr-input-dokumen" accept="image/*,.pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer border border-slate-200 rounded-xl p-1 bg-slate-50">
                </div>
            </div>
            <div class="lg:col-span-2 flex flex-col justify-between h-full space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Keterangan / Alasan <span class="text-error">*</span></label>
                    <textarea name="keterangan" rows="5" required class="w-full border border-slate-300 rounded-xl p-3 text-xs sm:text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="Tuliskan keterangan detail pengajuan cuti/izin/sakit ke Super Admin..."></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" class="px-5 py-2.5 text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer" onclick="document.getElementById('form-request-leave-hr').classList.add('hidden')">
                        Tutup
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-xs sm:text-sm font-bold text-white rounded-xl shadow transition-all cursor-pointer active:scale-95 bg-amber-500 hover:bg-amber-600">
                        Kirim Pengajuan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function toggleHrLeaveForm() {
        const formEl = document.getElementById('form-request-leave-hr');
        if (formEl) {
            formEl.classList.toggle('hidden');
            if (!formEl.classList.contains('hidden')) {
                formEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const hrTglMulai = document.getElementById('hr-tgl-mulai');
        const hrTglSelesai = document.getElementById('hr-tgl-selesai');
        if (hrTglMulai && hrTglSelesai) {
            hrTglMulai.addEventListener('change', (e) => {
                hrTglSelesai.min = e.target.value;
                if (!hrTglSelesai.value || hrTglSelesai.value < e.target.value) {
                    hrTglSelesai.value = e.target.value;
                }
            });
        }
    });

    function handleHrLeaveType(val) {
        const lbl = document.getElementById('hr-label-dokumen');
        const inp = document.getElementById('hr-input-dokumen');
        if (val === 'sakit') {
            if (lbl) lbl.innerHTML = 'Surat Keterangan Dokter <span class="text-error font-bold">*</span> <span class="text-xs text-error lowercase">(wajib jika sakit)</span>';
            if (inp) inp.required = true;
        } else {
            if (lbl) lbl.innerHTML = 'Dokumen Pendukung <span class="text-slate-400 font-normal lowercase">(opsional)</span>';
            if (inp) inp.required = false;
        }
    }
</script>

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
        <div class="h-[280px] w-full relative" id="chart-container">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>
    <!-- Employee Status -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow flex flex-col">
        <h4 class="font-title-sm text-title-sm text-on-background font-bold mb-8">Status Karyawan</h4>
        <div class="flex-1 flex flex-col items-center justify-center relative">
            <!-- Donut Chart -->
            @php
                $total = $total_karyawan > 0 ? $total_karyawan : 1; // Prevent division by zero
                $pct_tetap = round(($status_tetap / $total) * 100);
                $pct_kontrak = round(($status_kontrak / $total) * 100);
                $pct_magang = round(($status_magang / $total) * 100);
            @endphp
            <div class="w-48 h-48 relative flex items-center justify-center">
                <canvas id="statusChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-headline-md font-display-lg block leading-none font-bold text-on-background" id="donut-total">{{ $total_karyawan }}</span>
                    <span class="text-[10px] text-on-surface-variant uppercase tracking-wider mt-1">Total</span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-8 w-full">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-primary"></span>
                    <span class="text-body-sm text-on-surface-variant">Tetap ({{ $pct_tetap }}%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-surface-container-highest"></span>
                    <span class="text-body-sm text-on-surface-variant">Kontrak ({{ $pct_kontrak }}%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-tertiary-fixed-dim"></span>
                    <span class="text-body-sm text-on-surface-variant">Magang ({{ $pct_magang }}%)</span>
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
                    <td class="px-8 py-4 text-on-surface-variant">
                        @if($emp->position)
                            {{ $emp->position->nama_jabatan }}
                        @elseif($emp->user && $emp->user->role !== 'karyawan')
                            {{ ucwords(str_replace('_', ' ', $emp->user->role)) }}
                        @else
                            Karyawan
                        @endif
                    </td>
                    <td class="px-8 py-4">
                        <span class="px-2 py-1 rounded bg-secondary-container/30 text-secondary text-xs font-semibold uppercase">{{ $emp->department->nama_department ?? 'Umum' }}</span>
                    </td>
                    <td class="px-8 py-4 text-center">
                        <span class="px-3 py-1 rounded-full bg-primary-container/10 text-primary text-xs font-bold">{{ $emp->status_kerja ?? 'Tetap' }}</span>
                    </td>
                    <td class="px-8 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('backoffice.karyawan.show', $emp->id) }}" class="w-8 h-8 rounded-lg bg-surface-container-low text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-all cursor-pointer" title="Detail"><span class="material-symbols-outlined text-lg">search</span></a>
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
<div class="bg-[#0b1c30]/60 backdrop-blur-sm" id="modal-tambah" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 12px;">
    <div class="bg-white rounded-2xl shadow-2xl border border-outline-variant flex flex-col max-h-[90dvh] overflow-hidden animate-modal-pop w-full max-w-xl">
        <!-- Modal Header -->
        <div class="px-5 py-4 border-b border-outline-variant flex justify-between items-center bg-slate-50">
            <div>
                <h3 class="font-bold text-slate-800 text-sm sm:text-base">Pilih Anggota Baru</h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar pelamar yang belum ditempatkan ke departemen mana pun.</p>
            </div>
            <button class="p-1 hover:bg-slate-200 rounded-full text-slate-400 cursor-pointer" id="btn-close-modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <!-- Modal Search Bar -->
        <div class="p-4 border-b border-outline-variant bg-slate-50/50 flex gap-3">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                <input class="w-full bg-white border border-slate-300 rounded-xl pl-10 pr-4 py-2 text-xs sm:text-sm outline-none focus:ring-2 focus:ring-primary/20 transition-all text-slate-800 placeholder:text-slate-400" placeholder="Cari nama atau NIK pelamar..." type="text" id="search-modal-karyawan">
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
                    @forelse($unassigned_employees as $emp)
                    <tr class="hover:bg-primary/5 transition-colors group" data-nik="{{ $emp->nik }}" data-nama="{{ $emp->nama_lengkap }}" data-jabatan="{{ $emp->position->nama ?? 'Staff' }}" data-dept="-" data-status="{{ $emp->status_kerja ?? 'Tetap' }}">
                        <td class="px-lg py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/5 flex items-center justify-center text-xs font-bold text-primary/70">
                                    {{ strtoupper(substr($emp->nama_lengkap ?? 'U', 0, 2)) }}
                                </div>
                                <span class="font-bold text-on-surface">{{ $emp->nama_lengkap }}</span>
                            </div>
                        </td>
                        <td class="px-lg py-3 font-mono text-on-surface-variant">{{ $emp->nik }}</td>
                        <td class="px-lg py-3 text-right">
                            <div class="inline-flex items-center gap-1.5 justify-end">
                                <button class="btn-assign bg-primary hover:bg-primary-container text-white px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer active:scale-95 transition-all">
                                    + Pilih
                                </button>
                                <button type="button" onclick="hapusKaryawanDuplikatDashboard('{{ $emp->id }}', '{{ addslashes($emp->nama_lengkap) }}')" class="p-1.5 text-red-500 hover:bg-red-50 hover:text-red-700 rounded-lg transition-colors cursor-pointer" title="Hapus Data Duplikat">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-lg py-6 text-center text-slate-500">Semua karyawan sudah memiliki departemen.</td>
                    </tr>
                    @endforelse
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
                        <option value="">Pilih Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_department }}</option>
                        @endforeach
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initialize Chart.js Data
    const attendanceTrendLabels = @json($attendance_trend['labels'] ?? []);
    const attendanceTrendData = @json($attendance_trend['data'] ?? []);
    const statusData = [{{ $status_tetap }}, {{ $status_kontrak }}, {{ $status_magang }}];
    
    // Attendance Line Chart
    const ctxAttendance = document.getElementById('attendanceChart').getContext('2d');
    
    // Create Gradient for Line Chart
    let gradientLine = ctxAttendance.createLinearGradient(0, 0, 0, 400);
    gradientLine.addColorStop(0, 'rgba(0, 102, 255, 0.4)');
    gradientLine.addColorStop(1, 'rgba(0, 102, 255, 0)');

    new Chart(ctxAttendance, {
        type: 'line',
        data: {
            labels: attendanceTrendLabels,
            datasets: [{
                label: 'Kehadiran (Orang)',
                data: attendanceTrendData,
                borderColor: '#0066ff',
                backgroundColor: gradientLine,
                borderWidth: 3,
                pointBackgroundColor: '#0066ff',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 13, family: "'Inter', sans-serif" },
                    bodyFont: { size: 12, family: "'Inter', sans-serif" },
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)', borderDash: [5, 5] },
                    ticks: { precision: 0, font: { family: "'Inter', sans-serif", size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: "'Inter', sans-serif", size: 11 } }
                }
            }
        }
    });

    // Status Karyawan Doughnut Chart
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Tetap', 'Kontrak', 'Magang'],
            datasets: [{
                data: statusData,
                backgroundColor: ['#0066ff', '#f1f5f9', '#94a3b8'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    bodyFont: { size: 12, family: "'Inter', sans-serif" },
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: true
                }
            }
        }
    });

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
    // Dihapus hardcode localStorage, data langsung dari backend via view.
    
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
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const nikInput = document.createElement('input');
            nikInput.type = 'hidden';
            nikInput.name = 'nik';
            nikInput.value = nik;
            
            const jabatanInput = document.createElement('input');
            jabatanInput.type = 'hidden';
            jabatanInput.name = 'jabatan';
            jabatanInput.value = jabatan;
            
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status_kerja';
            statusInput.value = status;
            
            const deptInput = document.createElement('input');
            deptInput.type = 'hidden';
            deptInput.name = 'departemen';
            deptInput.value = dept; // Kirim ID departemen yang valid
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("backoffice.karyawan.assign") }}';
            form.appendChild(csrfToken);
            form.appendChild(nikInput);
            form.appendChild(jabatanInput);
            form.appendChild(statusInput);
            form.appendChild(deptInput);
            
            document.body.appendChild(form);
            form.submit();
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
            const NIK = targetRowToRelease.querySelector('td:nth-child(2)').innerText;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const nikInput = document.createElement('input');
            nikInput.type = 'hidden';
            nikInput.name = 'nik';
            nikInput.value = NIK;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("backoffice.karyawan.lepas") }}';
            form.appendChild(csrfToken);
            form.appendChild(nikInput);
            
            document.body.appendChild(form);
            form.submit();
        }
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

    window.hapusKaryawanDuplikatDashboard = function(id, nama) {
        if (confirm(`Apakah Anda yakin ingin menghapus data pelamar/karyawan duplikat "${nama}"? Akun dan data ini akan dihapus permanen.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/backoffice/karyawan/${id}/force-delete`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    };
</script>
@endpush

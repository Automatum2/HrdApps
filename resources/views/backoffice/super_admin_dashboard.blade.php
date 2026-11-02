@extends('layouts.admin')

@section('title', 'Dashboard Super Admin - HRDApps')
@section('page_title', 'Dashboard Super Admin')

@section('content')
<!-- Page Header -->
<div class="mb-6">
    <h2 class="font-display-lg text-2xl font-bold text-on-background mb-1">Dashboard Super Admin</h2>
    <p class="text-on-surface-variant text-sm">Kelola seluruh operasional HR perusahaan secara global.</p>
</div>

<!-- Stat Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Stat Card 1: Total Karyawan -->
    <div class="bg-white rounded-xl border border-outline-variant p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between hover-card-float">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-on-surface-variant font-medium text-sm mb-1">Total Karyawan</p>
                <h3 class="text-3xl font-bold text-on-background">{{ $total_karyawan }}</h3>
            </div>
            <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary">
                <span class="material-symbols-outlined">group</span>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            @if($kenaikan_karyawan > 0)
                <span class="text-green-700 bg-green-50 px-2 py-0.5 rounded text-xs font-semibold flex items-center border border-green-200">
                    <span class="material-symbols-outlined text-[14px] mr-1">trending_up</span> +{{ $kenaikan_karyawan }}%
                </span>
            @elseif($kenaikan_karyawan < 0)
                <span class="text-red-700 bg-red-50 px-2 py-0.5 rounded text-xs font-semibold flex items-center border border-red-200">
                    <span class="material-symbols-outlined text-[14px] mr-1">trending_down</span> {{ $kenaikan_karyawan }}%
                </span>
            @else
                <span class="text-slate-600 bg-slate-50 px-2 py-0.5 rounded text-xs font-semibold flex items-center border border-slate-200">
                    <span class="material-symbols-outlined text-[14px] mr-1">trending_flat</span> 0%
                </span>
            @endif
            <span class="text-slate-500 ml-2 text-xs">dari bulan lalu</span>
        </div>
    </div>

    <!-- Stat Card 2: Total HR Manager -->
    <div class="bg-white rounded-xl border border-outline-variant p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between hover-card-float">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-on-surface-variant font-medium text-sm mb-1">Total HR Manager</p>
                <h3 class="text-3xl font-bold text-on-background">{{ $total_manager }}</h3>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center text-green-700">
                <span class="material-symbols-outlined">manage_accounts</span>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-slate-500 text-xs">Aktif di seluruh departemen</span>
        </div>
    </div>

    <!-- Stat Card 3: Total Departemen -->
    <div class="bg-white rounded-xl border border-outline-variant p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between hover-card-float">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-on-surface-variant font-medium text-sm mb-1">Total Departemen</p>
                <h3 class="text-3xl font-bold text-on-background">{{ $total_departemen }}</h3>
            </div>
            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-700">
                <span class="material-symbols-outlined">domain</span>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-slate-500 text-xs">Termasuk cabang regional</span>
        </div>
    </div>
</div>

<!-- Pemantauan Hari Ini (Hadir / Training / Cuti) -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Karyawan Hadir Hari Ini -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-outline-variant flex justify-between items-center bg-slate-50/50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-green-600">how_to_reg</span>
                <h3 class="font-bold text-on-background">Karyawan Hadir Hari Ini</h3>
            </div>
            <span class="bg-green-50 text-green-700 font-bold px-2.5 py-0.5 rounded-full text-xs border border-green-200">{{ $hadirHariIni->count() }}</span>
        </div>
        <div class="p-4 divide-y divide-slate-100 overflow-y-auto custom-scrollbar" style="max-height: 320px;">
            @forelse($hadirHariIni as $att)
            <div class="py-2.5 flex justify-between items-center gap-3">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-800 truncate">{{ $att->employee->nama_lengkap ?? 'Unknown' }}</p>
                    <p class="text-[11px] text-slate-500">{{ $att->employee->department->nama_department ?? '-' }}</p>
                </div>
                <span class="text-xs font-mono font-bold text-green-700 whitespace-nowrap">{{ $att->jam_masuk ? substr($att->jam_masuk, 0, 5) : '--:--' }}</span>
            </div>
            @empty
            <p class="text-sm text-slate-500 text-center py-6">Belum ada karyawan yang hadir hari ini.</p>
            @endforelse
        </div>
        <div class="p-4 border-t border-outline-variant bg-slate-50/50">
            <a href="{{ route('backoffice.super_admin.absensi', ['status' => 'hadir']) }}" class="text-primary text-sm font-medium hover:underline flex items-center gap-1">
                <span>Lihat Monitoring Absensi</span>
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
        </div>
    </div>

    <!-- Sedang Training Hari Ini -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-outline-variant flex justify-between items-center bg-slate-50/50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">model_training</span>
                <h3 class="font-bold text-on-background">Sedang Training Hari Ini</h3>
            </div>
            <span class="bg-primary/10 text-primary font-bold px-2.5 py-0.5 rounded-full text-xs border border-primary/20">{{ $trainingHariIni->count() }}</span>
        </div>
        <div class="p-4 divide-y divide-slate-100 overflow-y-auto custom-scrollbar" style="max-height: 320px;">
            @forelse($trainingHariIni as $training)
            <div class="py-2.5">
                <p class="text-sm font-bold text-slate-800 truncate">{{ $training->nama_training ?: 'Pelatihan' }}</p>
                <p class="text-[11px] text-slate-500">{{ \Carbon\Carbon::parse($training->tanggal_mulai)->translatedFormat('j l F Y') }} s.d {{ \Carbon\Carbon::parse($training->tanggal_selesai)->translatedFormat('j l F Y') }}</p>
                <div class="flex flex-wrap gap-1 mt-1.5">
                    @forelse($training->employees as $emp)
                        <span class="text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-semibold">{{ $emp->nama_lengkap }}</span>
                    @empty
                        <span class="text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-400">Tidak ada peserta</span>
                    @endforelse
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-500 text-center py-6">Tidak ada pelatihan berlangsung hari ini.</p>
            @endforelse
        </div>
        <div class="p-4 border-t border-outline-variant bg-slate-50/50">
            <a href="{{ route('backoffice.training.index') }}" class="text-primary text-sm font-medium hover:underline flex items-center gap-1">
                <span>Kelola Training</span>
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
        </div>
    </div>

    <!-- Cuti / Izin / Sakit Hari Ini -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-outline-variant flex justify-between items-center bg-slate-50/50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500">event_available</span>
                <h3 class="font-bold text-on-background">Cuti / Izin / Sakit</h3>
            </div>
            <span class="bg-amber-50 text-amber-700 font-bold px-2.5 py-0.5 rounded-full text-xs border border-amber-200">{{ $cutiHariIni->count() }}</span>
        </div>
        <div class="p-4 divide-y divide-slate-100 overflow-y-auto custom-scrollbar" style="max-height: 320px;">
            @forelse($cutiHariIni as $att)
            <div class="py-2.5 flex justify-between items-center gap-3">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-800 truncate">{{ $att->employee->nama_lengkap ?? 'Unknown' }}</p>
                    <p class="text-[11px] text-slate-500">{{ $att->employee->department->nama_department ?? '-' }}</p>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase {{ strtolower($att->status_kehadiran) === 'sakit' ? 'bg-orange-50 text-orange-700' : 'bg-blue-50 text-blue-700' }}">{{ ucfirst($att->status_kehadiran) }}</span>
            </div>
            @empty
            <p class="text-sm text-slate-500 text-center py-6">Tidak ada karyawan cuti/izin/sakit hari ini.</p>
            @endforelse
        </div>
        <div class="p-4 border-t border-outline-variant bg-slate-50/50">
            <a href="{{ route('backoffice.super_admin.absensi') }}" class="text-primary text-sm font-medium hover:underline flex items-center gap-1">
                <span>Lihat Monitoring Absensi</span>
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
        </div>
    </div>
</div>

<!-- Main Table Section -->
<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
    <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-slate-50/50">
        <h3 class="font-bold text-on-background text-lg">Daftar HR Manager Terbaru</h3>
        <a href="{{ route('backoffice.super_admin.kelola_hr') }}" class="text-primary font-medium hover:underline flex items-center gap-1 text-sm">
            <span>Lihat Semua</span>
            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 border-b border-outline-variant">
                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">NIK</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Email</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Jabatan</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="text-sm font-medium text-slate-700 divide-y divide-slate-100">
                @forelse($latest_managers as $manager)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-6 font-mono">{{ $manager->employee ? $manager->employee->nik : '-' }}</td>
                    <td class="py-4 px-6 font-bold text-slate-800">{{ $manager->employee ? $manager->employee->nama_lengkap : $manager->username }}</td>
                    <td class="py-4 px-6 text-slate-600">{{ $manager->email }}</td>
                    <td class="py-4 px-6 text-slate-600">{{ $manager->employee && $manager->employee->position ? $manager->employee->position->nama_jabatan : 'HR Manager' }}</td>
                    <td class="py-4 px-6">
                        @if($manager->employee && $manager->employee->status === 'nonaktif')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                Nonaktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                Aktif
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-slate-500">Belum ada data HR Manager.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

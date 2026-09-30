@extends('layouts.admin')

@section('title', 'Monitoring Absensi Global - HRDApps')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-1">
                <a class="hover:text-primary transition-colors" href="{{ route('backoffice.dashboard') }}">Beranda</a>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                <span class="text-primary font-semibold">Monitoring Absensi Global</span>
            </nav>
            <h1 class="text-2xl font-bold text-on-surface">Monitoring Absensi Global</h1>
            <p class="text-body-sm text-on-surface-variant">Pantau absensi seluruh karyawan, manager departemen, dan HR manager di perusahaan.</p>
        </div>
    </div>

    <!-- Ringkasan Statistik Hari Ini -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">check_circle</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Hadir Hari Ini</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalHadir }} <span class="text-xs font-normal text-slate-500">orang</span></p>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">event_busy</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Izin / Sakit / Cuti</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalIzin }} <span class="text-xs font-normal text-slate-500">orang</span></p>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">cancel</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanpa Keterangan (Alpha)</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalAlpha }} <span class="text-xs font-normal text-slate-500">orang</span></p>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm">
        <form action="{{ route('backoffice.super_admin.absensi') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <!-- Filter Tanggal -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-600" for="tanggal">Pilih Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800">
            </div>

            <!-- Filter Departemen -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-600" for="department_id">Departemen</label>
                <select id="department_id" name="department_id" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 cursor-pointer">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>{{ $dept->nama_department }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Search Nama / NIK -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-600" for="search">Cari Nama / NIK</label>
                <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Ketik nama atau NIK..." class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800">
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full bg-primary hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-sm">filter_alt</span>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-outline-variant bg-surface-bright flex justify-between items-center">
            <h2 class="text-base font-bold text-on-surface">Data Presensi Harian ({{ \Carbon\Carbon::parse($tanggal)->isoFormat('D MMMM YYYY') }})</h2>
            <span class="text-xs text-slate-500 font-medium">Total: {{ $attendances->count() }} Record</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-outline-variant">
                        <th class="py-3 px-4 font-bold">Karyawan</th>
                        <th class="py-3 px-4 font-bold">Role & Departemen</th>
                        <th class="py-3 px-4 font-bold">Jam Masuk</th>
                        <th class="py-3 px-4 font-bold">Jam Keluar</th>
                        <th class="py-3 px-4 font-bold">Status Kehadiran</th>
                        <th class="py-3 px-4 font-bold">Status Kerja</th>
                        <th class="py-3 px-4 font-bold">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant text-sm text-slate-700">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-800">{{ $att->employee->nama_lengkap ?? '-' }}</div>
                                <div class="text-xs text-slate-400 font-mono">{{ $att->employee->nik ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider mb-1
                                    {{ ($att->employee->user->role ?? '') === 'super_admin' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ ($att->employee->user->role ?? '') === 'hr_manager' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ ($att->employee->user->role ?? '') === 'manager_departemen' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                    {{ ($att->employee->user->role ?? '') === 'karyawan' ? 'bg-slate-100 text-slate-700' : '' }}">
                                    {{ str_replace('_', ' ', $att->employee->user->role ?? 'Karyawan') }}
                                </span>
                                <div class="text-xs text-slate-500">{{ $att->employee->department->nama_department ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 font-mono text-xs">
                                {{ $att->jam_masuk ? \Carbon\Carbon::parse($att->jam_masuk)->format('H:i:s') : '-' }}
                            </td>
                            <td class="py-3 px-4 font-mono text-xs">
                                {{ $att->jam_keluar ? \Carbon\Carbon::parse($att->jam_keluar)->format('H:i:s') : '-' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ in_array(strtolower($att->status_kehadiran), ['hadir', 'wfo', 'wfh']) ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ in_array(strtolower($att->status_kehadiran), ['izin', 'sakit', 'cuti']) ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ strtolower($att->status_kehadiran) === 'alpha' ? 'bg-rose-100 text-rose-700' : '' }}">
                                    {{ ucfirst($att->status_kehadiran) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-600 font-medium">
                                    {{ strtoupper($att->status_kerja ?? 'WFO') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">
                                {{ $att->keterangan ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">event_busy</span>
                                Tidak ada data absensi untuk tanggal dan filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($attendances->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
            <p class="text-xs text-slate-500">
                Menampilkan <span class="font-bold text-slate-700">{{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-700">{{ $attendances->total() }}</span> entri
            </p>
            <div>
                {{ $attendances->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

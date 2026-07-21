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
            <span class="text-green-700 bg-green-50 px-2 py-0.5 rounded text-xs font-semibold flex items-center border border-green-200">
                <span class="material-symbols-outlined text-[14px] mr-1">trending_up</span> +5.2%
            </span>
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
                    <td class="py-4 px-6 text-slate-600">HR Manager</td>
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

@extends('layouts.admin')

@section('title', 'View Khusus Kinerja + Absensi - HRDApps')
@section('page_title', 'Audit Kinerja Harian')

@section('content')
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant text-body-sm mb-2 font-medium">
            <a class="hover:text-primary transition-colors text-xs" href="{{ route('backoffice.dashboard') }}">Beranda</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a class="hover:text-primary transition-colors text-xs" href="{{ route('backoffice.laporan') }}">Laporan</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-semibold text-xs">View Kinerja</span>
        </nav>
        <div class="flex items-center gap-3">
            <h2 class="font-bold text-headline-md text-on-surface">Audit Kinerja & Absensi</h2>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-xl border border-outline-variant p-4 md:p-6 mb-6 shadow-sm">
    <form action="{{ route('backoffice.laporan.kinerja') }}" method="GET" class="flex flex-col lg:flex-row gap-4 items-end">
        <div class="w-full lg:flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-xs font-semibold text-on-surface-variant block mb-1">Dari Tanggal</label>
                <input type="date" name="dari_tanggal" value="{{ $dari }}" class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg text-sm focus:ring-2 focus:ring-primary/20 outline-none">
            </div>
            <div>
                <label class="text-xs font-semibold text-on-surface-variant block mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai_tanggal" value="{{ $sampai }}" class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg text-sm focus:ring-2 focus:ring-primary/20 outline-none">
            </div>
            <div>
                <label class="text-xs font-semibold text-on-surface-variant block mb-1">Departemen</label>
                <select name="departemen_id" class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg text-sm focus:ring-2 focus:ring-primary/20 outline-none">
                    <option value="all">Semua Departemen</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('departemen_id') == $dept->id ? 'selected' : '' }}>{{ $dept->nama_department }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="w-full lg:w-auto px-6 py-2.5 bg-primary text-white font-bold rounded-lg hover:brightness-110 active:scale-95 transition-all shadow-sm text-sm whitespace-nowrap flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[18px]">filter_list</span> Filter
        </button>
    </form>
</div>

<!-- Table Data -->
<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr class="text-on-surface-variant font-semibold text-xs">
                    <th class="px-6 py-4 uppercase">Karyawan</th>
                    <th class="px-6 py-4 uppercase">Tanggal</th>
                    <th class="px-6 py-4 uppercase">Masuk & Lokasi</th>
                    <th class="px-6 py-4 uppercase">Keluar & Lokasi</th>
                    <th class="px-6 py-4 uppercase min-w-[200px]">Laporan Harian (Keterangan)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/40 font-body-sm text-body-sm">
                @forelse($attendances as $att)
                <tr class="hover:bg-primary/5 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-bold text-on-surface">{{ $att->employee->nama_lengkap ?? '-' }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $att->employee->department->nama_department ?? 'Umum' }}</p>
                    </td>
                    <td class="px-6 py-4 font-mono">{{ \Carbon\Carbon::parse($att->tanggal)->format('d/m/Y') }}</td>
                    
                    <td class="px-6 py-4">
                        @if($att->foto_masuk)
                            <div class="flex items-center gap-3">
                                <a href="{{ asset('storage/' . $att->foto_masuk) }}" target="_blank" class="w-12 h-12 rounded bg-surface-container overflow-hidden border border-outline-variant hover:scale-105 transition-transform block">
                                    <img src="{{ asset('storage/' . $att->foto_masuk) }}" class="w-full h-full object-cover">
                                </a>
                                <div>
                                    <p class="font-mono text-sm font-bold">{{ $att->jam_masuk ?? '--:--' }}</p>
                                    @if($att->lokasi_masuk)
                                    <a href="https://maps.google.com/?q={{ $att->lokasi_masuk }}" target="_blank" class="text-[10px] text-primary hover:underline flex items-center gap-1 mt-1">
                                        <span class="material-symbols-outlined text-[12px]">location_on</span> Cek Peta
                                    </a>
                                    @else
                                    <p class="text-[10px] text-on-surface-variant">Lokasi tidak tersedia</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p class="text-on-surface-variant text-xs italic">Tidak ada foto absen masuk</p>
                        @endif
                    </td>
                    
                    <td class="px-6 py-4">
                        @if($att->foto_keluar)
                            <div class="flex items-center gap-3">
                                <a href="{{ asset('storage/' . $att->foto_keluar) }}" target="_blank" class="w-12 h-12 rounded bg-surface-container overflow-hidden border border-outline-variant hover:scale-105 transition-transform block">
                                    <img src="{{ asset('storage/' . $att->foto_keluar) }}" class="w-full h-full object-cover">
                                </a>
                                <div>
                                    <p class="font-mono text-sm font-bold">{{ $att->jam_keluar ?? '--:--' }}</p>
                                    @if($att->lokasi_keluar)
                                    <a href="https://maps.google.com/?q={{ $att->lokasi_keluar }}" target="_blank" class="text-[10px] text-primary hover:underline flex items-center gap-1 mt-1">
                                        <span class="material-symbols-outlined text-[12px]">location_on</span> Cek Peta
                                    </a>
                                    @else
                                    <p class="text-[10px] text-on-surface-variant">Lokasi tidak tersedia</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p class="text-on-surface-variant text-xs italic">Tidak ada foto absen keluar</p>
                        @endif
                    </td>
                    
                    <td class="px-6 py-4 whitespace-normal">
                        <div class="p-3 bg-surface-container-low rounded border border-outline-variant text-xs h-full text-on-surface max-h-32 overflow-y-auto">
                            {!! $att->keterangan ?: 'Tidak ada laporan harian.' !!}
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-on-surface-variant">Tidak ada data absensi pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

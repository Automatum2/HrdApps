@extends('layouts.admin')

@section('title', 'Riwayat Penggajian - HRDApps')
@section('page_title', 'Riwayat Penggajian')

@section('content')
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant text-body-sm mb-2 font-medium">
            <a class="hover:text-primary transition-colors text-xs" href="{{ route('backoffice.dashboard') }}">Beranda</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-semibold text-xs">Riwayat Penggajian</span>
        </nav>
        <div class="flex items-center gap-3">
            <h2 class="font-bold text-headline-md text-on-surface">Riwayat & Periode Gaji</h2>
        </div>
    </div>
    <div class="flex gap-2">
        <button class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white font-semibold rounded-lg hover:brightness-110 transition-all shadow active:scale-95 text-xs cursor-pointer" onclick="document.getElementById('modal-tambah-periode').style.display='flex'">
            <span class="material-symbols-outlined text-lg">add</span>
            <span>Buat Periode Baru</span>
        </button>
    </div>
</div>

<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr class="text-on-surface-variant font-semibold text-xs">
                    <th class="px-6 py-4 uppercase">No</th>
                    <th class="px-6 py-4">Nama Periode</th>
                    <th class="px-6 py-4">Tanggal Mulai</th>
                    <th class="px-6 py-4">Tanggal Selesai</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/40 font-body-sm text-body-sm">
                @forelse($periods as $index => $period)
                <tr class="hover:bg-primary/5 transition-colors group">
                    <td class="px-6 py-4 text-on-surface font-semibold font-mono">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 font-bold text-on-surface text-sm">{{ $period->nama_periode }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($period->tanggal_mulai)->translatedFormat('d F Y') }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($period->tanggal_selesai)->translatedFormat('d F Y') }}</td>
                    <td class="px-6 py-4">
                        @if($period->status == 'draft')
                            <span class="px-2.5 py-0.5 bg-surface-container-high text-on-surface-variant text-[10px] font-bold rounded-full uppercase tracking-wider border border-outline-variant/10">Draft</span>
                        @elseif($period->status == 'proses')
                            <span class="px-2.5 py-0.5 bg-primary-container/10 text-primary text-[10px] font-bold rounded-full uppercase tracking-wider border border-primary/20">Proses</span>
                        @elseif($period->status == 'selesai')
                            <span class="px-2.5 py-0.5 bg-tertiary/10 text-tertiary text-[10px] font-bold rounded-full uppercase tracking-wider border border-tertiary/20">Selesai</span>
                        @else
                            <span class="px-2.5 py-0.5 bg-error/10 text-error text-[10px] font-bold rounded-full uppercase tracking-wider border border-error/20">{{ $period->status }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('backoffice.penggajian.show', $period->id) }}" class="inline-flex items-center gap-1 p-1.5 bg-white border border-outline-variant text-primary rounded hover:bg-primary hover:text-white transition-all shadow-sm cursor-pointer" title="Buka Detail">
                            <span class="material-symbols-outlined text-sm">visibility</span> <span class="text-xs px-1 font-semibold">Detail</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-on-surface-variant">Belum ada data periode penggajian. Silakan buat periode baru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('modals')
<!-- Modal Tambah Periode -->
<div class="bg-[#0b1c30]/60 backdrop-blur-sm" id="modal-tambah-periode" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 16px;">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant flex flex-col animate-modal-pop overflow-hidden" style="width: 100%; max-width: 450px; min-width: 280px;">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-base">Buat Periode Baru</h3>
            <button class="p-1 hover:bg-slate-200 rounded-full text-slate-400 cursor-pointer" onclick="document.getElementById('modal-tambah-periode').style.display='none'">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <form action="{{ route('backoffice.penggajian.store_period') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1 block">Nama Periode</label>
                <input name="nama_periode" type="text" placeholder="Contoh: Gaji Juni 2026" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1 block">Tanggal Mulai</label>
                    <input name="tanggal_mulai" type="date" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" required>
                </div>
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1 block">Tanggal Selesai</label>
                    <input name="tanggal_selesai" type="date" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" required>
                </div>
            </div>
            
            <div class="pt-4 flex justify-end gap-3 border-t border-outline-variant mt-6">
                <button type="button" class="border border-slate-300 hover:bg-slate-100 text-slate-600 px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all" onclick="document.getElementById('modal-tambah-periode').style.display='none'">Batal</button>
                <button type="submit" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all">Simpan Periode</button>
            </div>
        </form>
    </div>
</div>
@endpush
@endsection

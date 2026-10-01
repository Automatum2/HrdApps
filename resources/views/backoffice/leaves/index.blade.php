@extends('layouts.admin')

@section('page_title', 'Persetujuan Cuti')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-outline-variant p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h3 class="text-base sm:text-lg font-bold text-on-surface">Daftar Pengajuan Cuti & Izin</h3>
        </div>

        @if(session('success'))
            <div class="p-3.5 mb-4 text-xs sm:text-sm text-green-800 rounded-xl bg-green-50 border border-green-200" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 mb-4 text-xs sm:text-sm text-red-800 rounded-xl bg-red-50 border border-red-200" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-surface-container-low border-y border-outline-variant text-xs text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 font-bold">Karyawan</th>
                        <th class="py-3.5 px-4 font-bold">Departemen</th>
                        <th class="py-3.5 px-4 font-bold">Tipe</th>
                        <th class="py-3.5 px-4 font-bold">Rentang Tanggal</th>
                        <th class="py-3.5 px-4 font-bold min-w-[200px]">Keterangan</th>
                        <th class="py-3.5 px-4 font-bold text-center">Status</th>
                        <th class="py-3.5 px-4 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant text-xs sm:text-sm">
                    @forelse($leaves as $leave)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-4 text-on-surface font-semibold">{{ $leave->employee->nama_lengkap ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-on-surface-variant">{{ $leave->employee->department->nama_department ?? '-' }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                                    {{ $leave->tipe === 'cuti' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $leave->tipe === 'sakit' ? 'bg-rose-100 text-rose-800' : '' }}
                                    {{ $leave->tipe === 'izin' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ $leave->tipe }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-600">
                                {{ \Carbon\Carbon::parse($leave->tanggal_mulai)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($leave->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-on-surface-variant whitespace-normal">
                                <p class="text-xs leading-relaxed max-w-xs">{{ $leave->keterangan }}</p>
                                @if($leave->dokumen_pendukung)
                                    <a href="{{ asset('storage/' . $leave->dokumen_pendukung) }}" target="_blank" class="text-primary hover:underline text-[11px] font-bold inline-flex items-center gap-1 mt-1">
                                        <span class="material-symbols-outlined text-[14px]">attachment</span>
                                        <span>Lihat Dokumen Dokter</span>
                                    </a>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($leave->status === 'menunggu_manager')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        Menunggu Manager
                                    </span>
                                @elseif($leave->status === 'menunggu_hr')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                                        Menunggu HR
                                    </span>
                                @elseif($leave->status === 'menunggu_super_admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-800 border border-purple-200">
                                        Menunggu Super Admin
                                    </span>
                                @elseif($leave->status === 'disetujui')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-800 border border-green-200">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-800 border border-red-200" title="{{ $leave->alasan_penolakan }}">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex justify-center gap-1.5">
                                    @php 
                                        $user = auth()->user();
                                        $role = $user ? $user->role : session('user_role'); 
                                    @endphp
                                    @if($role === 'manager_departemen' && $leave->status === 'menunggu_manager')
                                        <form action="{{ route('backoffice.leaves.approve', $leave->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm cursor-pointer active:scale-95">Setujui</button>
                                        </form>
                                        <button onclick="document.getElementById('rejectModal-{{ $leave->id }}').classList.remove('hidden')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm cursor-pointer active:scale-95">Tolak</button>
                                    @elseif(in_array($role, ['hr_manager', 'hr_admin_manager', 'hr_training_manager']) && $leave->status === 'menunggu_hr')
                                        <form action="{{ route('backoffice.leaves.approve', $leave->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm cursor-pointer active:scale-95">Setujui</button>
                                        </form>
                                        <button onclick="document.getElementById('rejectModal-{{ $leave->id }}').classList.remove('hidden')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm cursor-pointer active:scale-95">Tolak</button>
                                    @elseif($role === 'super_admin' && in_array($leave->status, ['menunggu_super_admin', 'menunggu_hr']))
                                        <form action="{{ route('backoffice.leaves.approve', $leave->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm cursor-pointer active:scale-95">Setujui</button>
                                        </form>
                                        <button onclick="document.getElementById('rejectModal-{{ $leave->id }}').classList.remove('hidden')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm cursor-pointer active:scale-95">Tolak</button>
                                    @else
                                        <span class="text-slate-400 text-xs">Selesai</span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Tolak -->
                        <div id="rejectModal-{{ $leave->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6 animate-modal-pop">
                                <h4 class="text-base sm:text-lg font-bold text-slate-800 mb-2">Tolak Pengajuan Cuti / Izin</h4>
                                <p class="text-xs text-slate-500 mb-4">Berikan alasan penolakan untuk disampaikan kepada pemohon.</p>
                                <form action="{{ route('backoffice.leaves.reject', $leave->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alasan Penolakan <span class="text-error">*</span></label>
                                        <textarea name="alasan_penolakan" required class="w-full border border-slate-300 rounded-xl p-3 text-xs sm:text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20" rows="3" placeholder="Contoh: Jadwal operasional divisi sedang padat..."></textarea>
                                    </div>
                                    <div class="flex justify-end gap-2.5">
                                        <button type="button" onclick="document.getElementById('rejectModal-{{ $leave->id }}').classList.add('hidden')" class="px-4 py-2 text-xs sm:text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl cursor-pointer">Batal</button>
                                        <button type="submit" class="px-4 py-2 text-xs sm:text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl cursor-pointer shadow">Tolak Cuti</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada pengajuan cuti yang perlu diproses.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

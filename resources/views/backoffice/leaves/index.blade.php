@extends('layouts.admin')

@section('page_title', 'Persetujuan Cuti')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-outline-variant p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-title-lg font-bold text-on-surface">Daftar Pengajuan Cuti</h3>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-y border-outline-variant">
                        <th class="py-3 px-4 font-bold text-label-lg text-on-surface">Karyawan</th>
                        <th class="py-3 px-4 font-bold text-label-lg text-on-surface">Departemen</th>
                        <th class="py-3 px-4 font-bold text-label-lg text-on-surface">Tipe</th>
                        <th class="py-3 px-4 font-bold text-label-lg text-on-surface">Tanggal</th>
                        <th class="py-3 px-4 font-bold text-label-lg text-on-surface">Keterangan</th>
                        <th class="py-3 px-4 font-bold text-label-lg text-on-surface">Status</th>
                        <th class="py-3 px-4 font-bold text-label-lg text-on-surface text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($leaves as $leave)
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-3 px-4 text-body-md text-on-surface font-medium">{{ $leave->employee->nama_lengkap ?? '-' }}</td>
                            <td class="py-3 px-4 text-body-md text-on-surface-variant">{{ $leave->employee->department->nama_department ?? '-' }}</td>
                            <td class="py-3 px-4 text-body-md text-on-surface-variant capitalize">{{ $leave->tipe }}</td>
                            <td class="py-3 px-4 text-body-md text-on-surface-variant">
                                {{ \Carbon\Carbon::parse($leave->tanggal_mulai)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($leave->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td class="py-3 px-4 text-body-md text-on-surface-variant">
                                {{ \Illuminate\Support\Str::limit($leave->keterangan, 30) }}
                                @if($leave->dokumen_pendukung)
                                    <br>
                                    <a href="{{ asset('storage/' . $leave->dokumen_pendukung) }}" target="_blank" class="text-primary hover:underline text-sm">Lihat Dokumen</a>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($leave->status === 'menunggu_manager')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Menunggu Manager
                                    </span>
                                @elseif($leave->status === 'menunggu_hr')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Menunggu HR
                                    </span>
                                @elseif($leave->status === 'disetujui')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800" title="{{ $leave->alasan_penolakan }}">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex justify-center gap-2">
                                    @php $role = session('user_role'); @endphp
                                    @if($role === 'manager_departemen' && $leave->status === 'menunggu_manager')
                                        <form action="{{ route('backoffice.leaves.approve', $leave->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">Setujui</button>
                                        </form>
                                        <button onclick="document.getElementById('rejectModal-{{ $leave->id }}').classList.remove('hidden')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition-colors">Tolak</button>
                                    @elseif(in_array($role, ['hr_manager', 'hr_admin_manager', 'hr_training_manager', 'superadmin']) && $leave->status === 'menunggu_hr')
                                        <form action="{{ route('backoffice.leaves.approve', $leave->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">Setujui</button>
                                        </form>
                                        <button onclick="document.getElementById('rejectModal-{{ $leave->id }}').classList.remove('hidden')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition-colors">Tolak</button>
                                    @else
                                        <span class="text-gray-400 text-sm">Tidak ada aksi</span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Tolak -->
                        <div id="rejectModal-{{ $leave->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                                <h4 class="text-lg font-bold mb-4">Tolak Pengajuan Cuti</h4>
                                <form action="{{ route('backoffice.leaves.reject', $leave->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Alasan Penolakan</label>
                                        <textarea name="alasan_penolakan" required class="w-full border-gray-300 rounded-lg shadow-sm" rows="3"></textarea>
                                    </div>
                                    <div class="flex justify-end gap-3">
                                        <button type="button" onclick="document.getElementById('rejectModal-{{ $leave->id }}').classList.add('hidden')" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg">Batal</button>
                                        <button type="submit" class="px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-lg">Tolak Cuti</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-on-surface-variant">Belum ada pengajuan cuti.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

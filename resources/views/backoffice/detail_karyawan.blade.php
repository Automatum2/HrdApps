@extends('layouts.admin')

@section('title', 'Detail Profil Karyawan - HRDApps')
@section('page_title', 'Detail Profil Karyawan')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-1">
            <a class="hover:text-primary transition-colors" href="{{ $back_route }}">Kelola Karyawan</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary font-semibold">Profil Karyawan</span>
        </nav>
        <p class="text-on-surface-variant text-sm">Informasi lengkap profil dan data pribadi karyawan perusahaan.</p>
    </div>
    <a href="{{ $back_route }}" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-sm px-4 py-2 rounded-lg flex items-center gap-2 transition-all shadow-sm">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        <span>Kembali</span>
    </a>
</div>

<!-- Header Card -->
<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden mb-6 p-6 flex flex-col md:flex-row items-center gap-6">
    <div class="w-24 h-24 rounded-full bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center shrink-0">
        @if($employee->foto)
            <img src="{{ asset('storage/' . $employee->foto) }}" alt="{{ $employee->nama_lengkap }}" class="w-full h-full object-cover">
        @else
            <span class="material-symbols-outlined text-5xl text-slate-400">person</span>
        @endif
    </div>
    <div class="flex-1 text-center md:text-left">
        <h2 class="text-2xl font-bold text-slate-800">{{ $employee->nama_lengkap }}</h2>
        <p class="text-slate-500 font-medium mt-1">{{ $employee->nik }} • {{ $employee->status_kerja ? ucfirst($employee->status_kerja) : 'Belum Ditentukan' }}</p>
        
        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mt-3">
            <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                <span class="material-symbols-outlined text-[14px]">corporate_fare</span>
                Departemen: Belum Ditempatkan
            </span>
            <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                <span class="material-symbols-outlined text-[14px]">badge</span>
                Jabatan: Belum Ditentukan
            </span>
            <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $employee->status === 'aktif' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-slate-100 text-slate-500 border-slate-200' }} border px-3 py-1 rounded-full">
                <span class="material-symbols-outlined text-[14px]">fiber_manual_record</span>
                Status: {{ strtoupper($employee->status) }}
            </span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Informasi Pribadi -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-outline-variant bg-slate-50 flex items-center gap-2">
            <span class="material-symbols-outlined text-slate-400">person_book</span>
            <h3 class="font-bold text-slate-800 text-base">Informasi Pribadi</h3>
        </div>
        <div class="p-6 divide-y divide-slate-100">
            <div class="grid grid-cols-3 py-3 first:pt-0 last:pb-0">
                <div class="text-sm font-semibold text-slate-500">Nama Lengkap</div>
                <div class="col-span-2 text-sm font-bold text-slate-800">{{ $employee->nama_lengkap }}</div>
            </div>
            <div class="grid grid-cols-3 py-3 first:pt-0 last:pb-0">
                <div class="text-sm font-semibold text-slate-500">Tempat, Tanggal Lahir</div>
                <div class="col-span-2 text-sm font-medium text-slate-700">
                    {{ $employee->tempat_lahir ?: '-' }}, 
                    {{ $employee->tanggal_lahir ? \Carbon\Carbon::parse($employee->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                </div>
            </div>
            <div class="grid grid-cols-3 py-3 first:pt-0 last:pb-0">
                <div class="text-sm font-semibold text-slate-500">Jenis Kelamin</div>
                <div class="col-span-2 text-sm font-medium text-slate-700">{{ $employee->jenis_kelamin ? ($employee->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan') : '-' }}</div>
            </div>
            <div class="grid grid-cols-3 py-3 first:pt-0 last:pb-0">
                <div class="text-sm font-semibold text-slate-500">Alamat Lengkap</div>
                <div class="col-span-2 text-sm font-medium text-slate-700">{{ $employee->alamat ?: '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Informasi Kontak & Perbankan -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-outline-variant bg-slate-50 flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-400">contact_mail</span>
                <h3 class="font-bold text-slate-800 text-base">Informasi Kontak</h3>
            </div>
            <div class="p-6 divide-y divide-slate-100">
                <div class="grid grid-cols-3 py-3 first:pt-0 last:pb-0">
                    <div class="text-sm font-semibold text-slate-500">Alamat Email</div>
                    <div class="col-span-2 text-sm font-medium text-slate-700">{{ $employee->email ?: '-' }}</div>
                </div>
                <div class="grid grid-cols-3 py-3 first:pt-0 last:pb-0">
                    <div class="text-sm font-semibold text-slate-500">No. Telepon/WA</div>
                    <div class="col-span-2 text-sm font-medium text-slate-700">{{ $employee->no_telepon ?: '-' }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-outline-variant bg-slate-50 flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-400">account_balance</span>
                <h3 class="font-bold text-slate-800 text-base">Informasi Perbankan</h3>
            </div>
            <div class="p-6 divide-y divide-slate-100">
                <div class="grid grid-cols-3 py-3 first:pt-0 last:pb-0">
                    <div class="text-sm font-semibold text-slate-500">Gaji Pokok</div>
                    <div class="col-span-2 text-sm font-mono font-bold text-slate-800">Rp {{ number_format($employee->gaji_pokok, 0, ',', '.') }}</div>
                </div>
                <div class="grid grid-cols-3 py-3 first:pt-0 last:pb-0">
                    <div class="text-sm font-semibold text-slate-500">Nama Bank</div>
                    <div class="col-span-2 text-sm font-medium text-slate-700">{{ $employee->nama_bank ?: '-' }}</div>
                </div>
                <div class="grid grid-cols-3 py-3 first:pt-0 last:pb-0">
                    <div class="text-sm font-semibold text-slate-500">No. Rekening</div>
                    <div class="col-span-2 text-sm font-mono text-slate-700">{{ $employee->no_rekening ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dokumen Pendukung & CV -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <!-- Riwayat Hidup / CV -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden h-fit">
        <div class="px-6 py-4 border-b border-outline-variant bg-slate-50 flex items-center gap-2">
            <span class="material-symbols-outlined text-slate-400">description</span>
            <h3 class="font-bold text-slate-800 text-base">Riwayat Hidup (CV)</h3>
        </div>
        <div class="p-6">
            @if(isset($employee->cv_text) && $employee->cv_text)
                <div class="text-sm text-slate-700 leading-relaxed">{!! $employee->cv_text !!}</div>
            @else
                <div class="text-sm text-slate-500 italic text-center py-4">Belum ada data riwayat hidup.</div>
            @endif
        </div>
    </div>

    <!-- Dokumen -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden h-fit">
        <div class="px-6 py-4 border-b border-outline-variant bg-slate-50 flex items-center gap-2">
            <span class="material-symbols-outlined text-slate-400">folder</span>
            <h3 class="font-bold text-slate-800 text-base">Dokumen Pendukung</h3>
        </div>
        <div class="p-6">
            @if($employee->documents && $employee->documents->count() > 0)
                <ul class="divide-y divide-slate-100">
                    @foreach($employee->documents as $doc)
                        <li class="py-3 flex items-center justify-between first:pt-0 last:pb-0">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 shrink-0">
                                    <span class="material-symbols-outlined">{{ $doc->file_type === 'pdf' ? 'picture_as_pdf' : 'image' }}</span>
                                </div>
                                <div class="truncate">
                                    <p class="text-sm font-semibold text-slate-800 truncate" style="max-width: 150px;">{{ $doc->file_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $doc->file_size }} KB • {{ strtoupper($doc->file_type) }}</p>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="p-2 text-primary hover:bg-primary-container rounded-lg transition-colors cursor-pointer shrink-0" title="Lihat Dokumen">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-sm text-slate-500 italic text-center py-4">Belum ada dokumen yang diunggah.</div>
            @endif
        </div>
    </div>
</div>
@endsection

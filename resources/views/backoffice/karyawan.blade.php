@extends('layouts.admin')

@section('title', 'Manajemen Karyawan - HRDApps')
@section('page_title', 'Manajemen Karyawan')

@section('content')
<!-- Header & Breadcrumbs -->
<div class="flex flex-col md:flex-row md:items-end justify-between mb-6">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-1">
            <a class="hover:text-primary transition-colors" href="{{ route('backoffice.dashboard') }}">Beranda</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary font-semibold">Manajemen Karyawan</span>
        </nav>
        <p class="text-body-sm text-on-surface-variant">Kelola daftar penempatan, tugas, dan detail departemen karyawan Anda.</p>
    </div>
    <div class="flex items-center gap-3 mt-4 md:mt-0">
        @if(session('user_role') !== 'manager_departemen')
        <button class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-lg font-semibold text-sm shadow hover:brightness-110 active:scale-95 transition-all cursor-pointer" id="btn-open-modal">
            <span class="material-symbols-outlined text-lg">add</span>
            <span>Tambah Karyawan</span>
        </button>
        @endif
        <a href="{{ route('backoffice.karyawan.export') }}" class="flex items-center gap-2 px-4 py-2.5 bg-surface-container-lowest border border-outline-variant text-secondary rounded-lg font-semibold text-sm hover:bg-surface-container-low active:scale-95 transition-all cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">download</span>
            <span>Export Excel</span>
        </a>
    </div>
</div>

<!-- Bento Filter & List Container -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-sm overflow-hidden flex flex-col">
    <!-- Filter Section -->
    <div class="p-4 sm:p-6 border-b border-outline-variant bg-surface-bright flex flex-col md:flex-row gap-3 sm:gap-4 justify-between items-stretch md:items-center">
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-center w-full md:w-auto">
            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <label class="text-xs uppercase font-bold text-on-surface-variant whitespace-nowrap">Filter Status:</label>
                <select class="form-select bg-white border border-outline-variant rounded-xl text-xs sm:text-sm py-2 px-3 focus:ring-primary focus:border-primary w-full sm:w-48 transition-all text-on-surface" id="filter-status" onchange="window.location.href='{{ route('backoffice.karyawan') }}?status=' + this.value">
                    <option value="">Semua Karyawan</option>
                    <optgroup label="Status Akun">
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Hanya Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Hanya Nonaktif</option>
                    </optgroup>
                    <optgroup label="Status Kerja">
                        <option value="tetap" {{ request('status') === 'tetap' ? 'selected' : '' }}>Karyawan Tetap</option>
                        <option value="kontrak" {{ request('status') === 'kontrak' ? 'selected' : '' }}>Karyawan Kontrak</option>
                        <option value="magang" {{ request('status') === 'magang' ? 'selected' : '' }}>Magang (Intern)</option>
                        <option value="musiman" {{ request('status') === 'musiman' ? 'selected' : '' }}>Musiman</option>
                        <option value="harian" {{ request('status') === 'harian' ? 'selected' : '' }}>Harian (DW)</option>
                        <option value="tenaga_lepas" {{ request('status') === 'tenaga_lepas' ? 'selected' : '' }}>Tenaga Lepas</option>
                    </optgroup>
                </select>
            </div>
        </div>
        <div class="relative w-full md:w-80 group">
            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors text-lg">search</span>
            <input class="pl-10 pr-4 py-2 bg-white border border-outline-variant rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-primary/20 w-full transition-all outline-none text-on-surface" placeholder="Cari Nama atau NIK..." type="text" id="search-karyawan">
        </div>
    </div>
    
    <div class="p-4 bg-surface-container-low border-b border-outline-variant flex justify-between items-center">
        <p class="text-xs text-on-surface-variant font-medium">Menampilkan <span class="font-bold text-on-surface" id="showing-range">{{ $employees->firstItem() ?? 0 }}-{{ $employees->lastItem() ?? 0 }}</span> dari <span class="font-bold text-on-surface" id="total-entries-top">{{ $employees->total() }}</span> karyawan</p>
    </div>
    
    <!-- Table Content -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-surface-container text-on-surface uppercase tracking-wider font-semibold text-xs border-b border-outline-variant">
                    <th class="px-6 py-4 w-12 text-center">No</th>
                    <th class="px-6 py-4">Foto</th>
                    <th class="px-6 py-4">Nama Lengkap</th>
                    <th class="px-6 py-4">NIK</th>
                    <th class="px-6 py-4">Jabatan</th>
                    <th class="px-6 py-4">Departemen</th>
                    <th class="px-6 py-4">Status Kerja</th>
                    <th class="px-6 py-4">Status Akun</th>
                    @if(session('user_role') !== 'manager_departemen')
                    <th class="px-6 py-4 text-center">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10 font-body-sm text-body-sm" id="table-karyawan-body">
                @forelse($employees as $index => $emp)
                <tr class="{{ $emp->status === 'nonaktif' ? 'bg-slate-50 opacity-60 grayscale' : 'hover:bg-primary/5 group' }} transition-colors" data-nik="{{ $emp->nik }}" data-jabatan="{{ $emp->position->nama ?? 'Staff' }}" data-dept="{{ $emp->department->nama_department ?? '-' }}" data-status="{{ $emp->status_kerja ?? 'Tetap' }}">
                    <td class="px-6 py-4 text-center text-on-surface font-semibold font-mono">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        @if($emp->foto)
                            <img alt="{{ $emp->nama_lengkap }}" class="w-10 h-10 rounded-full border-2 border-surface-container shadow-sm object-cover" src="{{ asset('storage/' . $emp->foto) }}">
                        @else
                            <div class="w-10 h-10 rounded-full border-2 border-surface-container shadow-sm overflow-hidden bg-primary/5 flex items-center justify-center font-bold text-primary">
                                {{ strtoupper(substr($emp->nama_lengkap ?? 'U', 0, 2)) }}
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-bold {{ $emp->status === 'nonaktif' ? 'text-slate-400' : 'text-on-surface' }}">{{ $emp->nama_lengkap }}</td>
                    <td class="px-6 py-4 font-mono text-on-surface-variant text-sm">{{ $emp->nik }}</td>
                    <td class="px-6 py-4">
                        <div class="text-on-surface-variant">{{ $emp->position->nama_jabatan ?? ($emp->position->nama ?? 'Staff') }}</div>
                    </td>
                    <td class="px-6 py-4"><span class="px-2.5 py-1 bg-secondary-container/30 text-secondary rounded-full font-semibold text-xs uppercase">{{ $emp->department->nama_department ?? '-' }}</span></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ strtolower($emp->status_kerja ?? '') == 'kontrak' ? 'bg-surface-container-highest text-secondary' : (strtolower($emp->status_kerja ?? '') == 'magang' ? 'bg-tertiary-container/10 text-tertiary' : 'bg-primary-container/10 text-primary') }}">
                            {{ ucfirst($emp->status_kerja ?? 'Tetap') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($emp->status === 'nonaktif')
                            <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-500 border border-slate-200 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                <span>Nonaktif</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-200 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                                <span>Aktif</span>
                            </span>
                        @endif
                    </td>
                    @if(session('user_role') !== 'manager_departemen')
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('backoffice.karyawan.show', $emp->id) }}" class="w-8 h-8 flex items-center justify-center rounded bg-primary-container text-on-primary-container hover:brightness-110 active:scale-90 transition-all shadow-sm cursor-pointer" title="Detail / Edit">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                            </a>
                            <button class="btn-lepas w-8 h-8 flex items-center justify-center rounded bg-error text-white hover:brightness-110 active:scale-90 transition-all shadow-sm cursor-pointer" title="Lepas dari Departemen" data-nama="{{ $emp->nama_lengkap }}" data-nik="{{ $emp->nik }}">
                                <span class="material-symbols-outlined text-sm">person_remove</span>
                            </button>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ session('user_role') !== 'manager_departemen' ? '9' : '8' }}" class="px-6 py-8 text-center text-slate-500">
                        <span class="material-symbols-outlined text-4xl mb-2 text-outline">group_off</span>
                        <p>Belum ada data karyawan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination Footer -->
    <div class="p-6 border-t border-outline-variant bg-surface-bright flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-xs text-on-surface-variant">Menampilkan <span class="font-bold text-on-surface" id="showing-count-footer">{{ $employees->firstItem() ?? 0 }} - {{ $employees->lastItem() ?? 0 }}</span> karyawan dari total <span class="font-bold text-on-surface" id="total-count-footer">{{ $employees->total() }}</span></p>
        <div>
            {{ $employees->links() }}
        </div>
    </div>
</div>

<!-- Stats Overview (Bento Rekap Cards) -->
<div class="mt-4 sm:mt-6 grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-6">
    <!-- Card 1: Total Staff -->
    <div class="p-4 sm:p-6 bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-sm flex items-center gap-3 sm:gap-4">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
            <span class="material-symbols-outlined text-xl sm:text-2xl">group</span>
        </div>
        <div>
            <p class="text-[10px] sm:text-xs uppercase font-bold text-on-surface-variant tracking-wider">Total Staff</p>
            <p class="text-xl sm:text-headline-md font-bold text-on-surface" id="widget-total-staff">{{ $stats['total_staff'] ?? 0 }}</p>
        </div>
    </div>
    <!-- Card 2: Aktif -->
    <div class="p-4 sm:p-6 bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-sm flex items-center gap-3 sm:gap-4">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-tertiary/10 flex items-center justify-center text-tertiary shrink-0">
            <span class="material-symbols-outlined text-xl sm:text-2xl">how_to_reg</span>
        </div>
        <div>
            <p class="text-[10px] sm:text-xs uppercase font-bold text-on-surface-variant tracking-wider">Aktif</p>
            <p class="text-xl sm:text-headline-md font-bold text-on-surface" id="widget-aktif">{{ $stats['aktif'] ?? 0 }}</p>
        </div>
    </div>
    <!-- Card 3: Cuti/Off -->
    <div class="p-4 sm:p-6 bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-sm flex items-center gap-3 sm:gap-4">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-error/10 flex items-center justify-center text-error shrink-0">
            <span class="material-symbols-outlined text-xl sm:text-2xl">person_off</span>
        </div>
        <div>
            <p class="text-[10px] sm:text-xs uppercase font-bold text-on-surface-variant tracking-wider">Cuti/Izin Hari Ini</p>
            <p class="text-xl sm:text-headline-md font-bold text-on-surface" id="widget-cuti">{{ $stats['cuti'] ?? 0 }}</p>
        </div>
    </div>
    <!-- Card 4: Baru Bulan Ini -->
    <div class="p-4 sm:p-6 bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-sm flex items-center gap-3 sm:gap-4">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-secondary-container/30 flex items-center justify-center text-secondary shrink-0">
            <span class="material-symbols-outlined text-xl sm:text-2xl">new_releases</span>
        </div>
        <div>
            <p class="text-[10px] sm:text-xs uppercase font-bold text-on-surface-variant tracking-wider">Baru (Bulan Ini)</p>
            <p class="text-xl sm:text-headline-md font-bold text-on-surface" id="widget-baru">{{ $stats['baru'] ?? 0 }}</p>
        </div>
    </div>
</div>
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
                        <th class="font-table-header text-table-header px-lg py-3 uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10 font-body-sm text-body-sm" id="modal-table-body">
                    @forelse($unassigned_employees as $unassigned)
                    <tr class="hover:bg-primary/5 transition-colors group" data-nik="{{ $unassigned->nik }}" data-nama="{{ $unassigned->nama_lengkap }}" data-jabatan="Staff" data-dept="-" data-status="{{ $unassigned->status_kerja ?? 'Tetap' }}">
                        <td class="px-lg py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/5 flex items-center justify-center text-xs font-bold text-primary/70">
                                    {{ strtoupper(substr($unassigned->nama_lengkap ?? 'U', 0, 2)) }}
                                </div>
                                <span class="font-bold text-on-surface">{{ $unassigned->nama_lengkap }}</span>
                            </div>
                        </td>
                        <td class="px-lg py-3 font-mono text-on-surface-variant">{{ $unassigned->nik }}</td>
                        <td class="px-lg py-3 text-right">
                            <div class="inline-flex items-center gap-1.5 justify-end">
                                <button class="btn-assign bg-primary hover:bg-primary-container text-white px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer active:scale-95 transition-all">
                                    + Pilih
                                </button>
                                <button type="button" onclick="hapusKaryawanDuplikat('{{ $unassigned->id }}', '{{ addslashes($unassigned->nama_lengkap) }}')" class="p-1.5 text-red-500 hover:bg-red-50 hover:text-red-700 rounded-lg transition-colors cursor-pointer" title="Hapus Data Duplikat / Tidak Dipakai">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <!-- Kosong ditangani oleh elemen empty state di bawah -->
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
            Apakah Anda yakin ingin mengeluarkan <span class="font-bold text-on-surface" id="konfirmasi-nama">Nama Karyawan</span> (<span class="font-mono text-[12px]" id="konfirmasi-nik">NIK</span>) dari departemennya? Statusnya akan kembali menjadi "Belum Ditempatkan".
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
                        <option value="Musiman">Musiman</option>
                        <option value="Harian (DW)">Harian (DW)</option>
                        <option value="Tidak Tetap">Tidak Tetap</option>
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
<script>
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
    
    // Input Pencarian & Filter
    const searchKaryawan = document.getElementById('search-karyawan');
    const filterStatus = document.getElementById('filter-status');
    const searchModalKaryawan = document.getElementById('search-modal-karyawan');
    
    // Badan Tabel & Stat
    const tableKaryawanBody = document.getElementById('table-karyawan-body');
    const modalTableBody = document.getElementById('modal-table-body');
    const modalEmptyState = document.getElementById('modal-empty-state');
    
    // Widget Rekap
    const widgetTotalStaff = document.getElementById('widget-total-staff');
    const widgetAktif = document.getElementById('widget-aktif');
    const widgetCuti = document.getElementById('widget-cuti');
    const widgetBaru = document.getElementById('widget-baru');
    
    // Label Teks Atas & Bawah
    const showingRange = document.getElementById('showing-range');
    const totalEntriesTop = document.getElementById('total-entries-top');
    const showingCountFooter = document.getElementById('showing-count-footer');
    const totalCountFooter = document.getElementById('total-count-footer');
    
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
    // 2. Pencarian & Filter Real-Time Utama
    // ==========================================
    const filterTableUtama = () => {
        const query = searchKaryawan.value.toLowerCase().trim();
        const statusValue = filterStatus.value;
        const rows = tableKaryawanBody.querySelectorAll('tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const nama = row.querySelector('td:nth-child(3)').innerText.toLowerCase();
            const nik = row.getAttribute('data-nik').toLowerCase();
            const statusKerja = row.getAttribute('data-status');
            
            const matchQuery = nama.includes(query) || nik.includes(query);
            const matchStatus = !statusValue || (statusKerja && statusKerja.toLowerCase().includes(statusValue.toLowerCase()));
            
            if (matchQuery && matchStatus) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });
        
        // Perbarui nomor urut visual (hanya baris yang terlihat)
        let index = 1;
        rows.forEach(row => {
            if (!row.classList.contains('hidden')) {
                row.querySelector('td:first-child').innerText = index++;
            }
        });
        
        // Update info perolehan teks
        showingRange.innerText = visibleCount === 0 ? '0' : `1-${visibleCount}`;
        showingCountFooter.innerText = visibleCount;
    };
    
    searchKaryawan.addEventListener('input', filterTableUtama);
    filterStatus.addEventListener('change', filterTableUtama);

    // ==========================================
    // 3. Pencarian Karyawan Baru pada Modal
    // ==========================================
    if (searchModalKaryawan) {
        searchModalKaryawan.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            filterModalKaryawan(query);
        });
    }

    function filterModalKaryawan(query) {
        const rows = modalTableBody ? modalTableBody.querySelectorAll('tr') : [];
        let matchCount = 0;
        
        rows.forEach(row => {
            const nama = (row.getAttribute('data-nama') || '').toLowerCase();
            const nik = (row.getAttribute('data-nik') || '').toLowerCase();
            
            if (nama.includes(query) || nik.includes(query)) {
                row.classList.remove('hidden');
                matchCount++;
            } else {
                row.classList.add('hidden');
            }
        });
        
        if (modalEmptyState) {
            if (matchCount === 0 && rows.length > 0) {
                modalEmptyState.classList.remove('hidden');
            } else {
                modalEmptyState.classList.add('hidden');
            }
        }
    }

    // ==========================================
    // 4. Aksi Tambah / Pilih Karyawan Baru
    // ==========================================
    if (modalTableBody) {
        modalTableBody.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-assign')) {
                const row = e.target.closest('tr');
                const nama = row.getAttribute('data-nama');
                const nik = row.getAttribute('data-nik');
                
                activeAssignData = { row, nama, nik };
                if (assignNamaKaryawan) assignNamaKaryawan.innerText = nama;
                
                // Tutup modal pilihan awal
                tutupModalTambah();
                
                // Buka modal detail penempatan
                if (modalAssignDetail) {
                    modalAssignDetail.style.display = 'flex';
                    const inpJabatan = document.getElementById('assign-jabatan');
                    if (inpJabatan) inpJabatan.focus();
                }
            }
        });
    }
    
    const tutupModalAssignDetail = () => {
        if (modalAssignDetail) modalAssignDetail.style.display = 'none';
        if (formAssignDetail) formAssignDetail.reset();
        activeAssignData = null;
    };
    
    if (btnCloseAssignModal) btnCloseAssignModal.addEventListener('click', tutupModalAssignDetail);
    if (btnCancelAssignModal) btnCancelAssignModal.addEventListener('click', tutupModalAssignDetail);
    
    if (formAssignDetail) {
        formAssignDetail.addEventListener('submit', (e) => {
            e.preventDefault();
            if (activeAssignData) {
                const nik = activeAssignData.nik;
                const jabatan = document.getElementById('assign-jabatan').value;
                const status = document.getElementById('assign-status').value;
                const dept = document.getElementById('assign-departemen').value;
                
                // Buat form submission permanen
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("backoffice.karyawan.assign") }}';
                
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

                const deptInput = document.createElement('input');
                deptInput.type = 'hidden';
                deptInput.name = 'departemen';
                deptInput.value = dept;

                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = status;
                
                form.appendChild(csrfToken);
                form.appendChild(nikInput);
                form.appendChild(jabatanInput);
                form.appendChild(deptInput);
                form.appendChild(statusInput);
                document.body.appendChild(form);
                
                const btnSubmit = formAssignDetail.querySelector('button[type="submit"]');
                if(btnSubmit) {
                    btnSubmit.disabled = true;
                    btnSubmit.innerText = 'Memproses...';
                }
                
                form.submit();
            }
        });
    }

    // ==========================================
    // 5. Aksi Lepas Karyawan (Trigger Konfirmasi)
    // ==========================================
    if (tableKaryawanBody) {
        tableKaryawanBody.addEventListener('click', (e) => {
            const btnLepas = e.target.closest('.btn-lepas');
            if (btnLepas) {
                const nama = btnLepas.getAttribute('data-nama');
                const nik = btnLepas.getAttribute('data-nik');
                
                targetNikToRelease = nik;
                
                const konfNama = document.getElementById('konfirmasi-nama');
                const konfNik = document.getElementById('konfirmasi-nik');
                if (konfNama) konfNama.innerText = nama;
                if (konfNik) konfNik.innerText = nik;
                
                if (modalKonfirmasi) modalKonfirmasi.style.display = 'flex';
            }
        });
    }
    
    // Batal Konfirmasi
    if (btnKonfirmasiBatal) {
        btnKonfirmasiBatal.addEventListener('click', () => {
            if (modalKonfirmasi) modalKonfirmasi.style.display = 'none';
            targetRowToRelease = null;
            targetNikToRelease = '';
        });
    }
    
    // Setuju Konfirmasi (Proses Lepas secara Realtime dan Permanen)
    if (btnKonfirmasiLepas) {
        btnKonfirmasiLepas.addEventListener('click', () => {
            if (targetNikToRelease) {
                // Buat form untuk submit aksi lepas secara permanen
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("backoffice.karyawan.lepas") }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const nikInput = document.createElement('input');
                nikInput.type = 'hidden';
                nikInput.name = 'nik';
                nikInput.value = targetNikToRelease;
                
                form.appendChild(csrfToken);
                form.appendChild(nikInput);
                document.body.appendChild(form);
                
                btnKonfirmasiLepas.disabled = true;
                btnKonfirmasiLepas.innerText = 'Memproses...';
                
                form.submit();
            }
        });
    }

    window.hapusKaryawanDuplikat = function(id, nama) {
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

@extends('layouts.admin')

@section('title', 'Kelola HR Manager - HRDApps')
@section('page_title', 'Kelola HR Manager')

@section('content')

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative flex items-center gap-2">
        <span class="material-symbols-outlined text-green-500">check_circle</span>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative flex items-center gap-2">
        <span class="material-symbols-outlined text-red-500">error</span>
        <span class="text-sm font-medium">{{ session('error') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative">
        <div class="flex items-center gap-2 mb-1">
            <span class="material-symbols-outlined text-red-500">error</span>
            <span class="text-sm font-bold">Terjadi Kesalahan:</span>
        </div>
        <ul class="list-disc list-inside text-sm pl-6">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-1">
            <a class="hover:text-primary transition-colors" href="{{ route('backoffice.dashboard') }}">Beranda</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary font-semibold">Kelola HR Manager</span>
        </nav>
        <p class="text-on-surface-variant text-sm max-w-2xl">Manajemen akun administrator HRD untuk setiap departemen dan unit operasional.</p>
    </div>
    <button class="bg-[#0066ff] hover:bg-blue-700 text-white font-semibold text-sm px-4 py-2.5 rounded-lg flex items-center gap-2 transition-all shadow active:scale-95 whitespace-nowrap cursor-pointer" id="btn-tambah-hr">
        <span class="material-symbols-outlined text-[18px]">add</span>
        <span>Tambah HR Manager</span>
    </button>
</div>

<!-- Data Table Card -->
<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
    <!-- Table Toolbar -->
    <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-slate-50/50">
        <div class="flex items-center gap-3">
            <div class="font-bold text-on-surface text-base">Daftar Manager Aktif</div>
            <span class="bg-primary/10 text-primary font-semibold px-3 py-0.5 rounded-full text-xs" id="total-count-badge">{{ $managers->count() }} Total</span>
        </div>
        <button class="flex items-center gap-1.5 text-on-surface-variant hover:text-primary transition-colors text-xs font-bold border border-outline-variant px-3 py-2 rounded-lg bg-white cursor-pointer hover:bg-slate-50">
            <span class="material-symbols-outlined text-[18px]">filter_list</span>
            <span>Filter</span>
        </button>
    </div>
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 border-b border-outline-variant text-slate-500 uppercase tracking-wider text-xs font-bold">
                    <th class="py-4 px-6 w-16">NO</th>
                    <th class="py-4 px-6">NAMA</th>
                    <th class="py-4 px-6">EMAIL</th>
                    <th class="py-4 px-6">JABATAN</th>
                    <th class="py-4 px-6 w-32">STATUS</th>
                    <th class="py-4 px-6 w-32 text-right">AKSI</th>
                </tr>
            </thead>
            <tbody class="text-sm font-medium text-slate-700 divide-y divide-slate-100" id="hr-table-body">
                @forelse($managers as $index => $manager)
                <tr class="{{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'bg-slate-50 opacity-60 grayscale' : 'hover:bg-slate-50 transition-colors group' }}">
                    <td class="py-4 px-6 text-on-surface-variant">{{ $index + 1 }}</td>
                    <td class="py-4 px-6 font-bold {{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-800' }}">{{ $manager->employee ? $manager->employee->nama_lengkap : $manager->username }}</td>
                    <td class="py-4 px-6 {{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-600' }}">{{ $manager->email }}</td>
                    <td class="py-4 px-6 {{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-600' }}">{{ $manager->employee && $manager->employee->position ? $manager->employee->position->nama_jabatan : 'HR Manager' }}</td>
                    <td class="py-4 px-6" id="status-col-{{ $manager->id }}">
                        @if($manager->employee && $manager->employee->status === 'nonaktif')
                            <span class="status-badge inline-flex items-center gap-1.5 bg-slate-100 text-slate-500 border border-slate-200 px-2.5 py-1 rounded-full text-[11px] leading-none uppercase tracking-wide">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                <span>Nonaktif</span>
                            </span>
                        @else
                            <span class="status-badge inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-200 px-2.5 py-1 rounded-full text-[11px] leading-none uppercase tracking-wide">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                                <span>Aktif</span>
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end gap-2 {{ $manager->employee && $manager->employee->status === 'nonaktif' ? '' : 'opacity-0 group-hover:opacity-100' }} transition-opacity">
                            <button class="btn-edit-hr w-8 h-8 rounded border {{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'border-slate-200 text-slate-300 cursor-not-allowed flex items-center justify-center' : 'border-outline-variant flex items-center justify-center text-slate-500 hover:text-primary hover:border-primary transition-colors bg-white cursor-pointer' }}" title="Edit" data-id="{{ $manager->id }}" data-nama="{{ $manager->employee ? $manager->employee->nama_lengkap : '' }}" data-email="{{ $manager->email }}" data-nik="{{ $manager->employee ? $manager->employee->nik : '' }}" {{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'disabled' : '' }}>
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <button class="btn-delete-hr w-8 h-8 rounded border {{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'border-slate-200 text-slate-300 cursor-not-allowed flex items-center justify-center' : 'border-outline-variant flex items-center justify-center text-slate-500 hover:text-error hover:border-error transition-colors bg-white cursor-pointer' }}" title="Hapus / Nonaktifkan" data-id="{{ $manager->id }}" data-nama="{{ $manager->employee ? $manager->employee->nama_lengkap : $manager->username }}" {{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'disabled' : '' }}>
                                <span class="material-symbols-outlined text-[18px]">person_off</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-4xl text-slate-300">group_off</span>
                            <p>Belum ada data HR Manager.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Table Pagination/Footer -->
    <div class="p-6 border-t border-outline-variant bg-white">
        {{ $managers->links() }}
    </div>
</div>
@endsection

@push('modals')
<!-- MODAL: Tambah HR Manager Baru -->
<div class="bg-slate-900/60 backdrop-blur-sm" id="modal-tambah-hr" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 16px;">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant flex flex-col max-h-[90vh] overflow-hidden animate-modal-pop" style="width: 100%; max-width: 480px; min-width: 280px; display: flex; flex-direction: column;">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-slate-50">
            <div>
                <h3 class="font-bold text-slate-800 text-base" id="modal-title">Tambah HR Manager</h3>
                <p class="text-xs text-slate-500 mt-1" id="modal-subtitle">Daftarkan akun administrator HRD yang baru.</p>
            </div>
            <button class="p-1 hover:bg-slate-200 rounded-full text-slate-400 cursor-pointer" id="btn-close-modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <!-- Form Content -->
        <form id="form-hr-manager" action="{{ route('backoffice.super_admin.kelola_hr.store') }}" method="POST" class="p-6 space-y-4 overflow-y-auto">
            @csrf
            <!-- NIK Field -->
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="nik">NIK Manager</label>
                <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="nik" name="nik" placeholder="Contoh: 14785236" type="text" required>
            </div>
            
            <!-- Nama Field -->
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="nama">Nama Lengkap</label>
                <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="nama" name="nama" placeholder="Contoh: Rina Wijaya" type="text" required>
            </div>
            
            <!-- Email Field -->
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="email">Alamat Email</label>
                <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="email" name="email" placeholder="Contoh: rina.w@hrdapps.co.id" type="email" required>
            </div>
            
            <!-- Jabatan Field -->
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="jabatan">Jabatan</label>
                <div class="relative">
                    <select class="w-full appearance-none bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 cursor-pointer" id="jabatan" name="jabatan">
                        @forelse($positions as $pos)
                            <option value="{{ $pos->nama_jabatan }}">{{ $pos->nama_jabatan }}</option>
                        @empty
                            <option value="HR Manager">HR Manager</option>
                        @endforelse
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                </div>
            </div>
        </form>
        
        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-outline-variant bg-slate-50 flex justify-end gap-3">
            <button type="button" class="border border-slate-300 hover:bg-slate-100 text-slate-600 px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all" id="btn-cancel-modal">
                Batal
            </button>
            <button type="submit" form="form-hr-manager" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all">
                Simpan HR Manager
            </button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnTambahHr = document.getElementById('btn-tambah-hr');
        const modalTambahHr = document.getElementById('modal-tambah-hr');
        const btnCloseModal = document.getElementById('btn-close-modal');
        const btnCancelModal = document.getElementById('btn-cancel-modal');
        const formHrManager = document.getElementById('form-hr-manager');
        const hrTableBody = document.getElementById('hr-table-body');
        const totalCountBadge = document.getElementById('total-count-badge');
        const showingEntriesInfo = document.getElementById('showing-entries-info');
        
        // 1. Tampilkan / Tutup Modal
        btnTambahHr.addEventListener('click', () => {
            document.getElementById('modal-title').innerText = 'Tambah HR Manager';
            document.getElementById('modal-subtitle').innerText = 'Daftarkan akun administrator HRD yang baru.';
            formHrManager.action = "{{ route('backoffice.super_admin.kelola_hr.store') }}";
            formHrManager.innerHTML = `
                @csrf
                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="nik">NIK Manager</label>
                    <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="nik" name="nik" placeholder="Contoh: 14785236" type="text" required>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="nama">Nama Lengkap</label>
                    <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="nama" name="nama" placeholder="Contoh: Rina Wijaya" type="text" required>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="email">Alamat Email</label>
                    <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="email" name="email" placeholder="Contoh: rina.w@hrdapps.co.id" type="email" required>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="jabatan">Jabatan</label>
                    <div class="relative">
                        <select class="w-full appearance-none bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 cursor-pointer" id="jabatan" name="jabatan">
                            @forelse($positions as $pos)
                                <option value="{{ $pos->nama_jabatan }}">{{ $pos->nama_jabatan }}</option>
                            @empty
                                <option value="HR Manager">HR Manager</option>
                            @endforelse
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                    </div>
                </div>
            `;
            modalTambahHr.style.display = 'flex';
        });
        
        const tutupModal = () => {
            modalTambahHr.style.display = 'none';
        };
        
        btnCloseModal.addEventListener('click', tutupModal);
        btnCancelModal.addEventListener('click', tutupModal);
        
        // 2. Edit HR Manager
        hrTableBody.addEventListener('click', (e) => {
            const btnEdit = e.target.closest('.btn-edit-hr');
            if (btnEdit && !btnEdit.disabled) {
                const id = btnEdit.getAttribute('data-id');
                const nama = btnEdit.getAttribute('data-nama');
                const email = btnEdit.getAttribute('data-email');
                const nik = btnEdit.getAttribute('data-nik');
                
                document.getElementById('modal-title').innerText = 'Edit HR Manager';
                document.getElementById('modal-subtitle').innerText = 'Perbarui data administrator HRD.';
                formHrManager.action = `/backoffice/super-admin/kelola-hr/${id}`;
                formHrManager.innerHTML = `
                    @csrf
                    @method('PUT')
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="nik">NIK Manager</label>
                        <input class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-500" id="nik" type="text" value="${nik}" disabled>
                        <p class="text-[10px] text-slate-400 mt-1">NIK tidak dapat diubah.</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="nama">Nama Lengkap</label>
                        <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="nama" name="nama" value="${nama}" type="text" required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="email">Alamat Email</label>
                        <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="email" name="email" value="${email}" type="email" required>
                    </div>
                `;
                
                modalTambahHr.style.display = 'flex';
            }
        });

        
        // 3. Hapus / Nonaktifkan HR Manager
        hrTableBody.addEventListener('click', (e) => {
            const btnDelete = e.target.closest('.btn-delete-hr');
            if (btnDelete && !btnDelete.disabled) {
                const id = btnDelete.getAttribute('data-id');
                const nama = btnDelete.getAttribute('data-nama');
                
                if (confirm(`Apakah Anda yakin ingin menonaktifkan HR Manager "${nama}"? Akun ini tidak akan dapat login lagi.`)) {
                    // Create hidden form to submit DELETE request
                    const deleteForm = document.createElement('form');
                    deleteForm.method = 'POST';
                    deleteForm.action = `/backoffice/super-admin/kelola-hr/${id}`;
                    deleteForm.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(deleteForm);
                    deleteForm.submit();
                }
            }
        });
    });
</script>
@endpush

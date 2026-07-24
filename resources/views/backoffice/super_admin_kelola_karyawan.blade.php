@extends('layouts.admin')

@section('title', 'Kelola Karyawan - HRDApps')
@section('page_title', 'Kelola Karyawan')

@section('content')
<!-- Page Header -->
@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
    <span class="block sm:inline font-semibold">{{ session('success') }}</span>
</div>
@endif
@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
    <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
            <li class="font-semibold">{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-1">
            <a class="hover:text-primary transition-colors" href="{{ route('backoffice.dashboard') }}">Beranda</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary font-semibold">Kelola Karyawan</span>
        </nav>
        <p class="text-on-surface-variant text-sm">Supervisori seluruh data karyawan, riwayat jabatan, departemen, dan gaji pokok.</p>
    </div>
    <button class="bg-[#0066ff] hover:bg-blue-700 text-white font-semibold text-sm px-4 py-2.5 rounded-lg flex items-center gap-2 transition-all shadow active:scale-95 whitespace-nowrap cursor-pointer" id="btn-tambah-karyawan">
        <span class="material-symbols-outlined text-[18px]">add</span>
        <span>Tambah Karyawan</span>
    </button>
</div>

<!-- Data Table Card -->
<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
    <!-- Table Toolbar -->
    <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-slate-50/50">
        <div class="flex items-center gap-3">
            <div class="font-bold text-on-surface text-base">Daftar Karyawan Global</div>
            <span class="bg-primary/10 text-primary font-semibold px-3 py-0.5 rounded-full text-xs" id="total-karyawan-badge">{{ $employees->count() }} Total</span>
        </div>
        <div class="relative focus-within:ring-2 focus-within:ring-primary/20 rounded-lg">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
            <input class="pl-10 pr-4 py-2 bg-white border border-slate-300 rounded-lg text-xs outline-none focus:border-primary w-48 sm:w-64 transition-all text-slate-800 placeholder:text-slate-400" placeholder="Cari nama atau NIK..." type="text" id="search-karyawan">
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 border-b border-outline-variant text-slate-500 uppercase tracking-wider text-xs font-bold">
                    <th class="py-4 px-6 w-16">No</th>
                    <th class="py-4 px-6">Nama</th>
                    <th class="py-4 px-6">Email</th>
                    <th class="py-4 px-6">Jabatan</th>
                    <th class="py-4 px-6">Departemen</th>
                    <th class="py-4 px-6">Gaji Pokok</th>
                    <th class="py-4 px-6 w-32">Status</th>
                    <th class="py-4 px-6 w-32 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm font-medium text-slate-700 divide-y divide-slate-100" id="karyawan-table-body">
                @forelse($employees as $index => $k)
                <tr class="{{ $k->status === 'nonaktif' ? 'bg-slate-50 opacity-60 grayscale' : 'hover:bg-slate-50 group transition-colors' }}" data-nama="{{ $k->nama_lengkap }}">
                    <td class="py-4 px-6 text-on-surface-variant">{{ $index + 1 }}</td>
                    <td class="py-4 px-6 font-bold {{ $k->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-800' }}">{{ $k->nama_lengkap }}</td>
                    <td class="py-4 px-6 {{ $k->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-600' }}">{{ $k->email }}</td>
                    <td class="py-4 px-6 {{ $k->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-600' }}">{{ $k->position ? $k->position->nama_jabatan : 'Belum Ditentukan' }}</td>
                    <td class="py-4 px-6 {{ $k->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-600' }}">{{ $k->department ? $k->department->nama_department : 'Belum Ditempatkan' }}</td>
                    <td class="py-4 px-6 font-mono {{ $k->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-600' }}">Rp {{ number_format($k->gaji_pokok, 0, ',', '.') }}</td>
                    <td class="py-4 px-6" id="status-k-{{ $k->nik }}">
                        <span class="status-badge-k inline-flex items-center {{ $k->status === 'aktif' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-slate-100 text-slate-500 border-slate-200' }} border px-2 py-0.5 rounded-full text-[10px] uppercase font-bold tracking-wide">
                            {{ $k->status }}
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            @if($k->status === 'nonaktif')
                                <button class="w-8 h-8 rounded border border-slate-200 text-slate-300 cursor-not-allowed flex items-center justify-center" title="Detail" disabled>
                                    <span class="material-symbols-outlined text-[16px]">search</span>
                                </button>
                            @else
                                <a href="{{ route('backoffice.super_admin.kelola_karyawan.show', $k->id) }}" class="w-8 h-8 rounded border border-outline-variant text-slate-500 hover:bg-slate-50 hover:text-primary transition-colors flex items-center justify-center cursor-pointer" title="Detail">
                                    <span class="material-symbols-outlined text-[16px]">search</span>
                                </a>
                            @endif
                            <button class="btn-edit-karyawan w-8 h-8 rounded border {{ $k->status === 'nonaktif' ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-outline-variant text-slate-500 hover:bg-slate-50 hover:text-primary transition-colors cursor-pointer' }} flex items-center justify-center" title="Edit" data-id="{{ $k->id }}" data-nama="{{ $k->nama_lengkap }}" data-email="{{ $k->email }}" data-gaji="{{ $k->gaji_pokok }}" {{ $k->status === 'nonaktif' ? 'disabled' : '' }}>
                                <span class="material-symbols-outlined text-[16px]">edit</span>
                            </button>
                            <button class="btn-delete-karyawan w-8 h-8 rounded border {{ $k->status === 'nonaktif' ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-red-200 text-red-500 hover:bg-red-50 transition-colors cursor-pointer' }} flex items-center justify-center" title="Hapus / Nonaktifkan" data-id="{{ $k->id }}" data-nama="{{ $k->nama_lengkap }}" {{ $k->status === 'nonaktif' ? 'disabled' : '' }}>
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-6 text-slate-500 italic">Belum ada karyawan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <div class="p-6 border-t border-outline-variant bg-white">
        {{ $employees->links() }}
    </div>
</div>
@endsection

@push('modals')
<!-- MODAL: Tambah Karyawan Baru -->
<div class="bg-slate-900/60 backdrop-blur-sm" id="modal-tambah-karyawan" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 16px;">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant flex flex-col max-h-[90vh] overflow-hidden animate-modal-pop" style="width: 100%; max-width: 480px; min-width: 280px; display: flex; flex-direction: column;">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-slate-50">
            <div>
                <h3 class="font-bold text-slate-800 text-base">Tambah Karyawan</h3>
                <p class="text-xs text-slate-500 mt-1">Daftarkan profil karyawan baru ke database perusahaan.</p>
            </div>
            <button class="p-1 hover:bg-slate-200 rounded-full text-slate-400 cursor-pointer" id="btn-close-modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <!-- Form Content -->
        <form id="form-karyawan" class="p-6 space-y-4 overflow-y-auto" method="POST" action="{{ route('backoffice.super_admin.kelola_karyawan.store') }}">
            @csrf
            <!-- Nama Field -->
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="nama">Nama Lengkap</label>
                <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="nama" name="nama" placeholder="Contoh: Adi Saputra" type="text" required>
            </div>
            
            <!-- Email Field -->
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="email">Alamat Email</label>
                <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="email" name="email" placeholder="Contoh: adi.s@email.com" type="email" required>
            </div>

            <!-- Gaji Pokok Field -->
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="gaji">Gaji Pokok</label>
                <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 font-mono" id="gaji" name="gaji" placeholder="Contoh: 8500000" type="number" required>
            </div>
        </form>
        
        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-outline-variant bg-slate-50 flex justify-end gap-3">
            <button type="button" class="border border-slate-300 hover:bg-slate-100 text-slate-600 px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all" id="btn-cancel-modal">
                Batal
            </button>
            <button type="submit" form="form-karyawan" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all">
                Simpan
            </button>
        </div>
    </div>
</div>

<!-- Form tersembunyi untuk Delete Karyawan -->
<form id="form-delete-karyawan" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- MODAL: Dialog Konfirmasi Hapus/Nonaktifkan -->
<div class="bg-slate-900/60 backdrop-blur-sm" id="modal-delete-confirm" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 16px;">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant p-6 text-center animate-modal-pop" style="width: 100%; max-width: 400px; min-width: 280px; display: flex; flex-direction: column; align-items: center;">
        <div class="w-14 h-14 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-3xl">delete</span>
        </div>
        <h3 class="font-bold text-slate-800 text-lg mb-2">Hapus Karyawan?</h3>
        <p class="text-sm text-slate-500 mb-6 leading-relaxed">
            Apakah Anda yakin ingin menonaktifkan karyawan <span class="font-bold text-slate-800" id="delete-karyawan-nama">Nama</span>? Akun karyawan tersebut akan diubah statusnya menjadi **Nonaktif**.
        </p>
        <div class="flex gap-3 justify-center w-full">
            <button class="flex-1 border border-slate-300 hover:bg-slate-50 text-slate-600 py-2.5 rounded-lg text-sm font-semibold cursor-pointer transition-all" id="btn-delete-cancel">
                Batal
            </button>
            <button class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-lg text-sm font-semibold cursor-pointer transition-all" id="btn-delete-confirm-act">
                Ya, Nonaktifkan
            </button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnTambahKaryawan = document.getElementById('btn-tambah-karyawan');
        const modalTambahKaryawan = document.getElementById('modal-tambah-karyawan');
        const btnCloseModal = document.getElementById('btn-close-modal');
        const btnCancelModal = document.getElementById('btn-cancel-modal');
        const formKaryawan = document.getElementById('form-karyawan');
        const karyawanTableBody = document.getElementById('karyawan-table-body');
        const totalKaryawanBadge = document.getElementById('total-karyawan-badge');
        const showingRangeInfo = document.getElementById('showing-range-info');
        const searchKaryawan = document.getElementById('search-karyawan');
        
        // Modal Delete
        const modalDeleteConfirm = document.getElementById('modal-delete-confirm');
        const btnDeleteCancel = document.getElementById('btn-delete-cancel');
        const btnDeleteConfirmAct = document.getElementById('btn-delete-confirm-act');
        const deleteKaryawanNama = document.getElementById('delete-karyawan-nama');
        
        let activeDeleteId = null;
        
        // 1. Tampilkan / Tutup Modal
        btnTambahKaryawan.addEventListener('click', () => {
            document.getElementById('modal-tambah-karyawan').querySelector('h3').innerText = 'Tambah Karyawan';
            formKaryawan.action = "{{ route('backoffice.super_admin.kelola_karyawan.store') }}";
            formKaryawan.innerHTML = `
                @csrf
                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="nama">Nama Lengkap</label>
                    <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="nama" name="nama" type="text" required>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="email">Alamat Email</label>
                    <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="email" name="email" type="email" required>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="gaji">Gaji Pokok</label>
                    <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 font-mono" id="gaji" name="gaji" type="number" required>
                </div>
            `;
            modalTambahKaryawan.style.display = 'flex';
        });
        
        const tutupModal = () => {
            modalTambahKaryawan.style.display = 'none';
        };
        
        btnCloseModal.addEventListener('click', tutupModal);
        btnCancelModal.addEventListener('click', tutupModal);
        
        karyawanTableBody.addEventListener('click', (e) => {
            const btnEdit = e.target.closest('.btn-edit-karyawan');
            if (btnEdit) {
                const id = btnEdit.getAttribute('data-id');
                const nama = btnEdit.getAttribute('data-nama');
                const email = btnEdit.getAttribute('data-email');
                const gaji = btnEdit.getAttribute('data-gaji');

                document.getElementById('modal-tambah-karyawan').querySelector('h3').innerText = 'Edit Karyawan';
                formKaryawan.action = `/backoffice/super-admin/kelola-karyawan/${id}`;
                formKaryawan.innerHTML = `
                    @csrf
                    @method('PUT')
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="nama">Nama Lengkap</label>
                        <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="nama" name="nama" value="${nama}" type="text" required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="email">Alamat Email</label>
                        <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="email" name="email" value="${email}" type="email" required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="gaji">Gaji Pokok</label>
                        <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 font-mono" id="gaji" name="gaji" value="${gaji}" type="number" required>
                    </div>
                `;
                modalTambahKaryawan.style.display = 'flex';
            }
        });
        
        // Removed LS mockup
        
        // 3. Logika Filter Pencarian
        searchKaryawan.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const rows = karyawanTableBody.querySelectorAll('tr');
            
            rows.forEach(row => {
                const nama = row.getAttribute('data-nama').toLowerCase();
                if (nama.includes(query)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        });
        
        // 4. Trigger Hapus (Modal Konfirmasi)
        karyawanTableBody.addEventListener('click', (e) => {
            const btnDelete = e.target.closest('.btn-delete-karyawan');
            if (btnDelete) {
                const id = btnDelete.getAttribute('data-id');
                const nama = btnDelete.getAttribute('data-nama');
                
                activeDeleteId = id;
                deleteKaryawanNama.innerText = nama;
                
                modalDeleteConfirm.style.display = 'flex';
            }
        });
        
        btnDeleteCancel.addEventListener('click', () => {
            modalDeleteConfirm.style.display = 'none';
            activeDeleteId = null;
        });
        
        btnDeleteConfirmAct.addEventListener('click', () => {
            if (activeDeleteId) {
                const formDelete = document.getElementById('form-delete-karyawan');
                formDelete.action = `/backoffice/super-admin/kelola-karyawan/${activeDeleteId}`;
                formDelete.submit();
            }
        });
    });
</script>
@endpush

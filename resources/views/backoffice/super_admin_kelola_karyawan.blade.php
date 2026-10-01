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
                    <th class="py-4 px-6">NIK</th>
                    <th class="py-4 px-6">Nama Lengkap</th>
                    <th class="py-4 px-6">Email</th>
                    <th class="py-4 px-6">Jabatan</th>
                    <th class="py-4 px-6">Departemen</th>
                    <th class="py-4 px-6">Gaji Pokok</th>
                    <th class="py-4 px-6 w-32">Status</th>
                    <th class="py-4 px-6 w-36 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm font-medium text-slate-700 divide-y divide-slate-100" id="karyawan-table-body">
                @forelse($employees as $index => $k)
                <tr class="{{ $k->status === 'nonaktif' ? 'bg-slate-50 opacity-60 grayscale' : 'hover:bg-slate-50 group transition-colors' }}" data-nama="{{ $k->nama_lengkap }}" data-nik="{{ $k->nik }}" data-email="{{ $k->email }}">
                    <td class="py-4 px-6 text-on-surface-variant">{{ $index + 1 }}</td>
                    <td class="py-4 px-6 font-mono font-bold text-xs {{ $k->status === 'nonaktif' ? 'text-slate-400' : 'text-primary' }}">{{ $k->nik }}</td>
                    <td class="py-4 px-6 font-bold {{ $k->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-800' }}">{{ $k->nama_lengkap }}</td>
                    <td class="py-4 px-6 {{ $k->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-600' }}">{{ $k->email }}</td>
                    <td class="py-4 px-6 {{ $k->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-600' }}">{{ $k->position ? $k->position->nama_jabatan : 'Belum Ditentukan' }}</td>
                    <td class="py-4 px-6 {{ $k->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-600' }}">
                        @if($k->department)
                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-xs font-semibold">{{ $k->department->nama_department }}</span>
                        @else
                            <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 text-xs font-semibold">Belum Ditempatkan</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 font-mono {{ $k->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-600' }}">Rp {{ number_format($k->gaji_pokok, 0, ',', '.') }}</td>
                    <td class="py-4 px-6" id="status-k-{{ $k->nik }}">
                        @if($k->status === 'nonaktif')
                            <span class="status-badge-k inline-flex items-center gap-1.5 bg-slate-100 text-slate-500 border border-slate-200 px-2.5 py-1 rounded-full text-[11px] leading-none uppercase tracking-wide font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                <span>Nonaktif</span>
                            </span>
                        @else
                            <span class="status-badge-k inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-200 px-2.5 py-1 rounded-full text-[11px] leading-none uppercase tracking-wide font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                                <span>Aktif</span>
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-1.5">
                            @if($k->status === 'nonaktif')
                                <button class="w-8 h-8 rounded border border-slate-200 text-slate-300 cursor-not-allowed flex items-center justify-center" title="Detail" disabled>
                                    <span class="material-symbols-outlined text-[16px]">search</span>
                                </button>
                            @else
                                <a href="{{ route('backoffice.super_admin.kelola_karyawan.show', $k->id) }}" class="w-8 h-8 rounded border border-outline-variant text-slate-500 hover:bg-slate-50 hover:text-primary transition-colors flex items-center justify-center cursor-pointer" title="Detail">
                                    <span class="material-symbols-outlined text-[16px]">search</span>
                                </a>
                            @endif
                            <button class="btn-edit-karyawan w-8 h-8 rounded border {{ $k->status === 'nonaktif' ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-outline-variant text-slate-500 hover:bg-slate-50 hover:text-primary transition-colors cursor-pointer' }} flex items-center justify-center" title="Edit" data-id="{{ $k->id }}" data-nama="{{ $k->nama_lengkap }}" data-email="{{ $k->email }}" data-gaji="{{ $k->gaji_pokok }}" data-department="{{ $k->department_id }}" data-position="{{ $k->position_id }}" data-status_kerja="{{ $k->status_kerja }}" {{ $k->status === 'nonaktif' ? 'disabled' : '' }}>
                                <span class="material-symbols-outlined text-[16px]">edit</span>
                            </button>
                            
                            @if($k->status === 'aktif')
                                <button class="btn-delete-karyawan w-8 h-8 rounded border border-slate-200 text-amber-600 hover:bg-amber-50 transition-colors cursor-pointer flex items-center justify-center" title="Nonaktifkan Karyawan" data-id="{{ $k->id }}" data-nama="{{ $k->nama_lengkap }}">
                                    <span class="material-symbols-outlined text-[16px]">person_off</span>
                                </button>
                            @else
                                <button class="btn-force-delete-karyawan w-8 h-8 rounded border border-red-200 text-red-600 hover:bg-red-50 transition-colors cursor-pointer flex items-center justify-center" title="Hapus Permanen (Data Duplikat/Tidak Dipakai)" data-id="{{ $k->id }}" data-nama="{{ $k->nama_lengkap }}" data-nik="{{ $k->nik }}">
                                    <span class="material-symbols-outlined text-[16px]">delete_forever</span>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-6 text-slate-500 italic">Belum ada karyawan</td>
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
        
        <!-- Form Content (diisi via JS pada mode Edit) -->
        <form id="form-karyawan" class="p-6 space-y-4 overflow-y-auto" method="POST">
            @csrf
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
        <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-3xl">person_off</span>
        </div>
        <h3 class="font-bold text-slate-800 text-lg mb-2">Nonaktifkan Karyawan?</h3>
        <p class="text-sm text-slate-500 mb-6 leading-relaxed">
            Apakah Anda yakin ingin menonaktifkan karyawan <span class="font-bold text-slate-800" id="delete-karyawan-nama">Nama</span>? Akun karyawan tersebut akan diubah statusnya menjadi **Nonaktif**.
        </p>
        <div class="flex gap-3 justify-center w-full">
            <button class="flex-1 border border-slate-300 hover:bg-slate-50 text-slate-600 py-2.5 rounded-lg text-sm font-semibold cursor-pointer transition-all" id="btn-delete-cancel">
                Batal
            </button>
            <button class="flex-1 bg-amber-600 hover:bg-amber-700 text-white py-2.5 rounded-lg text-sm font-semibold cursor-pointer transition-all" id="btn-delete-confirm-act">
                Ya, Nonaktifkan
            </button>
        </div>
    </div>
</div>

<!-- MODAL: Dialog Konfirmasi Hapus Permanen (Hard Delete) -->
<div class="bg-slate-900/60 backdrop-blur-sm" id="modal-force-delete-confirm" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 16px;">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant p-6 text-center animate-modal-pop" style="width: 100%; max-width: 420px; min-width: 280px; display: flex; flex-direction: column; align-items: center;">
        <div class="w-14 h-14 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-3xl">delete_forever</span>
        </div>
        <h3 class="font-bold text-slate-800 text-lg mb-2">Hapus Permanen Karyawan?</h3>
        <p class="text-sm text-slate-500 mb-6 leading-relaxed">
            Apakah Anda yakin ingin <strong class="text-red-600">menghapus permanen</strong> data <span class="font-bold text-slate-800" id="force-delete-nama">Nama</span> (<span class="font-mono text-xs font-bold" id="force-delete-nik">NIK</span>)? Tindakan ini akan menghapus akun login dan seluruh data pelamar secara permanen dan tidak dapat dibatalkan.
        </p>
        <form id="form-force-delete-karyawan" method="POST" class="w-full">
            @csrf
            @method('DELETE')
            <div class="flex gap-3 justify-center w-full">
                <button type="button" class="flex-1 border border-slate-300 hover:bg-slate-50 text-slate-600 py-2.5 rounded-lg text-sm font-semibold cursor-pointer transition-all" onclick="document.getElementById('modal-force-delete-confirm').style.display='none'">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-lg text-sm font-bold cursor-pointer transition-all shadow">
                    Ya, Hapus Permanen
                </button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
    window.departments = @json($departments);
    window.positions = @json($positions);
    document.addEventListener('DOMContentLoaded', () => {
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

                const department = btnEdit.getAttribute('data-department');
                const position = btnEdit.getAttribute('data-position');
                const statusKerja = btnEdit.getAttribute('data-status_kerja');

                let depOptions = '<option value="">-- Pilih Departemen --</option>';
                window.departments.forEach(d => {
                    depOptions += `<option value="${d.id}" ${department == d.id ? 'selected' : ''}>${d.nama_department}</option>`;
                });

                let posOptions = '<option value="">-- Pilih Jabatan --</option>';
                window.positions.forEach(p => {
                    posOptions += `<option value="${p.id}" ${position == p.id ? 'selected' : ''}>${p.nama_jabatan}</option>`;
                });

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
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="department_id">Departemen</label>
                        <select class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="department_id" name="department_id">
                            ${depOptions}
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="position_id">Jabatan</label>
                        <select class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="position_id" name="position_id">
                            ${posOptions}
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="status_kerja">Status Kerja</label>
                        <select class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="status_kerja" name="status_kerja">
                            <option value="tetap" ${statusKerja == 'tetap' ? 'selected' : ''}>Karyawan Tetap</option>
                            <option value="kontrak" ${statusKerja == 'kontrak' ? 'selected' : ''}>Karyawan Kontrak</option>
                            <option value="harian" ${statusKerja == 'harian' ? 'selected' : ''}>Karyawan Harian</option>
                            <option value="tenaga_lepas" ${statusKerja == 'tenaga_lepas' ? 'selected' : ''}>Tenaga Lepas</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="gaji_pokok">Gaji Pokok (Rp)</label>
                        <input class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" id="gaji_pokok" name="gaji_pokok" value="${gaji}" type="number" min="0" step="1000">
                    </div>
                `;
                modalTambahKaryawan.style.display = 'flex';
            }
        });
        
        // Removed LS mockup
        
        // 3. Logika Filter Pencarian (Nama & NIK)
        searchKaryawan.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const rows = karyawanTableBody.querySelectorAll('tr');
            
            rows.forEach(row => {
                const nama = (row.getAttribute('data-nama') || '').toLowerCase();
                const nik = (row.getAttribute('data-nik') || '').toLowerCase();
                const email = (row.getAttribute('data-email') || '').toLowerCase();
                if (nama.includes(query) || nik.includes(query) || email.includes(query)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        });
        
        // 4. Trigger Nonaktifkan (Modal Konfirmasi)
        karyawanTableBody.addEventListener('click', (e) => {
            const btnDelete = e.target.closest('.btn-delete-karyawan');
            if (btnDelete) {
                const id = btnDelete.getAttribute('data-id');
                const nama = btnDelete.getAttribute('data-nama');
                
                activeDeleteId = id;
                deleteKaryawanNama.innerText = nama;
                
                modalDeleteConfirm.style.display = 'flex';
            }

            // Trigger Hard Delete
            const btnForceDelete = e.target.closest('.btn-force-delete-karyawan');
            if (btnForceDelete) {
                const id = btnForceDelete.getAttribute('data-id');
                const nama = btnForceDelete.getAttribute('data-nama');
                const nik = btnForceDelete.getAttribute('data-nik');

                document.getElementById('force-delete-nama').innerText = nama;
                document.getElementById('force-delete-nik').innerText = nik;
                document.getElementById('form-force-delete-karyawan').action = `/backoffice/super-admin/kelola-karyawan/${id}/force-delete`;
                document.getElementById('modal-force-delete-confirm').style.display = 'flex';
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

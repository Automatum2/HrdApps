@extends('layouts.admin')

@section('title', 'Kelola Manager - HRDApps')
@section('page_title', 'Kelola Manager')

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
            <span class="text-primary font-semibold">Kelola Manager</span>
        </nav>
        <p class="text-on-surface-variant text-sm max-w-2xl">Manajemen akun administrator HRD dan Manager Departemen.</p>
    </div>
    <button class="bg-[#0066ff] hover:bg-blue-700 text-white font-semibold text-sm px-4 py-2.5 rounded-lg flex items-center gap-2 transition-all shadow active:scale-95 whitespace-nowrap cursor-pointer" id="btn-tambah-hr">
        <span class="material-symbols-outlined text-[18px]">add</span>
        <span>Tambah Manager</span>
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
        <div class="relative focus-within:ring-2 focus-within:ring-primary/20 rounded-lg">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
            <input class="pl-10 pr-4 py-2 bg-white border border-slate-300 rounded-lg text-xs outline-none focus:border-primary w-48 sm:w-64 transition-all text-slate-800 placeholder:text-slate-400" placeholder="Cari nama atau email..." type="text" id="search-hr">
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
                    <th class="py-4 px-6">Peran</th>
                    <th class="py-4 px-6">Departemen</th>
                    <th class="py-4 px-6 w-32">Status</th>
                    <th class="py-4 px-6 w-32 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm font-medium text-slate-700 divide-y divide-slate-100" id="hr-table-body">
                @forelse($managers as $index => $manager)
                <tr class="{{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'bg-slate-50 opacity-60 grayscale' : 'hover:bg-slate-50 transition-colors group' }}">
                    <td class="py-4 px-6 text-on-surface-variant">{{ $index + 1 }}</td>
                    <td class="py-4 px-6 font-bold {{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-800' }}">{{ $manager->employee ? $manager->employee->nama_lengkap : $manager->username }}</td>
                    <td class="py-4 px-6 {{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-600' }}">{{ $manager->email }}</td>
                    <td class="py-4 px-6 font-bold {{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-600' }}">
                        {{ $manager->role === 'hr_manager' ? 'HR Manager' : 'Manager Departemen' }}
                    </td>
                    <td class="py-4 px-6 {{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'text-slate-400' : 'text-slate-600' }}">
                        {{ $manager->employee && $manager->employee->department ? $manager->employee->department->nama_department : '-' }}
                    </td>
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
                            <a href="{{ $manager->employee ? route('backoffice.karyawan.show', $manager->employee->id) : '#' }}" class="w-8 h-8 rounded border {{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'border-slate-200 text-slate-300 pointer-events-none flex items-center justify-center' : 'border-outline-variant flex items-center justify-center text-slate-500 hover:text-primary hover:border-primary transition-colors bg-white' }}" title="Detail">
                                <span class="material-symbols-outlined text-[18px]">search</span>
                            </a>
                            <button class="btn-edit-hr w-8 h-8 rounded border {{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'border-slate-200 text-slate-300 cursor-not-allowed flex items-center justify-center' : 'border-outline-variant flex items-center justify-center text-slate-500 hover:text-primary hover:border-primary transition-colors bg-white cursor-pointer' }}" title="Edit" data-id="{{ $manager->id }}" data-nama="{{ $manager->employee ? $manager->employee->nama_lengkap : '' }}" data-email="{{ $manager->email }}" data-nik="{{ $manager->employee ? $manager->employee->nik : '' }}" data-role="{{ $manager->role }}" data-dept-id="{{ $manager->employee ? $manager->employee->department_id : '' }}" {{ $manager->employee && $manager->employee->status === 'nonaktif' ? 'disabled' : '' }}>
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
                            <p>Belum ada data Manager.</p>
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
<!-- MODAL: Tambah Manager -->
<div class="bg-slate-900/60 backdrop-blur-sm" id="modal-tambah-hr" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 16px;">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant flex flex-col max-h-[90vh] overflow-hidden animate-modal-pop" style="width: 100%; max-width: 520px; min-width: 280px; display: flex; flex-direction: column;">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-slate-50">
            <div>
                <h3 class="font-bold text-slate-800 text-base" id="modal-title">Tambah Manager</h3>
                <p class="text-xs text-slate-500 mt-1" id="modal-subtitle">Promosikan karyawan internal atau daftarkan manager eksternal.</p>
            </div>
            <button class="p-1 hover:bg-slate-200 rounded-full text-slate-400 cursor-pointer" id="btn-close-modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <!-- Form Content (diisi via JS: tab Promosi / Eksternal / Edit) -->
        <form id="form-hr-manager" action="{{ route('backoffice.super_admin.kelola_hr.store') }}" method="POST" class="p-6 space-y-4 overflow-y-auto">
            @csrf
        </form>
        
        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-outline-variant bg-slate-50 flex justify-end gap-3">
            <button type="button" class="border border-slate-300 hover:bg-slate-100 text-slate-600 px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all" id="btn-cancel-modal">
                Batal
            </button>
            <button type="submit" form="form-hr-manager" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all">
                Simpan Manager
            </button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    window.promotableEmployees = @json($employees);

    document.addEventListener('DOMContentLoaded', () => {
        const btnTambahHr = document.getElementById('btn-tambah-hr');
        const modalTambahHr = document.getElementById('modal-tambah-hr');
        const btnCloseModal = document.getElementById('btn-close-modal');
        const btnCancelModal = document.getElementById('btn-cancel-modal');
        const formHrManager = document.getElementById('form-hr-manager');
        const hrTableBody = document.getElementById('hr-table-body');
        const totalCountBadge = document.getElementById('total-count-badge');

        const empOptionsHtml = window.promotableEmployees.length
            ? window.promotableEmployees.map(e => `<option value="${e.id}" data-nama="${e.nama_lengkap}" data-nik="${e.nik}">${e.nama_lengkap} (${e.nik}) &mdash; ${e.email}</option>`).join('')
            : '<option value="" disabled>Tidak ada karyawan yang dapat dipromosikan</option>';

        const buildAddForm = (activeTab) => `
            @csrf
            <input type="hidden" name="type" id="form-type" value="${activeTab}">

            <!-- Tabs -->
            <div class="flex bg-slate-100 rounded-lg p-1 gap-1">
                <button type="button" class="manager-tab-btn flex-1 py-2.5 rounded-lg text-xs transition-all cursor-pointer ${activeTab === 'promosi' ? 'bg-white text-primary shadow font-bold' : 'text-slate-500 hover:text-slate-700 font-semibold'}" data-tab="promosi">
                    <span class="flex items-center justify-center gap-1.5"><span class="material-symbols-outlined text-base">upgrade</span> Promosi Internal</span>
                </button>
                <button type="button" class="manager-tab-btn flex-1 py-2.5 rounded-lg text-xs transition-all cursor-pointer ${activeTab === 'eksternal' ? 'bg-white text-primary shadow font-bold' : 'text-slate-500 hover:text-slate-700 font-semibold'}" data-tab="eksternal">
                    <span class="flex items-center justify-center gap-1.5"><span class="material-symbols-outlined text-base">person_add</span> Manager Eksternal</span>
                </button>
            </div>

            <!-- TAB 1: PROMOSI KARYAWAN INTERNAL -->
            <div class="space-y-4 tab-panel ${activeTab === 'promosi' ? '' : 'hidden'}" id="tab-promosi">
                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="employee_id">Pilih Karyawan (Email / NIK)</label>
                    <div class="relative">
                        <select class="w-full appearance-none bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 cursor-pointer" id="employee_id" name="employee_id" required>
                            <option value="">-- Pilih Karyawan --</option>
                            ${empOptionsHtml}
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="promosi-nik">NIK (Terisi Otomatis)</label>
                        <input class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-500" id="promosi-nik" type="text" readonly tabindex="-1">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="promosi-nama">Nama Lengkap (Terisi Otomatis)</label>
                        <input class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-500" id="promosi-nama" type="text" readonly tabindex="-1">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="role-promosi">Peran Baru</label>
                    <div class="relative">
                        <select class="w-full appearance-none bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 cursor-pointer" id="role-promosi" name="role" onchange="document.getElementById('dept-promosi').style.display = this.value === 'manager_departemen' ? 'block' : 'none'">
                            <option value="manager_departemen">Manager Departemen</option>
                            <option value="hr_manager">HR Manager</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                    </div>
                </div>
                <div class="space-y-1" id="dept-promosi">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="department_id_promosi">Departemen</label>
                    <div class="relative">
                        <select class="w-full appearance-none bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 cursor-pointer" id="department_id_promosi" name="department_id">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_department }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="jabatan-promosi">Jabatan Manager</label>
                    <div class="relative">
                        <select class="w-full appearance-none bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 cursor-pointer" id="jabatan-promosi" name="jabatan" required>
                            @forelse($positions as $pos)
                            <option value="{{ $pos->nama_jabatan }}">{{ $pos->nama_jabatan }}</option>
                            @empty
                            <option value="Manager">Manager</option>
                            @endforelse
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                    </div>
                </div>
            </div>

            <!-- TAB 2: MANAGER EKSTERNAL -->
            <div class="space-y-4 tab-panel ${activeTab === 'eksternal' ? '' : 'hidden'}" id="tab-eksternal">
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
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="role_eksternal">Peran</label>
                    <div class="relative">
                        <select class="w-full appearance-none bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 cursor-pointer" id="role_eksternal" name="role" onchange="document.getElementById('dept-eksternal').style.display = this.value === 'manager_departemen' ? 'block' : 'none'">
                            <option value="hr_manager">HR Manager</option>
                            <option value="manager_departemen">Manager Departemen</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                    </div>
                </div>
                <div class="space-y-1" id="dept-eksternal" style="display: none;">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="department_id_eksternal">Departemen</label>
                    <div class="relative">
                        <select class="w-full appearance-none bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 cursor-pointer" id="department_id_eksternal" name="department_id">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_department }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="jabatan">Jabatan</label>
                    <div class="relative">
                        <select class="w-full appearance-none bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 cursor-pointer" id="jabatan" name="jabatan">
                            @forelse($positions as $pos)
                            <option value="{{ $pos->nama_jabatan }}">{{ $pos->nama_jabatan }}</option>
                            @empty
                            <option value="Manager">Manager</option>
                            @endforelse
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                    </div>
                </div>
            </div>
        `;

        // Logika perpindahan tab (hanya berlaku pada mode Tambah)
        const setManagerTab = (activeTab) => {
            const typeInput = document.getElementById('form-type');
            if (typeInput) typeInput.value = activeTab;

            document.querySelectorAll('#form-hr-manager .manager-tab-btn').forEach(btn => {
                const on = btn.getAttribute('data-tab') === activeTab;
                btn.classList.toggle('bg-white', on);
                btn.classList.toggle('shadow', on);
                btn.classList.toggle('text-primary', on);
                btn.classList.toggle('font-bold', on);
                btn.classList.toggle('text-slate-500', !on);
                btn.classList.toggle('hover:text-slate-700', !on);
                btn.classList.toggle('font-semibold', !on);
            });

            document.querySelectorAll('#form-hr-manager .tab-panel').forEach(panel => {
                const isOn = panel.id === 'tab-' + activeTab;
                panel.classList.toggle('hidden', !isOn);
                // Nonaktifkan kontrol pada tab tersembunyi agar tidak ikut terkirim
                panel.querySelectorAll('input, select, textarea').forEach(el => {
                    el.disabled = !isOn;
                });
            });
        };

        const isiOtomatisPromosi = () => {
            const sel = document.getElementById('employee_id');
            const namaEl = document.getElementById('promosi-nama');
            const nikEl = document.getElementById('promosi-nik');
            if (!sel) return;
            const opt = sel.options[sel.selectedIndex];
            if (namaEl) namaEl.value = opt && opt.dataset.nama ? opt.dataset.nama : '';
            if (nikEl) nikEl.value = opt && opt.dataset.nik ? opt.dataset.nik : '';
        };

        // 1. Tampilkan Modal Tambah Manager (tab Promosi default)
        btnTambahHr.addEventListener('click', () => {
            document.getElementById('modal-title').innerText = 'Tambah Manager';
            document.getElementById('modal-subtitle').innerText = 'Promosikan karyawan internal atau daftarkan manager eksternal.';
            formHrManager.action = "{{ route('backoffice.super_admin.kelola_hr.store') }}";
            formHrManager.innerHTML = buildAddForm('promosi');
            setManagerTab('promosi');
            modalTambahHr.style.display = 'flex';
        });

        const tutupModal = () => {
            modalTambahHr.style.display = 'none';
        };

        btnCloseModal.addEventListener('click', tutupModal);
        btnCancelModal.addEventListener('click', tutupModal);

        // Event delegation untuk tab & autofill promosi
        formHrManager.addEventListener('click', (e) => {
            const tabBtn = e.target.closest('.manager-tab-btn');
            if (tabBtn) setManagerTab(tabBtn.getAttribute('data-tab'));
        });

        formHrManager.addEventListener('change', (e) => {
            if (e.target.id === 'employee_id') isiOtomatisPromosi();
        });

        // 2. Edit Manager
        hrTableBody.addEventListener('click', (e) => {
            const btnEdit = e.target.closest('.btn-edit-hr');
            if (btnEdit && !btnEdit.disabled) {
                const id = btnEdit.getAttribute('data-id');
                const nama = btnEdit.getAttribute('data-nama');
                const email = btnEdit.getAttribute('data-email');
                const nik = btnEdit.getAttribute('data-nik');
                const role = btnEdit.getAttribute('data-role') || 'hr_manager';
                const dept_id = btnEdit.getAttribute('data-dept-id') || '';

                document.getElementById('modal-title').innerText = 'Edit Manager';
                document.getElementById('modal-subtitle').innerText = 'Perbarui data manager.';
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
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="role_edit">Peran</label>
                        <div class="relative">
                            <select class="w-full appearance-none bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 cursor-pointer" id="role_edit" name="role" onchange="document.getElementById('dept-container-edit').style.display = this.value === 'manager_departemen' ? 'block' : 'none'">
                                <option value="hr_manager" ${role === 'hr_manager' ? 'selected' : ''}>HR Manager</option>
                                <option value="manager_departemen" ${role === 'manager_departemen' ? 'selected' : ''}>Manager Departemen</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                        </div>
                    </div>
                    <div class="space-y-1" id="dept-container-edit" style="display: ${role === 'manager_departemen' ? 'block' : 'none'};">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="department_id_edit">Departemen</label>
                        <div class="relative">
                            <select class="w-full appearance-none bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 cursor-pointer" id="department_id_edit" name="department_id">
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->nama_department }}</option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                        </div>
                    </div>
                `;

                modalTambahHr.style.display = 'flex';

                if(role === 'manager_departemen') {
                    document.getElementById('department_id_edit').value = dept_id;
                }
            }
        });

        // 3. Hapus / Nonaktifkan Manager
        hrTableBody.addEventListener('click', (e) => {
            const btnDelete = e.target.closest('.btn-delete-hr');
            if (btnDelete && !btnDelete.disabled) {
                const id = btnDelete.getAttribute('data-id');
                const nama = btnDelete.getAttribute('data-nama');

                if (confirm(`Apakah Anda yakin ingin menonaktifkan Manager "${nama}"? Akun ini tidak akan dapat login lagi.`)) {
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

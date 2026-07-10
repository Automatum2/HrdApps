@extends('layouts.admin')

@section('title', 'Kelola HR Manager - HRDApps')
@section('page_title', 'Kelola HR Manager')

@section('content')
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
            <span class="bg-primary/10 text-primary font-semibold px-3 py-0.5 rounded-full text-xs" id="total-count-badge">3 Total</span>
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
                <!-- Row 1 -->
                <tr class="hover:bg-slate-50 transition-colors group">
                    <td class="py-4 px-6 text-on-surface-variant">1</td>
                    <td class="py-4 px-6 font-bold text-slate-800">Budi Santoso</td>
                    <td class="py-4 px-6 text-slate-600">budi.s@hrdapps.co.id</td>
                    <td class="py-4 px-6 text-slate-600">Senior HR Manager</td>
                    <td class="py-4 px-6" id="status-col-1">
                        <span class="status-badge inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-200 px-2.5 py-1 rounded-full text-[11px] leading-none uppercase tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                            <span>Aktif</span>
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="w-8 h-8 rounded border border-outline-variant flex items-center justify-center text-slate-500 hover:text-primary hover:border-primary transition-colors bg-white cursor-pointer" title="Edit" onclick="alert('Edit HR Manager Budi Santoso')">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <button class="btn-toggle-status w-8 h-8 rounded border border-outline-variant flex items-center justify-center text-slate-500 hover:text-error hover:border-error transition-colors bg-white cursor-pointer" title="Ubah Status" data-id="1" data-nama="Budi Santoso">
                                <span class="material-symbols-outlined text-[18px]">person_off</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Row 2 -->
                <tr class="hover:bg-slate-50 transition-colors group">
                    <td class="py-4 px-6 text-on-surface-variant">2</td>
                    <td class="py-4 px-6 font-bold text-slate-800">Siti Nuraini</td>
                    <td class="py-4 px-6 text-slate-600">siti.n@hrdapps.co.id</td>
                    <td class="py-4 px-6 text-slate-600">HR Manager</td>
                    <td class="py-4 px-6" id="status-col-2">
                        <span class="status-badge inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-200 px-2.5 py-1 rounded-full text-[11px] leading-none uppercase tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                            <span>Aktif</span>
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="w-8 h-8 rounded border border-outline-variant flex items-center justify-center text-slate-500 hover:text-primary hover:border-primary transition-colors bg-white cursor-pointer" title="Edit" onclick="alert('Edit HR Manager Siti Nuraini')">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <button class="btn-toggle-status w-8 h-8 rounded border border-outline-variant flex items-center justify-center text-slate-500 hover:text-error hover:border-error transition-colors bg-white cursor-pointer" title="Ubah Status" data-id="2" data-nama="Siti Nuraini">
                                <span class="material-symbols-outlined text-[18px]">person_off</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Row 3 -->
                <tr class="hover:bg-slate-50 transition-colors group">
                    <td class="py-4 px-6 text-on-surface-variant">3</td>
                    <td class="py-4 px-6 font-bold text-slate-800">Andi Pratama</td>
                    <td class="py-4 px-6 text-slate-600">andi.p@hrdapps.co.id</td>
                    <td class="py-4 px-6 text-slate-600">Regional HR Head</td>
                    <td class="py-4 px-6" id="status-col-3">
                        <span class="status-badge inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-200 px-2.5 py-1 rounded-full text-[11px] leading-none uppercase tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                            <span>Aktif</span>
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="w-8 h-8 rounded border border-outline-variant flex items-center justify-center text-slate-500 hover:text-primary hover:border-primary transition-colors bg-white cursor-pointer" title="Edit" onclick="alert('Edit HR Manager Andi Pratama')">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <button class="btn-toggle-status w-8 h-8 rounded border border-outline-variant flex items-center justify-center text-slate-500 hover:text-error hover:border-error transition-colors bg-white cursor-pointer" title="Ubah Status" data-id="3" data-nama="Andi Pratama">
                                <span class="material-symbols-outlined text-[18px]">person_off</span>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Table Pagination/Footer -->
    <div class="p-6 border-t border-outline-variant bg-white flex justify-between items-center text-slate-500 text-xs font-semibold">
        <span id="showing-entries-info">Menampilkan 1-3 dari 3 data</span>
        <div class="flex items-center gap-1">
            <button class="p-1.5 rounded border border-slate-200 hover:bg-slate-50 text-slate-400 disabled:opacity-50" disabled>
                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
            </button>
            <span class="text-primary px-2 font-bold">1</span>
            <button class="p-1.5 rounded border border-slate-200 hover:bg-slate-50 text-slate-400 disabled:opacity-50" disabled>
                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </button>
        </div>
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
                <h3 class="font-bold text-slate-800 text-base">Tambah HR Manager</h3>
                <p class="text-xs text-slate-500 mt-1">Daftarkan akun administrator HRD yang baru.</p>
            </div>
            <button class="p-1 hover:bg-slate-200 rounded-full text-slate-400 cursor-pointer" id="btn-close-modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <!-- Form Content -->
        <form id="form-hr-manager" class="p-6 space-y-4 overflow-y-auto">
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
                        <option value="Senior HR Manager">Senior HR Manager</option>
                        <option value="HR Manager">HR Manager</option>
                        <option value="Regional HR Head">Regional HR Head</option>
                        <option value="Junior HR Manager">Junior HR Manager</option>
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
            modalTambahHr.style.display = 'flex';
            document.getElementById('nik').focus();
        });
        
        const tutupModal = () => {
            modalTambahHr.style.display = 'none';
            formHrManager.reset();
        };
        
        btnCloseModal.addEventListener('click', tutupModal);
        btnCancelModal.addEventListener('click', tutupModal);
        
        // 2. Submit Form Tambah HR Manager (Mockup)
        formHrManager.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const nik = document.getElementById('nik').value;
            const nama = document.getElementById('nama').value;
            const email = document.getElementById('email').value;
            const jabatan = document.getElementById('jabatan').value;
            
            const totalRows = hrTableBody.querySelectorAll('tr').length;
            const newIndex = totalRows + 1;
            
            const newRow = document.createElement('tr');
            newRow.className = 'hover:bg-slate-50 transition-colors group';
            newRow.innerHTML = `
                <td class="py-4 px-6 text-on-surface-variant">${newIndex}</td>
                <td class="py-4 px-6 font-bold text-slate-800">${nama}</td>
                <td class="py-4 px-6 text-slate-600">${email}</td>
                <td class="py-4 px-6 text-slate-600">${jabatan}</td>
                <td class="py-4 px-6" id="status-col-new-${newIndex}">
                    <span class="status-badge inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-full text-[11px] leading-none uppercase tracking-wide">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                        <span>Onboarding</span>
                    </span>
                </td>
                <td class="py-4 px-6 text-right">
                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button class="w-8 h-8 rounded border border-outline-variant flex items-center justify-center text-slate-500 hover:text-primary hover:border-primary transition-colors bg-white cursor-pointer" title="Edit" onclick="alert('Edit HR Manager ${nama}')">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </button>
                        <button class="btn-toggle-status w-8 h-8 rounded border border-outline-variant flex items-center justify-center text-slate-500 hover:text-error hover:border-error transition-colors bg-white cursor-pointer" title="Ubah Status" data-id="new-${newIndex}" data-nama="${nama}">
                            <span class="material-symbols-outlined text-[18px]">person_off</span>
                        </button>
                    </div>
                </td>
            `;
            
            hrTableBody.appendChild(newRow);
            
            // Update widget count
            const newTotal = totalRows + 1;
            totalCountBadge.innerText = `${newTotal} Total`;
            showingEntriesInfo.innerText = `Menampilkan 1-${newTotal} dari ${newTotal} data`;
            
            alert(`HR Manager ${nama} berhasil didaftarkan!`);
            tutupModal();
        });
        
        // 3. Ubah Status Manager (Aktif <-> Nonaktif)
        hrTableBody.addEventListener('click', (e) => {
            const btnToggle = e.target.closest('.btn-toggle-status');
            if (btnToggle) {
                const id = btnToggle.getAttribute('data-id');
                const nama = btnToggle.getAttribute('data-nama');
                const statusCol = document.getElementById(`status-col-${id}`);
                const badge = statusCol.querySelector('.status-badge');
                const textSpan = badge.querySelector('span:nth-child(2)');
                const dotSpan = badge.querySelector('span:nth-child(1)');
                
                if (textSpan.innerText === 'AKTIF' || textSpan.innerText === 'Aktif' || textSpan.innerText === 'ONBOARDING' || textSpan.innerText === 'Onboarding') {
                    // Ubah jadi nonaktif
                    badge.className = 'status-badge inline-flex items-center gap-1.5 bg-red-50 text-red-700 border border-red-200 px-2.5 py-1 rounded-full text-[11px] leading-none uppercase tracking-wide';
                    dotSpan.className = 'w-1.5 h-1.5 rounded-full bg-red-600';
                    textSpan.innerText = 'Nonaktif';
                    btnToggle.innerHTML = '<span class="material-symbols-outlined text-[18px]">how_to_reg</span>';
                    btnToggle.setAttribute('title', 'Aktifkan');
                    alert(`HR Manager ${nama} telah dinonaktifkan.`);
                } else {
                    // Ubah jadi aktif
                    badge.className = 'status-badge inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-200 px-2.5 py-1 rounded-full text-[11px] leading-none uppercase tracking-wide';
                    dotSpan.className = 'w-1.5 h-1.5 rounded-full bg-green-600';
                    textSpan.innerText = 'Aktif';
                    btnToggle.innerHTML = '<span class="material-symbols-outlined text-[18px]">person_off</span>';
                    btnToggle.setAttribute('title', 'Nonaktifkan');
                    alert(`HR Manager ${nama} telah diaktifkan kembali.`);
                }
            }
        });
    });
</script>
@endpush

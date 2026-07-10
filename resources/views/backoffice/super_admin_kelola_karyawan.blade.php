@extends('layouts.admin')

@section('title', 'Kelola Karyawan - HRDApps')
@section('page_title', 'Kelola Karyawan')

@section('content')
<!-- Page Header -->
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
            <span class="bg-primary/10 text-primary font-semibold px-3 py-0.5 rounded-full text-xs" id="total-karyawan-badge">4 Total</span>
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
                <!-- Row 1 -->
                <tr class="hover:bg-slate-50 transition-colors group" data-nama="Adi Saputra">
                    <td class="py-4 px-6 text-on-surface-variant">1</td>
                    <td class="py-4 px-6 font-bold text-slate-800">Adi Saputra</td>
                    <td class="py-4 px-6 text-slate-600">adi.saputra@email.com</td>
                    <td class="py-4 px-6 text-slate-600">Senior Developer</td>
                    <td class="py-4 px-6 text-slate-600">Engineering</td>
                    <td class="py-4 px-6 font-mono text-slate-600">Rp 8.500.000</td>
                    <td class="py-4 px-6" id="status-k-1">
                        <span class="status-badge-k inline-flex items-center bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-full text-[10px] uppercase font-bold tracking-wide">
                            Aktif
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            <button class="w-8 h-8 rounded border border-outline-variant text-slate-500 hover:bg-slate-50 hover:text-primary transition-colors flex items-center justify-center cursor-pointer" title="Detail" onclick="alert('Detail Karyawan Adi Saputra')">
                                <span class="material-symbols-outlined text-[16px]">search</span>
                            </button>
                            <button class="w-8 h-8 rounded border border-outline-variant text-slate-500 hover:bg-slate-50 hover:text-primary transition-colors flex items-center justify-center cursor-pointer" title="Edit" onclick="alert('Edit Karyawan Adi Saputra')">
                                <span class="material-symbols-outlined text-[16px]">edit</span>
                            </button>
                            <button class="btn-delete-karyawan w-8 h-8 rounded border border-red-200 text-red-500 hover:bg-red-50 transition-colors flex items-center justify-center cursor-pointer" title="Hapus / Nonaktifkan" data-id="1" data-nama="Adi Saputra">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Row 2 -->
                <tr class="hover:bg-slate-50 transition-colors group" data-nama="Budi Santoso">
                    <td class="py-4 px-6 text-on-surface-variant">2</td>
                    <td class="py-4 px-6 font-bold text-slate-800">Budi Santoso</td>
                    <td class="py-4 px-6 text-slate-600">budi.santoso@email.com</td>
                    <td class="py-4 px-6 text-slate-600">UI/UX Designer</td>
                    <td class="py-4 px-6 text-slate-600">Product</td>
                    <td class="py-4 px-6 font-mono text-slate-600">Rp 7.200.000</td>
                    <td class="py-4 px-6" id="status-k-2">
                        <span class="status-badge-k inline-flex items-center bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-full text-[10px] uppercase font-bold tracking-wide">
                            Aktif
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            <button class="w-8 h-8 rounded border border-outline-variant text-slate-500 hover:bg-slate-50 hover:text-primary transition-colors flex items-center justify-center cursor-pointer" title="Detail" onclick="alert('Detail Karyawan Budi Santoso')">
                                <span class="material-symbols-outlined text-[16px]">search</span>
                            </button>
                            <button class="w-8 h-8 rounded border border-outline-variant text-slate-500 hover:bg-slate-50 hover:text-primary transition-colors flex items-center justify-center cursor-pointer" title="Edit" onclick="alert('Edit Karyawan Budi Santoso')">
                                <span class="material-symbols-outlined text-[16px]">edit</span>
                            </button>
                            <button class="btn-delete-karyawan w-8 h-8 rounded border border-red-200 text-red-500 hover:bg-red-50 transition-colors flex items-center justify-center cursor-pointer" title="Hapus / Nonaktifkan" data-id="2" data-nama="Budi Santoso">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Row 3 -->
                <tr class="hover:bg-slate-50 transition-colors group" data-nama="Citra Rahayu">
                    <td class="py-4 px-6 text-on-surface-variant">3</td>
                    <td class="py-4 px-6 font-bold text-slate-400">Citra Rahayu</td>
                    <td class="py-4 px-6 text-slate-400">citra.rahayu@email.com</td>
                    <td class="py-4 px-6 text-slate-400 font-normal">HR Specialist</td>
                    <td class="py-4 px-6 text-slate-400 font-normal">Human Resources</td>
                    <td class="py-4 px-6 font-mono text-slate-400">Rp 9.000.000</td>
                    <td class="py-4 px-6" id="status-k-3">
                        <span class="status-badge-k inline-flex items-center bg-slate-100 text-slate-500 border border-slate-200 px-2 py-0.5 rounded-full text-[10px] uppercase font-bold tracking-wide">
                            Nonaktif
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            <button class="w-8 h-8 rounded border border-outline-variant text-slate-500 hover:bg-slate-50 hover:text-primary transition-colors flex items-center justify-center cursor-pointer" title="Detail" onclick="alert('Detail Karyawan Citra Rahayu')">
                                <span class="material-symbols-outlined text-[16px]">search</span>
                            </button>
                            <button class="w-8 h-8 rounded border border-slate-200 text-slate-300 cursor-not-allowed" disabled title="Edit">
                                <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                            </button>
                            <button class="w-8 h-8 rounded border border-slate-200 text-slate-300 cursor-not-allowed" disabled title="Nonaktifkan">
                                <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Row 4 -->
                <tr class="hover:bg-slate-50 transition-colors group" data-nama="Deni Kurniawan">
                    <td class="py-4 px-6 text-on-surface-variant">4</td>
                    <td class="py-4 px-6 font-bold text-slate-800">Deni Kurniawan</td>
                    <td class="py-4 px-6 text-slate-600">deni.k@email.com</td>
                    <td class="py-4 px-6 text-slate-600">Marketing Lead</td>
                    <td class="py-4 px-6 text-slate-600">Marketing</td>
                    <td class="py-4 px-6 font-mono text-slate-600">Rp 10.500.000</td>
                    <td class="py-4 px-6" id="status-k-4">
                        <span class="status-badge-k inline-flex items-center bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-full text-[10px] uppercase font-bold tracking-wide">
                            Aktif
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            <button class="w-8 h-8 rounded border border-outline-variant text-slate-500 hover:bg-slate-50 hover:text-primary transition-colors flex items-center justify-center cursor-pointer" title="Detail" onclick="alert('Detail Karyawan Deni Kurniawan')">
                                <span class="material-symbols-outlined text-[16px]">search</span>
                            </button>
                            <button class="w-8 h-8 rounded border border-outline-variant text-slate-500 hover:bg-slate-50 hover:text-primary transition-colors flex items-center justify-center cursor-pointer" title="Edit" onclick="alert('Edit Karyawan Deni Kurniawan')">
                                <span class="material-symbols-outlined text-[16px]">edit</span>
                            </button>
                            <button class="btn-delete-karyawan w-8 h-8 rounded border border-red-200 text-red-500 hover:bg-red-50 transition-colors flex items-center justify-center cursor-pointer" title="Hapus / Nonaktifkan" data-id="4" data-nama="Deni Kurniawan">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <div class="p-6 border-t border-outline-variant bg-white flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-semibold text-slate-500">
        <div id="showing-range-info">
            Menampilkan 1 hingga 4 dari 4 entri
        </div>
        <div class="flex gap-1">
            <button class="px-3 py-1.5 border border-slate-200 rounded text-slate-400 hover:bg-slate-50 transition-colors cursor-pointer disabled:opacity-50" disabled>Sebelumnya</button>
            <button class="px-3 py-1.5 border border-primary bg-primary text-white rounded hover:bg-blue-700 transition-colors cursor-pointer">1</button>
            <button class="px-3 py-1.5 border border-slate-200 rounded text-slate-400 hover:bg-slate-50 transition-colors cursor-pointer disabled:opacity-50" disabled>Selanjutnya</button>
        </div>
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
        <form id="form-karyawan" class="p-6 space-y-4 overflow-y-auto">
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
                Simpan Karyawan
            </button>
        </div>
    </div>
</div>

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
            modalTambahKaryawan.style.display = 'flex';
            document.getElementById('nama').focus();
        });
        
        const tutupModal = () => {
            modalTambahKaryawan.style.display = 'none';
            formKaryawan.reset();
        };
        
        btnCloseModal.addEventListener('click', tutupModal);
        btnCancelModal.addEventListener('click', tutupModal);
        
        // Format Rupiah
        const formatRupiah = (val) => {
            return 'Rp ' + parseFloat(val).toLocaleString('id-ID');
        };
        
        // Load data karyawan dari localStorage
        const loadKaryawanDariStorage = () => {
            const listKaryawanStr = localStorage.getItem('karyawan_baru');
            if (listKaryawanStr) {
                const listKaryawan = JSON.parse(listKaryawanStr);
                listKaryawan.forEach((k) => {
                    const existingRow = karyawanTableBody.querySelector(`tr[data-nik="${k.nik}"]`);
                    if (!existingRow) {
                        const totalRows = karyawanTableBody.querySelectorAll('tr').length;
                        const newIndex = totalRows + 1;
                        
                        const newRow = document.createElement('tr');
                        newRow.className = 'hover:bg-slate-50 transition-colors group';
                        newRow.setAttribute('data-nama', k.nama);
                        newRow.setAttribute('data-nik', k.nik);
                        
                        const displayJabatan = k.departemen === 'Belum Ditempatkan' ? '<span class="text-slate-400 italic font-normal">Belum Ditentukan</span>' : k.jabatan;
                        const displayDepartemen = k.departemen === 'Belum Ditempatkan' ? '<span class="text-slate-400 italic font-normal">Belum Ditempatkan</span>' : k.departemen;
                        
                        newRow.innerHTML = `
                            <td class="py-4 px-6 text-on-surface-variant">${newIndex}</td>
                            <td class="py-4 px-6 font-bold text-slate-800">${k.nama}</td>
                            <td class="py-4 px-6 text-slate-600">${k.email}</td>
                            <td class="py-4 px-6 text-slate-600">${displayJabatan}</td>
                            <td class="py-4 px-6 text-slate-600">${displayDepartemen}</td>
                            <td class="py-4 px-6 font-mono text-slate-600">${formatRupiah(k.gaji)}</td>
                            <td class="py-4 px-6" id="status-k-${k.nik}">
                                <span class="status-badge-k inline-flex items-center bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-full text-[10px] uppercase font-bold tracking-wide">
                                    Aktif
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="w-8 h-8 rounded border border-outline-variant text-slate-500 hover:bg-slate-50 hover:text-primary transition-colors flex items-center justify-center cursor-pointer" title="Detail" onclick="alert('Detail Karyawan ${k.nama}')">
                                        <span class="material-symbols-outlined text-[16px]">search</span>
                                    </button>
                                    <button class="w-8 h-8 rounded border border-outline-variant text-slate-500 hover:bg-slate-50 hover:text-primary transition-colors flex items-center justify-center cursor-pointer" title="Edit" onclick="alert('Edit Karyawan ${k.nama}')">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                    </button>
                                    <button class="btn-delete-karyawan w-8 h-8 rounded border border-red-200 text-red-500 hover:bg-red-50 transition-colors flex items-center justify-center cursor-pointer" title="Hapus / Nonaktifkan" data-id="${k.nik}" data-nama="${k.nama}">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        `;
                        karyawanTableBody.appendChild(newRow);
                    }
                });
                
                const finalTotal = karyawanTableBody.querySelectorAll('tr').length;
                totalKaryawanBadge.innerText = `${finalTotal} Total`;
                showingRangeInfo.innerText = `Menampilkan 1 hingga ${finalTotal} dari ${finalTotal} entri`;
            }
        };
        
        loadKaryawanDariStorage();
        
        // 2. Submit Form Tambah Karyawan (Mockup)
        formKaryawan.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const nama = document.getElementById('nama').value;
            const email = document.getElementById('email').value;
            const gaji = document.getElementById('gaji').value;
            
            const generateNik = 'EMP-' + String(Math.floor(1000 + Math.random() * 9000));
            
            const listKaryawanStr = localStorage.getItem('karyawan_baru') || '[]';
            const listKaryawan = JSON.parse(listKaryawanStr);
            const karyawanBaruObj = {
                nik: generateNik,
                nama: nama,
                email: email,
                gaji: gaji,
                jabatan: 'Belum Ditentukan',
                departemen: 'Belum Ditempatkan',
                status: 'Magang'
            };
            listKaryawan.push(karyawanBaruObj);
            localStorage.setItem('karyawan_baru', JSON.stringify(listKaryawan));
            
            loadKaryawanDariStorage();
            
            alert(`Karyawan ${nama} berhasil ditambahkan!`);
            tutupModal();
        });
        
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
                const statusCol = document.getElementById(`status-k-${activeDeleteId}`);
                const badge = statusCol.querySelector('.status-badge-k');
                const namaKaryawan = deleteKaryawanNama.innerText;
                
                // Ubah status jadi nonaktif di tabel
                badge.className = 'status-badge-k inline-flex items-center bg-slate-100 text-slate-500 border border-slate-200 px-2 py-0.5 rounded-full text-[10px] uppercase font-bold tracking-wide';
                badge.innerText = 'Nonaktif';
                
                // Ubah baris tabel visual menjadi redup/deactive
                const row = statusCol.closest('tr');
                row.querySelector('td:nth-child(2)').className = 'py-4 px-6 font-bold text-slate-400';
                row.querySelectorAll('td:not(:nth-child(2)):not(:nth-child(7)):not(:last-child)').forEach(col => {
                    col.className = 'py-4 px-6 text-slate-400';
                });
                
                // Disable tombol aksi edit & delete
                const btnEdit = row.querySelector('button[title="Edit"]');
                const btnDel = row.querySelector('.btn-delete-karyawan');
                
                if (btnEdit) {
                    btnEdit.className = 'w-8 h-8 rounded border border-slate-200 text-slate-300 cursor-not-allowed';
                    btnEdit.disabled = true;
                    btnEdit.removeAttribute('onclick');
                }
                if (btnDel) {
                    btnDel.className = 'w-8 h-8 rounded border border-slate-200 text-slate-300 cursor-not-allowed';
                    btnDel.disabled = true;
                    btnDel.classList.remove('btn-delete-karyawan');
                }
                
                alert(`Karyawan ${namaKaryawan} berhasil dinonaktifkan.`);
                modalDeleteConfirm.style.display = 'none';
                activeDeleteId = null;
            }
        });
    });
</script>
@endpush

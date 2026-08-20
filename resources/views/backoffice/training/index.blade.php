@extends('layouts.admin')

@section('title', 'Manajemen Pelatihan - HRDApps')
@section('page_title', 'Trainer / Pelatihan')

@section('content')
<!-- Header & Breadcrumbs -->
<div class="flex flex-col md:flex-row md:items-end justify-between mb-6">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-1">
            <a class="hover:text-primary transition-colors" href="{{ route('backoffice.dashboard') }}">Beranda</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary font-semibold">Manajemen Pelatihan</span>
        </nav>
        <p class="text-body-sm text-on-surface-variant">Kelola daftar pelatihan dan tugaskan karyawan untuk mengikuti training.</p>
    </div>
    <div class="flex items-center gap-3 mt-4 md:mt-0">
        <button class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-lg font-semibold text-sm shadow hover:brightness-110 active:scale-95 transition-all cursor-pointer" id="btn-open-modal">
            <span class="material-symbols-outlined text-lg">add</span>
            <span>Buat Pelatihan</span>
        </button>
    </div>
</div>

@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6 flex items-center justify-between">
    <div class="flex items-center">
        <span class="material-symbols-outlined mr-2">check_circle</span>
        <p>{{ session('success') }}</p>
    </div>
    <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900 cursor-pointer">
        <span class="material-symbols-outlined">close</span>
    </button>
</div>
@endif

@if($errors->any())
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
    <div class="flex items-center mb-2">
        <span class="material-symbols-outlined mr-2">error</span>
        <p class="font-bold">Terjadi kesalahan:</p>
    </div>
    <ul class="list-disc list-inside text-sm">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Table Content -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-surface-container text-on-surface uppercase tracking-wider font-semibold text-xs border-b border-outline-variant">
                    <th class="px-6 py-4 w-12 text-center">No</th>
                    <th class="px-6 py-4">Nama Pelatihan</th>
                    <th class="px-6 py-4">Skill / Materi</th>
                    <th class="px-6 py-4">Periode</th>
                    <th class="px-6 py-4">Peserta (Karyawan)</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10 font-body-sm text-body-sm">
                @forelse($trainings as $index => $training)
                <tr class="hover:bg-primary/5 transition-colors group">
                    <td class="px-6 py-4 text-center text-on-surface font-semibold font-mono">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 font-bold text-on-surface">{{ $training->nama_training ?: 'Tidak ada nama' }}</td>
                    <td class="px-6 py-4 text-on-surface-variant truncate max-w-xs">{{ $training->skill_dipelajari }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-primary/10 text-primary rounded-lg text-xs font-semibold">
                            {{ \Carbon\Carbon::parse($training->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($training->tanggal_selesai)->format('d M Y') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @foreach($training->employees as $emp)
                                <span class="px-2 py-1 bg-surface-container-highest text-on-surface-variant rounded-md text-[10px] font-bold" title="{{ $emp->nik }}">
                                    {{ $emp->nama_lengkap }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <form action="{{ route('backoffice.training.destroy', $training->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelatihan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded bg-error text-white hover:brightness-110 active:scale-90 transition-all shadow-sm cursor-pointer" title="Hapus Pelatihan">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                        <span class="material-symbols-outlined text-4xl mb-2 text-outline">event_busy</span>
                        <p>Belum ada data pelatihan/training yang dibuat.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('modals')
<!-- MODAL: Tambah Pelatihan -->
<div class="bg-[#0b1c30]/60 backdrop-blur-sm" id="modal-tambah" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 16px;">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant flex flex-col max-h-[90vh] overflow-hidden animate-modal-pop" style="width: 100%; max-width: 700px; min-width: 280px; display: flex; flex-direction: column;">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface">
            <div>
                <h3 class="font-title-sm text-title-sm text-on-surface font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">model_training</span>
                    Buat Jadwal Pelatihan Baru
                </h3>
                <p class="text-body-sm text-on-surface-variant">Isi detail pelatihan dan tugaskan maksimal 5 karyawan.</p>
            </div>
            <button class="p-1 hover:bg-surface-container rounded-full text-on-surface-variant cursor-pointer" id="btn-close-modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <form action="{{ route('backoffice.training.store') }}" method="POST" id="form-tambah-training" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            
            <div class="p-6 overflow-y-auto flex-1 space-y-5">
                <!-- Row 1: Nama & Skill -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">Nama Pelatihan <span class="text-outline text-[10px] lowercase font-normal">(Opsional)</span></label>
                        <input name="nama_training" class="w-full bg-white border border-outline-variant rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-on-surface" placeholder="Contoh: Laravel Advanced Training" type="text" value="{{ old('nama_training') }}">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">Skill / Materi <span class="text-error">*</span></label>
                        <input name="skill_dipelajari" class="w-full bg-white border border-outline-variant rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-on-surface" placeholder="Contoh: Web Security & Middleware" type="text" required value="{{ old('skill_dipelajari') }}">
                    </div>
                </div>

                <!-- Row 2: Tanggal Mulai & Tanggal Selesai -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">Tanggal Mulai <span class="text-error">*</span></label>
                        <input name="tanggal_mulai" class="w-full bg-white border border-outline-variant rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-on-surface" type="date" required id="tanggal_mulai" value="{{ old('tanggal_mulai') }}">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">Tanggal Selesai <span class="text-error">*</span></label>
                        <input name="tanggal_selesai" class="w-full bg-white border border-outline-variant rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-on-surface" type="date" required id="tanggal_selesai" value="{{ old('tanggal_selesai') }}">
                    </div>
                </div>

                <!-- Row 3: Pilih Karyawan -->
                <div class="space-y-2 border border-outline-variant rounded-xl p-4 bg-surface-container-lowest">
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">
                            Tugaskan Karyawan <span class="text-error">*</span> 
                            <span class="text-primary normal-case font-normal ml-1">(Maks 5)</span>
                        </label>
                        <span class="text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-md" id="karyawan-count">0 / 5 Terpilih</span>
                    </div>
                    
                    <div class="relative mb-3">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
                        <input type="text" id="search-karyawan" class="w-full bg-surface border border-outline-variant rounded-lg pl-9 pr-3 py-2 text-sm outline-none focus:border-primary transition-all" placeholder="Cari nama karyawan...">
                    </div>

                    <div class="max-h-48 overflow-y-auto rounded-lg border border-outline-variant/50 bg-white" id="karyawan-list">
                        @foreach($employees as $emp)
                        <label class="flex items-center gap-3 p-3 border-b border-outline-variant/30 hover:bg-primary/5 cursor-pointer transition-colors employee-item">
                            <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" class="emp-checkbox w-4 h-4 text-primary rounded border-outline-variant focus:ring-primary" {{ (is_array(old('employee_ids')) && in_array($emp->id, old('employee_ids'))) ? 'checked' : '' }}>
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary">
                                    {{ strtoupper(substr($emp->nama_lengkap, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-on-surface emp-name">{{ $emp->nama_lengkap }}</p>
                                    <p class="text-[11px] text-on-surface-variant">{{ $emp->nik }} • {{ $emp->department->nama_department ?? '-' }}</p>
                                </div>
                            </div>
                        </label>
                        @endforeach
                        @if(count($employees) == 0)
                        <div class="p-4 text-center text-sm text-outline">Tidak ada karyawan yang tersedia.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-outline-variant bg-surface flex justify-end gap-3">
                <button type="button" class="border border-outline-variant hover:bg-surface-container text-on-surface-variant px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-colors" id="btn-cancel-modal">
                    Batal
                </button>
                <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-5 py-2 rounded-lg text-sm font-semibold cursor-pointer shadow-sm active:scale-95 transition-all" id="btn-submit-training">
                    Simpan & Tugaskan
                </button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modalTambah = document.getElementById('modal-tambah');
        const btnOpenModal = document.getElementById('btn-open-modal');
        const btnCloseModal = document.getElementById('btn-close-modal');
        const btnCancelModal = document.getElementById('btn-cancel-modal');
        
        // Input Dates
        const tglMulai = document.getElementById('tanggal_mulai');
        const tglSelesai = document.getElementById('tanggal_selesai');
        
        // Karyawan Selection Logic
        const searchKaryawan = document.getElementById('search-karyawan');
        const employeeItems = document.querySelectorAll('.employee-item');
        const checkboxes = document.querySelectorAll('.emp-checkbox');
        const countDisplay = document.getElementById('karyawan-count');
        const btnSubmit = document.getElementById('btn-submit-training');

        // Buka Modal
        btnOpenModal.addEventListener('click', () => {
            modalTambah.style.display = 'flex';
        });

        // Tutup Modal
        const tutupModal = () => {
            modalTambah.style.display = 'none';
        };
        btnCloseModal.addEventListener('click', tutupModal);
        btnCancelModal.addEventListener('click', tutupModal);

        // Auto set Tanggal Selesai = Tanggal Mulai
        if (tglMulai && tglSelesai) {
            tglMulai.addEventListener('change', (e) => {
                if (!tglSelesai.value || tglSelesai.value < e.target.value) {
                    tglSelesai.value = e.target.value;
                }
                tglSelesai.min = e.target.value;
            });
        }

        // Search Filter Karyawan
        searchKaryawan.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            employeeItems.forEach(item => {
                const name = item.querySelector('.emp-name').innerText.toLowerCase();
                if (name.includes(query)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Batasi Maksimal 5 Karyawan
        const updateCheckedCount = () => {
            const checkedCount = document.querySelectorAll('.emp-checkbox:checked').length;
            countDisplay.innerText = `${checkedCount} / 5 Terpilih`;
            
            if (checkedCount > 5) {
                countDisplay.classList.remove('text-primary', 'bg-primary/10');
                countDisplay.classList.add('text-error', 'bg-error/10');
            } else {
                countDisplay.classList.remove('text-error', 'bg-error/10');
                countDisplay.classList.add('text-primary', 'bg-primary/10');
            }
            
            // Disable unchecked checkboxes if max reached
            checkboxes.forEach(cb => {
                if (!cb.checked && checkedCount >= 5) {
                    cb.disabled = true;
                    cb.parentElement.classList.add('opacity-50');
                } else {
                    cb.disabled = false;
                    cb.parentElement.classList.remove('opacity-50');
                }
            });
        };

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateCheckedCount);
        });
        
        // Initial check for validation old values
        updateCheckedCount();

        // Form Submit Validation
        const form = document.getElementById('form-tambah-training');
        form.addEventListener('submit', (e) => {
            const checkedCount = document.querySelectorAll('.emp-checkbox:checked').length;
            if (checkedCount === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal 1 karyawan untuk ditugaskan.');
            } else if (checkedCount > 5) {
                e.preventDefault();
                alert('Maksimal hanya 5 karyawan yang dapat ditugaskan untuk satu sesi pelatihan.');
            } else {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = 'Menyimpan...';
            }
        });
    });
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Proses Penggajian - HRDApps')
@section('page_title', 'Proses Penggajian')

@section('content')
<!-- Breadcrumbs & Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant text-body-sm mb-2 font-medium">
            <a class="hover:text-primary transition-colors text-xs" href="{{ route('backoffice.dashboard') }}">Beranda</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-semibold text-xs">Proses Bulanan</span>
        </nav>
        <div class="flex items-center gap-3">
            <h2 class="font-bold text-headline-md text-on-surface">Proses Penggajian - {{ $monthName }}</h2>
            <span class="px-3 py-0.5 bg-surface-container-highest text-primary font-bold text-[10px] rounded-full uppercase tracking-wider border border-primary/20" id="badge-proses-status">Draft</span>
        </div>
    </div>
    <div class="flex gap-2">
        <button class="flex items-center gap-2 px-4 py-2.5 bg-white border border-outline-variant text-on-surface font-semibold rounded-lg hover:bg-surface-container-low transition-all active:scale-95 text-xs cursor-pointer shadow-sm" onclick="window.print()">
            <span class="material-symbols-outlined text-lg">print</span>
            <span>Print Laporan</span>
        </button>
        <button class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white font-semibold rounded-lg hover:brightness-110 transition-all shadow active:scale-95 text-xs cursor-pointer" id="btn-ajukan-approval">
            <span class="material-symbols-outlined text-lg">send</span>
            <span>Ajukan Approval</span>
        </button>
    </div>
</div>

<!-- Dashboard Grid -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
    <!-- Summary Card: Total Gaji -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-outline-variant p-6 shadow-sm flex justify-between items-start">
        <div>
            <p class="text-on-surface-variant font-medium text-xs mb-2">Total Gaji Bersih</p>
            <h3 class="font-bold text-[28px] text-on-surface tracking-tight" id="widget-total-gaji">Rp {{ number_format($totalGajiBersih, 0, ',', '.') }}</h3>
            <div class="flex items-center gap-2 mt-2 text-tertiary font-semibold text-xs">
                <span class="material-symbols-outlined text-sm">trending_up</span>
                <span>2.4% vs Bulan Lalu</span>
            </div>
        </div>
        <div class="w-12 h-12 rounded-full bg-primary-container/10 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">payments</span>
        </div>
    </div>
    <!-- Summary Card: Total Karyawan -->
    <div class="bg-white rounded-xl border border-outline-variant p-6 shadow-sm flex justify-between items-start">
        <div>
            <p class="text-on-surface-variant font-medium text-xs mb-2">Total Penerima Gaji</p>
            <h3 class="font-bold text-[28px] text-on-surface tracking-tight" id="widget-total-karyawan">{{ count($payrolls) }}</h3>
            <p class="text-on-surface-variant text-xs mt-2 font-medium">Semua Departemen</p>
        </div>
        <div class="w-12 h-12 rounded-full bg-secondary-container/30 flex items-center justify-center text-secondary">
            <span class="material-symbols-outlined text-2xl">groups</span>
        </div>
    </div>
    <!-- Summary Card: Progress -->
    <div class="bg-white rounded-xl border border-outline-variant p-6 shadow-sm flex justify-between items-start">
        <div>
            <p class="text-on-surface-variant font-medium text-xs mb-2">Status Proses</p>
            <h3 class="font-bold text-lg text-on-surface" id="text-proses-progress">Review Data</h3>
            <div class="w-full bg-surface-container-high rounded-full h-2 mt-4 overflow-hidden">
                <div class="bg-primary h-full w-[60%] transition-all duration-500" id="bar-proses-progress"></div>
            </div>
            <p class="text-on-surface-variant text-xs mt-2 font-medium" id="text-percent-progress">60% selesai</p>
        </div>
    </div>
</div>

<!-- Payroll Stepper Card -->
<div class="bg-white rounded-xl border border-outline-variant p-6 mb-6 shadow-sm">
    <h4 class="font-bold text-sm mb-4">Tahapan Proses Penggajian</h4>
    <div class="flex items-center justify-between flex-wrap md:flex-nowrap gap-4 md:gap-0">
        <!-- Step 1 -->
        <div class="flex flex-col items-center gap-2 flex-1 min-w-[120px]">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold bg-[#0066ff] shadow-sm">
                <span class="material-symbols-outlined text-sm">check</span>
            </div>
            <span class="text-xs font-semibold text-on-surface">1. Tarik Data</span>
        </div>
        <div class="hidden md:block h-0.5 bg-[#0066ff] flex-1 mx-2"></div>
        <!-- Step 2 -->
        <div class="flex flex-col items-center gap-2 flex-1 min-w-[120px]">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold bg-[#0066ff] shadow-sm">
                <span class="material-symbols-outlined text-sm">check</span>
            </div>
            <span class="text-xs font-semibold text-on-surface">2. Hitung Gaji</span>
        </div>
        <div class="hidden md:block h-0.5 bg-[#0066ff] flex-1 mx-2" id="line-step-3"></div>
        <!-- Step 3 (Active) -->
        <div class="flex flex-col items-center gap-2 flex-1 min-w-[120px]">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold bg-primary shadow" id="circle-step-3">
                3
            </div>
            <span class="text-xs font-bold text-primary" id="text-step-3">3. Review</span>
        </div>
        <div class="hidden md:block h-0.5 bg-surface-container-high flex-1 mx-2" id="line-step-4"></div>
        <!-- Step 4 -->
        <div class="flex flex-col items-center gap-2 flex-1 min-w-[120px]">
            <div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant text-xs font-bold circle border border-outline-variant" id="circle-step-4">
                4
            </div>
            <span class="text-xs font-medium text-on-surface-variant" id="text-step-4">4. Approve</span>
        </div>
        <div class="hidden md:block h-0.5 bg-surface-container-high flex-1 mx-2" id="line-step-5"></div>
        <!-- Step 5 -->
        <div class="flex flex-col items-center gap-2 flex-1 min-w-[120px]">
            <div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant text-xs font-bold circle border border-outline-variant" id="circle-step-5">
                5
            </div>
            <span class="text-xs font-medium text-on-surface-variant" id="text-step-5">5. Distribusi</span>
        </div>
    </div>
</div>

<!-- Table Actions Section -->
<div class="flex flex-col lg:flex-row justify-between items-center gap-4 mb-4">
    <div class="flex items-center gap-2 bg-white border border-outline-variant p-1 rounded-lg w-full lg:w-auto">
        <button class="px-4 py-1.5 bg-primary text-white font-bold rounded-md text-xs cursor-pointer transition-colors active:scale-95" id="btn-tab-semua">Semua Karyawan</button>
        <button class="px-4 py-1.5 text-on-surface-variant font-semibold hover:bg-surface-container-low rounded-md transition-all text-xs cursor-pointer active:scale-95" id="btn-tab-draft">Draft</button>
        <button class="px-4 py-1.5 text-on-surface-variant font-semibold hover:bg-surface-container-low rounded-md transition-all text-xs cursor-pointer active:scale-95" id="btn-tab-approved">Approved</button>
    </div>
    <div class="flex gap-2 w-full lg:w-auto justify-end">
        <button class="flex items-center gap-1.5 px-4 py-2 bg-primary-container text-white font-semibold rounded-lg hover:opacity-90 active:scale-95 transition-all text-xs cursor-pointer" id="btn-hitung-gaji">
            <span class="material-symbols-outlined text-sm">calculate</span>
            <span>Hitung Ulang</span>
        </button>
        <button class="flex items-center gap-1.5 px-4 py-2 bg-tertiary text-white font-semibold rounded-lg hover:brightness-110 active:scale-95 transition-all text-xs cursor-pointer" id="btn-approve-all">
            <span class="material-symbols-outlined text-sm">done_all</span>
            <span>Setujui Semua</span>
        </button>
        <button class="flex items-center gap-1.5 px-4 py-2 bg-secondary text-white font-semibold rounded-lg hover:opacity-90 active:scale-95 transition-all text-xs cursor-pointer" onclick="alert('Membuat lembaran slip gaji massal format PDF...')">
            <span class="material-symbols-outlined text-sm">article</span>
            <span>Generate Slip</span>
        </button>
    </div>
</div>

<!-- Payroll Table -->
<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr class="text-on-surface-variant font-semibold text-xs">
                    <th class="px-6 py-4 uppercase">No</th>
                    <th class="px-6 py-4">Nama Karyawan</th>
                    <th class="px-6 py-4">Gaji Pokok</th>
                    <th class="px-6 py-4">Tunjangan</th>
                    <th class="px-6 py-4">Potongan</th>
                    <th class="px-6 py-4">Lembur</th>
                    <th class="px-6 py-4">Gaji Bersih</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/40 font-body-sm text-body-sm" id="table-payroll-body">
                @forelse($payrolls as $index => $payroll)
                <tr class="hover:bg-primary/5 transition-colors group" data-status="{{ $payroll['status'] }}">
                    <td class="px-6 py-4 text-on-surface font-semibold font-mono">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">{{ strtoupper(substr($payroll['employee']->nama_lengkap, 0, 2)) }}</div>
                            <div>
                                <p class="font-bold text-on-surface text-sm">{{ $payroll['employee']->nama_lengkap }}</p>
                                <p class="text-[10px] text-on-surface-variant">{{ $payroll['employee']->position->nama ?? 'Staff' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-on-surface font-mono">Rp {{ number_format($payroll['gajiPokok'], 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-tertiary font-mono">+Rp {{ number_format($payroll['totalTunjangan'], 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-error font-mono">-Rp {{ number_format($payroll['totalPotongan'], 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-on-surface font-mono">Rp 0</td>
                    <td class="px-6 py-4 font-bold text-on-surface font-mono">Rp {{ number_format($payroll['gajiBersih'], 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        @if($payroll['status'] == 'Approved')
                            <span class="px-2.5 py-0.5 bg-tertiary/10 text-tertiary text-[10px] font-bold rounded-full uppercase tracking-wider badge-status border border-tertiary/20">Approved</span>
                        @else
                            <span class="px-2.5 py-0.5 bg-surface-container-high text-on-surface-variant text-[10px] font-bold rounded-full uppercase tracking-wider badge-status border border-outline-variant/10">{{ $payroll['status'] }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <button class="p-1.5 bg-white border border-outline-variant text-primary rounded hover:bg-primary hover:text-white transition-all shadow-sm cursor-pointer" title="Lihat Detail Slip" onclick="alert(' {{ $payroll['employee']->nama_lengkap }} \nGaji Pokok: Rp {{ number_format($payroll['gajiPokok'], 0, ',', '.') }}\nTunjangan: Rp {{ number_format($payroll['totalTunjangan'], 0, ',', '.') }}\nPotongan: Rp {{ number_format($payroll['totalPotongan'], 0, ',', '.') }}\nLembur: Rp 0\nGaji Bersih: Rp {{ number_format($payroll['gajiBersih'], 0, ',', '.') }}\n\nStatus: {{ $payroll['status'] }}')">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                            </button>
                            @if($payroll['status'] == 'Approved')
                                <button class="p-1.5 bg-white border border-outline-variant text-[#A1A1AA] rounded cursor-not-allowed opacity-50" title="Telah Disetujui" disabled>
                                    <span class="material-symbols-outlined text-sm">done</span>
                                </button>
                            @else
                                <button class="btn-approve-single p-1.5 bg-white border border-outline-variant text-tertiary rounded hover:bg-tertiary hover:text-white transition-all shadow-sm cursor-pointer active:scale-90" title="Setujui Slip Gaji">
                                    <span class="material-symbols-outlined text-sm">check</span>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-8 text-center text-on-surface-variant">Tidak ada data penggajian bulan ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 bg-white border-t border-outline-variant flex items-center justify-between">
        <p class="text-xs text-on-surface-variant">Menampilkan <span class="font-bold text-on-surface" id="showing-entries">{{ count($payrolls) > 0 ? '1-'.count($payrolls) : '0' }}</span> dari <span class="font-bold text-on-surface">{{ count($payrolls) }}</span> karyawan</p>
        <div class="flex items-center gap-2">
            <button class="p-2 border border-outline-variant rounded hover:bg-surface-container-low transition-all disabled:opacity-50 cursor-pointer" disabled>
                <span class="material-symbols-outlined text-lg">chevron_left</span>
            </button>
            <button class="px-3 py-1 bg-primary text-white font-bold rounded text-xs">1</button>
            <button class="px-3 py-1 text-on-surface-variant hover:bg-surface-container-low rounded text-xs cursor-pointer">2</button>
            <button class="px-3 py-1 text-on-surface-variant hover:bg-surface-container-low rounded text-xs cursor-pointer">3</button>
            <button class="p-2 border border-outline-variant rounded hover:bg-surface-container-low transition-all cursor-pointer">
                <span class="material-symbols-outlined text-lg">chevron_right</span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inisialisasi Elemen
    const tableBody = document.getElementById('table-payroll-body');
    const badgeProsesStatus = document.getElementById('badge-proses-status');
    
    // Buttons Tab
    const tabSemua = document.getElementById('btn-tab-semua');
    const tabDraft = document.getElementById('btn-tab-draft');
    const tabApproved = document.getElementById('btn-tab-approved');
    
    // Aksi Massal
    const btnHitung = document.getElementById('btn-hitung-gaji');
    const btnApproveAll = document.getElementById('btn-approve-all');
    const btnAjukan = document.getElementById('btn-ajukan-approval');
    
    // Rekap Widget
    const progressText = document.getElementById('text-proses-progress');
    const progressBar = document.getElementById('bar-proses-progress');
    const progressPercent = document.getElementById('text-percent-progress');
    const showingEntries = document.getElementById('showing-entries');
    
    let currentTab = 'semua';

    // ==========================================
    // 1. Logika Tab Filter
    // ==========================================
    const switchTab = (tabName, clickedBtn) => {
        currentTab = tabName;
        
        // Reset button classes
        [tabSemua, tabDraft, tabApproved].forEach(btn => {
            btn.className = "px-4 py-1.5 text-on-surface-variant font-semibold hover:bg-surface-container-low rounded-md transition-all text-xs cursor-pointer active:scale-95";
        });
        
        // Set active class
        clickedBtn.className = "px-4 py-1.5 bg-primary text-white font-bold rounded-md text-xs cursor-pointer transition-colors active:scale-95";
        
        applyFilters();
    };

    const applyFilters = () => {
        const rows = tableBody.querySelectorAll('tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const status = row.getAttribute('data-status');
            
            if (currentTab === 'semua' || 
                (currentTab === 'draft' && status === 'Draft') ||
                (currentTab === 'approved' && status === 'Approved')) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });
        
        // Sesuaikan nomor urut
        let idx = 1;
        rows.forEach(row => {
            if (!row.classList.contains('hidden')) {
                row.querySelector('td:first-child').innerText = idx++;
            }
        });
        
        showingEntries.innerText = visibleCount === 0 ? '0' : `1-${visibleCount}`;
    };

    tabSemua.addEventListener('click', () => switchTab('semua', tabSemua));
    tabDraft.addEventListener('click', () => switchTab('draft', tabDraft));
    tabApproved.addEventListener('click', () => switchTab('approved', tabApproved));

    // ==========================================
    // 2. Fungsi Pembaharuan Status & Progres
    // ==========================================
    const updateProgress = () => {
        const rows = tableBody.querySelectorAll('tr');
        const total = rows.length;
        let approved = 0;
        
        rows.forEach(row => {
            if (row.getAttribute('data-status') === 'Approved') {
                approved++;
            }
        });
        
        const percent = Math.round((approved / total) * 100);
        
        // Perbarui Teks & Progress Bar
        progressPercent.innerText = `${percent}% selesai`;
        progressBar.style.width = `${percent}%`;
        
        if (percent === 100) {
            progressText.innerText = "Selesai Direview";
            badgeProsesStatus.innerText = "Reviewed";
            badgeProsesStatus.className = "px-3 py-0.5 bg-tertiary/10 text-tertiary font-bold text-[10px] rounded-full uppercase tracking-wider border border-tertiary/20";
        } else if (percent > 0) {
            progressText.innerText = "Review Data";
            badgeProsesStatus.innerText = "Draft";
            badgeProsesStatus.className = "px-3 py-0.5 bg-surface-container-highest text-primary font-bold text-[10px] rounded-full uppercase tracking-wider border border-primary/20";
        } else {
            progressText.innerText = "Draft Awal";
            badgeProsesStatus.innerText = "Draft";
        }
    };

    // ==========================================
    // 3. Aksi Setujui Single Karyawan (Tombol Centang)
    // ==========================================
    tableBody.addEventListener('click', (e) => {
        const btnApprove = e.target.closest('.btn-approve-single');
        if (btnApprove) {
            const row = btnApprove.closest('tr');
            
            // Ubah data status
            row.setAttribute('data-status', 'Approved');
            
            // Ubah badge visual
            const badge = row.querySelector('.badge-status');
            badge.innerText = 'Approved';
            badge.className = 'px-2.5 py-0.5 bg-tertiary/10 text-tertiary text-[10px] font-bold rounded-full uppercase tracking-wider badge-status border border-tertiary/20';
            
            // Matikan tombol
            btnApprove.className = 'p-1.5 bg-white border border-outline-variant text-[#A1A1AA] rounded cursor-not-allowed opacity-50';
            btnApprove.disabled = true;
            btnApprove.innerHTML = '<span class="material-symbols-outlined text-sm">done</span>';
            
            updateProgress();
            applyFilters();
        }
    });

    // ==========================================
    // 4. Aksi Setujui Semua (Approve All)
    // ==========================================
    btnApproveAll.addEventListener('click', () => {
        const rows = tableBody.querySelectorAll('tr');
        rows.forEach(row => {
            if (row.getAttribute('data-status') === 'Draft') {
                row.setAttribute('data-status', 'Approved');
                
                const badge = row.querySelector('.badge-status');
                badge.innerText = 'Approved';
                badge.className = 'px-2.5 py-0.5 bg-tertiary/10 text-tertiary text-[10px] font-bold rounded-full uppercase tracking-wider badge-status border border-tertiary/20';
                
                const btnApprove = row.querySelector('.btn-approve-single');
                if (btnApprove) {
                    btnApprove.className = 'p-1.5 bg-white border border-outline-variant text-[#A1A1AA] rounded cursor-not-allowed opacity-50';
                    btnApprove.disabled = true;
                    btnApprove.innerHTML = '<span class="material-symbols-outlined text-sm">done</span>';
                }
            }
        });
        
        updateProgress();
        applyFilters();
        alert('Seluruh data penggajian karyawan berhasil disetujui!');
    });

    // ==========================================
    // 5. Aksi Hitung Ulang & Ajukan Approval
    // ==========================================
    btnHitung.addEventListener('click', () => {
        alert('Sistem sedang menghitung ulang lembur, potongan BPJS, dan PPh21 untuk periode Juni 2026...\n\nPenghitungan ulang sukses!');
    });
    
    btnAjukan.addEventListener('click', () => {
        alert('Mengirimkan draf penggajian periode Juni 2026 kepada Direktur Utama untuk mendapatkan Approval final...');
    });

    // Inisialisasi awal progress
    updateProgress();
</script>
@endpush

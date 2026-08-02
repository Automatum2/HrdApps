@extends('layouts.admin')

@section('title', 'Laporan & Rekap - HRDApps')
@section('page_title', 'Laporan & Rekap')

@section('content')
<!-- Breadcrumbs & Info -->
<div class="flex flex-col md:flex-row md:items-end justify-between mb-6">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-1">
            <a class="hover:text-primary transition-colors text-xs" href="{{ route('backoffice.dashboard') }}">Beranda</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-semibold text-xs">Laporan</span>
        </nav>
        <p class="text-body-sm text-on-surface-variant">Analisis rekapitulasi data bulanan, audit biaya, absensi, dan performa divisi karyawan Anda.</p>
    </div>
    <a href="{{ route('backoffice.laporan.kinerja') }}" class="flex items-center gap-2 px-4 py-2.5 bg-secondary text-white font-semibold rounded-lg hover:brightness-110 transition-all shadow active:scale-95 text-xs cursor-pointer mt-4 md:mt-0">
        <span class="material-symbols-outlined text-lg">admin_panel_settings</span>
        <span>View Khusus Kinerja + Absensi</span>
    </a>
</div>

<div class="space-y-8">
    <!-- Bento Grid: Report Categories -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1: Laporan Absensi -->
        <div class="bg-primary text-white rounded-xl p-6 relative overflow-hidden group cursor-pointer shadow-sm hover-card-float" onclick="document.getElementById('report-type').value = 'Laporan Absensi Bulanan'">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-500"></div>
            <span class="material-symbols-outlined text-4xl mb-4 filled block text-white/90">event_note</span>
            <h3 class="font-bold text-lg mb-2">Laporan Absensi</h3>
            <p class="text-xs text-white/80">Rekapitulasi Kehadiran, Cuti & Izin Periodik</p>
        </div>
        <!-- Card 2: Laporan Penggajian -->
        <div class="bg-tertiary text-white rounded-xl p-6 relative overflow-hidden group cursor-pointer shadow-sm hover-card-float" onclick="document.getElementById('report-type').value = 'Laporan Gaji Pokok'">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-500"></div>
            <span class="material-symbols-outlined text-4xl mb-4 filled block text-white/90">payments</span>
            <h3 class="font-bold text-lg mb-2">Laporan Penggajian</h3>
            <p class="text-xs text-white/85">Audit Total Gaji, Bonus, Lembur & Pajak PPh21</p>
        </div>
        <!-- Card 3: Laporan Karyawan -->
        <div class="bg-[#7c3aed] text-white rounded-xl p-6 relative overflow-hidden group cursor-pointer shadow-sm hover-card-float" onclick="document.getElementById('report-type').value = 'Laporan Kinerja Tahunan'">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-500"></div>
            <span class="material-symbols-outlined text-4xl mb-4 filled block text-white/90">groups</span>
            <h3 class="font-bold text-lg mb-2">Laporan Karyawan</h3>
            <p class="text-xs text-white/80">Data Divisi, Riwayat Kontrak & Evaluasi Kinerja</p>
        </div>
    </div>
    
    <!-- Generate Report Form Section -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden hover-card-float">
        <div class="p-6 border-b border-outline-variant bg-surface/50">
            <h3 class="font-bold text-sm text-on-background">Generate Laporan</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('backoffice.laporan.generate') }}" method="POST" class="flex flex-col lg:flex-row gap-6 items-end">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 flex-1 w-full">
                    <!-- Tipe Laporan -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-on-surface-variant block">Tipe Laporan</label>
                        <div class="relative">
                            <select name="tipe_laporan" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-sm text-on-background focus:ring-2 focus:ring-primary/20 outline-none cursor-pointer" id="report-type">
                                <option>Laporan Absensi Bulanan</option>
                                <option>Laporan Gaji Pokok</option>
                                <option>Laporan Kinerja Tahunan</option>
                            </select>
                        </div>
                    </div>
                    <!-- Periode -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-on-surface-variant block">Periode</label>
                        <div class="relative flex items-center">
                            <input name="periode" class="w-full px-4 py-2.5 bg-white border border-outline-variant rounded-lg text-sm text-on-background focus:ring-2 focus:ring-primary/20 outline-none" type="text" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('d/m/Y') }} - {{ \Carbon\Carbon::now()->endOfMonth()->format('d/m/Y') }}" id="report-period">
                        </div>
                    </div>
                    <!-- Department -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-on-surface-variant block">Department</label>
                        <div class="relative">
                            <select name="department_id" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-sm text-on-background focus:ring-2 focus:ring-primary/20 outline-none cursor-pointer" id="report-dept">
                                <option value="all">Semua Department</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->nama_department }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- Format -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-on-surface-variant block">Format</label>
                        <div class="relative">
                            <select name="format" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-sm text-on-background focus:ring-2 focus:ring-primary/20 outline-none cursor-pointer" id="report-format">
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel (CSV)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Generate Button -->
                <button class="w-full lg:w-32 bg-primary hover:brightness-110 text-white rounded-xl py-3 flex flex-col items-center justify-center gap-1 transition-all shadow active:scale-95 cursor-pointer text-xs font-bold lg:mb-[2px]" type="submit" id="btn-generate-report">
                    <span class="material-symbols-outlined text-2xl filled block" style="font-variation-settings: 'FILL' 1;">description</span>
                    <span>Generate</span>
                </button>
            </form>
        </div>
    </div>
    
    <!-- Recent Reports Table Section -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col hover-card-float">
        <div class="p-6 border-b border-outline-variant bg-surface/50 flex justify-between items-center">
            <h3 class="font-bold text-sm text-on-background">Laporan Terakhir</h3>
            <button class="text-primary hover:bg-surface-container-low px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors cursor-pointer active:scale-95">Lihat Semua</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-surface-container-low border-b border-outline-variant text-on-surface-variant font-semibold text-xs">
                        <th class="py-4 px-6 w-16 text-center">No</th>
                        <th class="py-4 px-6">Nama Laporan</th>
                        <th class="py-4 px-6">Tipe</th>
                        <th class="py-4 px-6">Periode</th>
                        <th class="py-4 px-6">Dibuat Oleh</th>
                        <th class="py-4 px-6">Tanggal</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/40 font-body-sm text-body-sm" id="table-laporan-body">
                    @forelse($recent_reports as $index => $report)
                    <tr class="hover:bg-primary/5 transition-colors group">
                        <td class="py-4 px-6 text-center text-on-surface-variant font-mono">{{ $index + 1 }}</td>
                        <td class="py-4 px-6 font-bold text-on-background flex-name">{{ $report->nama_laporan }}</td>
                        <td class="py-4 px-6 text-on-surface-variant flex-type">{{ $report->tipe }}</td>
                        <td class="py-4 px-6 text-on-surface-variant font-mono text-xs">{{ $report->periode }}</td>
                        <td class="py-4 px-6 text-on-surface-variant">{{ $report->dibuat_oleh }}</td>
                        <td class="py-4 px-6 text-on-surface-variant font-mono text-xs">{{ $report->created_at->format('d-m-Y H:i') }}</td>
                        <td class="py-4 px-6 text-right">
                            <a href="{{ route('backoffice.laporan.download', $report->id) }}" class="inline-flex bg-white hover:bg-surface-container-low border border-outline-variant text-secondary text-xs font-semibold px-3 py-1.5 rounded items-center justify-center gap-1.5 ml-auto transition-colors shadow-sm cursor-pointer active:scale-95 btn-download">
                                <span class="material-symbols-outlined text-sm">download</span>
                                <span>Download</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 px-6 text-center text-slate-500">
                            <span class="material-symbols-outlined text-4xl mb-2 text-outline block mx-auto">description</span>
                            <p>Belum ada laporan yang di-generate bulan ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const form = document.querySelector('form');
    const btnGenerate = document.getElementById('btn-generate-report');
    
    form.addEventListener('submit', function() {
        const originalContent = btnGenerate.innerHTML;
        btnGenerate.disabled = true;
        btnGenerate.classList.add('opacity-80', 'cursor-not-allowed');
        btnGenerate.innerHTML = `
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-[10px] mt-1">Generating...</span>
        `;
    });
</script>
@endpush

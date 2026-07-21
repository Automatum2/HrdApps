@extends('layouts.admin')

@section('title', 'Slip Gaji Karyawan - HRDApps')
@section('page_title', 'Slip Gaji')

@push('styles')
<style>
    .payslip-border { border: 1px solid #e2e8f0; }
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .payslip-container { 
            padding: 0 !important; 
            margin: 0 !important; 
            box-shadow: none !important; 
            border: none !important; 
        }
        /* Hide sidebar and header from layouts/admin.blade.php */
        aside, header { display: none !important; }
        main { margin-left: 0 !important; padding: 0 !important; }
        .p-8 { padding: 0 !important; }
        body { background: white; }
    }
    
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-spin-slow {
        animation: spin-slow 20s linear infinite;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto w-full">
    <!-- Breadcrumb -->
    <nav class="no-print flex items-center gap-2 text-on-surface-variant mb-md font-body-sm">
        <span class="hover:text-primary cursor-pointer">Payroll</span>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="hover:text-primary cursor-pointer">Proses Bulanan</span>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-primary font-bold">Slip Gaji</span>
    </nav>
    
    <!-- Page Header -->
    <div class="no-print flex flex-col md:flex-row md:items-center justify-between gap-4 mb-xl">
        <div>
            <h2 class="font-headline-md text-headline-md text-on-background">Slip Gaji Karyawan - {{ $monthName }}</h2>
            <p class="text-on-surface-variant font-body-md">Data gaji terverifikasi untuk periode pembayaran {{ $monthName }}.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="flex items-center gap-2 px-md py-2 bg-white border border-outline-variant rounded-lg font-body-md font-bold text-on-secondary-fixed hover:bg-surface-container-low transition-all btn-ripple" onclick="window.print()">
                <span class="material-symbols-outlined text-sm">print</span>
                Print
            </button>
            <button class="flex items-center gap-2 px-md py-2 bg-primary text-on-primary rounded-lg font-body-md font-bold hover:bg-primary-container transition-all shadow-md active:scale-95 btn-ripple">
                <span class="material-symbols-outlined text-sm">download</span>
                Download PDF
            </button>
        </div>
    </div>
    
    <!-- Payslip Card (Main Focus) -->
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant overflow-hidden p-xl payslip-container hover-card-float relative">
        <!-- Payslip Header -->
        <div class="flex flex-col md:flex-row justify-between items-start border-b-2 border-primary/20 pb-lg mb-lg">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-surface-container-low rounded-xl flex items-center justify-center p-2">
                    <img alt="HRDApps Logo" class="w-full h-full object-contain" src="{{ asset('images/logo.svg') }}">
                </div>
                <div>
                    <h3 class="font-display-lg text-display-lg text-primary leading-tight">PT Global Tech Solusindo</h3>
                    <p class="font-body-md text-on-surface-variant italic">Cyber Tower Suite 10, Jakarta Pusat, Indonesia</p>
                </div>
            </div>
            <div class="text-right mt-4 md:mt-0">
                <h4 class="font-headline-md text-headline-md font-extrabold uppercase tracking-widest text-on-background">SLIP GAJI KARYAWAN</h4>
                <div class="inline-block mt-1 px-4 py-1 bg-surface-container-high rounded-full">
                    <p class="font-label-uppercase text-label-uppercase text-primary">Periode: {{ $monthName }}</p>
                </div>
            </div>
        </div>
        
        <!-- Employee Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-xl bg-surface-container-low/50 p-lg rounded-xl mb-xl border border-outline-variant/30">
            <div class="space-y-3">
                <div class="grid grid-cols-3">
                    <span class="text-on-surface-variant font-label-uppercase">Nama</span>
                    <span class="col-span-2 font-body-md font-bold">: {{ $employee->nama_lengkap }}</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-on-surface-variant font-label-uppercase">NIK</span>
                    <span class="col-span-2 font-body-md font-bold">: {{ $employee->nik }}</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-on-surface-variant font-label-uppercase">No Rekening</span>
                    <span class="col-span-2 font-body-md font-bold">: {{ $employee->nama_bank }} - {{ $employee->no_rekening }}</span>
                </div>
            </div>
            <div class="space-y-3">
                <div class="grid grid-cols-3">
                    <span class="text-on-surface-variant font-label-uppercase">Jabatan</span>
                    <span class="col-span-2 font-body-md font-bold">: {{ $employee->position->nama ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-on-surface-variant font-label-uppercase">Departemen</span>
                    <span class="col-span-2 font-body-md font-bold">: {{ $employee->department->nama ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-on-surface-variant font-label-uppercase">Status</span>
                    <span class="col-span-2 font-body-md font-bold">: {{ ucfirst($employee->status_kerja) }}</span>
                </div>
            </div>
        </div>
        
        <!-- Earnings vs Deductions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-lg mb-xl">
            <!-- Earnings (Pendapatan) -->
            <div class="border border-outline-variant rounded-xl overflow-hidden">
                <div class="bg-primary px-md py-2 flex justify-between items-center">
                    <h5 class="text-white font-label-uppercase">Pendapatan</h5>
                    <span class="text-white/80 text-xs text-right">Hari Hadir: {{ $hadir }}</span>
                </div>
                <div class="p-md space-y-3">
                    <div class="flex justify-between items-center font-body-md transition-all duration-200 px-2 py-1 rounded-md hover:bg-surface-container-low">
                        <span class="text-on-surface-variant">Gaji Pokok</span>
                        <span class="font-bold">Rp {{ number_format($gajiPokok, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center font-body-md transition-all duration-200 px-2 py-1 rounded-md hover:bg-surface-container-low">
                        <span class="text-on-surface-variant">Tunjangan Transport</span>
                        <span class="font-bold">Rp {{ number_format($tunjanganTransport, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center font-body-md transition-all duration-200 px-2 py-1 rounded-md hover:bg-surface-container-low">
                        <span class="text-on-surface-variant">Tunjangan Makan</span>
                        <span class="font-bold">Rp {{ number_format($tunjanganMakan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center font-body-md transition-all duration-200 px-2 py-1 rounded-md hover:bg-surface-container-low">
                        <span class="text-on-surface-variant">Tunjangan Jabatan</span>
                        <span class="font-bold">Rp {{ number_format($tunjanganJabatan, 0, ',', '.') }}</span>
                    </div>
                    <!-- Filler for equal height -->
                    <div class="h-8 opacity-0">Filler</div>
                    
                    <div class="pt-3 border-t border-outline-variant flex justify-between items-center">
                        <span class="font-bold text-primary">Total Pendapatan</span>
                        <span class="font-display-lg text-title-sm text-primary">Rp {{ number_format($gajiKotor, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Deductions (Potongan) -->
            <div class="border border-outline-variant rounded-xl overflow-hidden">
                <div class="bg-error px-md py-2 flex justify-between items-center">
                    <h5 class="text-white font-label-uppercase">Potongan</h5>
                    <span class="text-white/80 text-xs text-right">Alpha/Mangkir: {{ $alpha }}</span>
                </div>
                <div class="p-md space-y-3">
                    <div class="flex justify-between items-center font-body-md transition-all duration-200 px-2 py-1 rounded-md hover:bg-surface-container-low">
                        <span class="text-on-surface-variant">BPJS Kesehatan (4%)</span>
                        <span class="font-bold text-error">Rp {{ number_format($potonganBPJSKesehatan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center font-body-md transition-all duration-200 px-2 py-1 rounded-md hover:bg-surface-container-low">
                        <span class="text-on-surface-variant">BPJS Ketenagakerjaan (2%)</span>
                        <span class="font-bold text-error">Rp {{ number_format($potonganBPJSKetenagakerjaan, 0, ',', '.') }}</span>
                    </div>
                    @if($potonganAlpha > 0)
                    <div class="flex justify-between items-center font-body-md transition-all duration-200 px-2 py-1 rounded-md hover:bg-surface-container-low">
                        <span class="text-on-surface-variant">Potongan Kehadiran (Alpha)</span>
                        <span class="font-bold text-error">Rp {{ number_format($potonganAlpha, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <!-- Filler for equal height -->
                    <div class="h-8 opacity-0">Filler</div>
                    @if($potonganAlpha == 0)
                    <div class="h-8 opacity-0">Filler</div>
                    @endif
                    
                    <div class="pt-3 border-t border-outline-variant flex justify-between items-center">
                        <span class="font-bold text-error">Total Potongan</span>
                        <span class="font-display-lg text-title-sm text-error">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Net Salary Summary -->
        <div class="bg-primary-container rounded-xl p-lg text-white mb-xl flex flex-col md:flex-row justify-between items-center shadow-lg transform hover:scale-[1.01] transition-transform">
            <div class="mb-4 md:mb-0">
                <p class="font-label-uppercase opacity-80 uppercase tracking-widest">Total Gaji Bersih Terbayarkan</p>
                <h4 class="font-display-lg text-display-lg font-extrabold">Rp {{ number_format($gajiBersih, 0, ',', '.') }}</h4>
                <p class="font-body-sm italic opacity-70">{{ $terbilangGaji }}</p>
            </div>
            <div class="bg-white/20 backdrop-blur-sm p-4 rounded-lg flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                </div>
                <div>
                    <p class="font-body-md font-bold">Status: Dibayarkan</p>
                    <p class="text-xs opacity-80">25 Juni 2026 - 09:00 WIB</p>
                </div>
            </div>
        </div>
        
        <!-- Signature & Footer -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
            <div class="space-y-4 w-full md:max-w-sm shrink-0">
                <div class="bg-surface-container p-4 rounded-lg border-l-4 border-outline">
                    <p class="text-[11px] leading-relaxed text-on-surface-variant">
                        <strong>CATATAN:</strong><br>
                        Ini adalah dokumen resmi yang dihasilkan secara otomatis oleh sistem HRDApps. 
                        Segala bentuk perubahan fisik tanpa persetujuan HRD dianggap tidak sah.
                    </p>
                </div>
                <div class="text-[10px] text-on-surface-variant/60 font-mono uppercase">
                    ID Dokumen: HR-SLIP-{{ strtoupper(date('Y-M')) }}-{{ $employee->id }}
                </div>
            </div>
            <div class="text-center mt-xl md:mt-0">
                <p class="font-label-uppercase text-on-surface-variant mb-md">Diterbitkan oleh Digital HRD</p>
                <div class="w-32 h-32 mx-auto relative group">
                    <!-- Digital Seal Placeholder -->
                    <div class="absolute inset-0 bg-primary/5 rounded-full border-2 border-dashed border-primary/20 animate-spin-slow"></div>
                    <img class="w-full h-full object-contain relative z-10 opacity-80 grayscale group-hover:grayscale-0 transition-all" alt="Digital Signature" src="{{ asset('images/signature.svg') }}">
                </div>
                <p class="font-body-md font-bold mt-2">Jane Doe</p>
                <p class="text-xs text-on-surface-variant">Manager HRD</p>
            </div>
        </div>
    </div>
    
    <!-- Additional Help/Contact -->
    <div class="no-print mt-xl p-lg bg-surface-container-high rounded-xl flex items-center gap-4 border border-primary/10">
        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-primary">
            <span class="material-symbols-outlined">help_outline</span>
        </div>
        <div>
            <h6 class="font-title-sm text-primary">Ada pertanyaan mengenai rincian gaji Anda?</h6>
            <p class="font-body-md text-on-surface-variant">Hubungi departemen keuangan atau HRD melalui tiket bantuan di portal HRDApps.</p>
        </div>
        <button class="ml-auto px-md py-2 bg-white rounded-lg font-bold text-primary shadow-sm hover:shadow-md transition-all btn-ripple">Kirim Tiket</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Trigger print function
    window.onbeforeprint = function() {
        console.log("Preparing payslip for print...");
    };
</script>
@endpush

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
            <h2 class="font-headline-md text-headline-md text-on-background">Slip Gaji Karyawan - Juni 2026</h2>
            <p class="text-on-surface-variant font-body-md">Data gaji terverifikasi untuk periode pembayaran Juni.</p>
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
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant overflow-hidden p-xl payslip-container hover-card-float card-gradient-border">
        <!-- Payslip Header -->
        <div class="flex flex-col md:flex-row justify-between items-start border-b-2 border-primary/20 pb-lg mb-lg">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-surface-container-low rounded-xl flex items-center justify-center p-2">
                    <img alt="HRDApps Logo" class="w-full h-full object-contain" src="https://lh3.googleusercontent.com/aida/AP1WRLvpFHUCy_Yq6LtQqNmX6ag1aIQKY-BWSHyohEUquy3wK6mK85LadnnQrcjTEKOl-u_Di5Vu_1inn1-FwSS_RsfRS_lxkWf7dUun0xT-mZ4hif9k1elklSKL6tnImnswj7HdtCeFyE7ZDzAGtf2O8_P3wIDQBduk0NNjEqw58GjppC2mqBIK_gipbo4FFeiiuaNQLvWr-HV4Ke8Zho1gzNOMGkijIz0wKaS-Soge8ZsRPwXNtquAQwv47ow">
                </div>
                <div>
                    <h3 class="font-display-lg text-display-lg text-primary leading-tight">PT Global Tech Solusindo</h3>
                    <p class="font-body-md text-on-surface-variant italic">Cyber Tower Suite 10, Jakarta Pusat, Indonesia</p>
                </div>
            </div>
            <div class="text-right mt-4 md:mt-0">
                <h4 class="font-headline-md text-headline-md font-extrabold uppercase tracking-widest text-on-background">SLIP GAJI KARYAWAN</h4>
                <div class="inline-block mt-1 px-4 py-1 bg-surface-container-high rounded-full">
                    <p class="font-label-uppercase text-label-uppercase text-primary">Periode: Juni 2026</p>
                </div>
            </div>
        </div>
        
        <!-- Employee Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-xl bg-surface-container-low/50 p-lg rounded-xl mb-xl border border-outline-variant/30">
            <div class="space-y-3">
                <div class="grid grid-cols-3">
                    <span class="text-on-surface-variant font-label-uppercase">Nama</span>
                    <span class="col-span-2 font-body-md font-bold">: Ahmad Fadillah</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-on-surface-variant font-label-uppercase">NIK</span>
                    <span class="col-span-2 font-body-md font-bold">: EMP-2024-001</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-on-surface-variant font-label-uppercase">No Rekening</span>
                    <span class="col-span-2 font-body-md font-bold">: BCA - 1234567890</span>
                </div>
            </div>
            <div class="space-y-3">
                <div class="grid grid-cols-3">
                    <span class="text-on-surface-variant font-label-uppercase">Jabatan</span>
                    <span class="col-span-2 font-body-md font-bold">: Staff IT</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-on-surface-variant font-label-uppercase">Departemen</span>
                    <span class="col-span-2 font-body-md font-bold">: Teknologi Informasi</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-on-surface-variant font-label-uppercase">Status</span>
                    <span class="col-span-2 font-body-md font-bold">: Karyawan Tetap</span>
                </div>
            </div>
        </div>
        
        <!-- Earnings vs Deductions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-lg mb-xl">
            <!-- Earnings (Pendapatan) -->
            <div class="border border-outline-variant rounded-xl overflow-hidden">
                <div class="bg-primary px-md py-2">
                    <h5 class="text-white font-label-uppercase">Pendapatan</h5>
                </div>
                <div class="p-md space-y-3">
                    <div class="flex justify-between items-center font-body-md transition-all duration-200 px-2 py-1 rounded-md hover:bg-surface-container-low">
                        <span class="text-on-surface-variant">Gaji Pokok</span>
                        <span class="font-bold">Rp 5.000.000</span>
                    </div>
                    <div class="flex justify-between items-center font-body-md transition-all duration-200 px-2 py-1 rounded-md hover:bg-surface-container-low">
                        <span class="text-on-surface-variant">Tunjangan Transport</span>
                        <span class="font-bold">Rp 500.000</span>
                    </div>
                    <div class="flex justify-between items-center font-body-md transition-all duration-200 px-2 py-1 rounded-md hover:bg-surface-container-low">
                        <span class="text-on-surface-variant">Tunjangan Makan</span>
                        <span class="font-bold">Rp 400.000</span>
                    </div>
                    <div class="flex justify-between items-center font-body-md transition-all duration-200 px-2 py-1 rounded-md hover:bg-surface-container-low">
                        <span class="text-on-surface-variant">Tunjangan Jabatan</span>
                        <span class="font-bold">Rp 300.000</span>
                    </div>
                    <div class="flex justify-between items-center font-body-md transition-all duration-200 px-2 py-1 rounded-md hover:bg-surface-container-low">
                        <span class="text-on-surface-variant">Lembur (8 jam)</span>
                        <span class="font-bold">Rp 250.000</span>
                    </div>
                    <div class="pt-3 border-t border-outline-variant flex justify-between items-center">
                        <span class="font-bold text-primary">Total Pendapatan</span>
                        <span class="font-display-lg text-title-sm text-primary">Rp 6.450.000</span>
                    </div>
                </div>
            </div>
            
            <!-- Deductions (Potongan) -->
            <div class="border border-outline-variant rounded-xl overflow-hidden">
                <div class="bg-error px-md py-2">
                    <h5 class="text-white font-label-uppercase">Potongan</h5>
                </div>
                <div class="p-md space-y-3">
                    <div class="flex justify-between items-center font-body-md transition-all duration-200 px-2 py-1 rounded-md hover:bg-surface-container-low">
                        <span class="text-on-surface-variant">BPJS Kesehatan</span>
                        <span class="font-bold text-error">Rp 200.000</span>
                    </div>
                    <div class="flex justify-between items-center font-body-md transition-all duration-200 px-2 py-1 rounded-md hover:bg-surface-container-low">
                        <span class="text-on-surface-variant">BPJS Ketenagakerjaan</span>
                        <span class="font-bold text-error">Rp 150.000</span>
                    </div>
                    <div class="flex justify-between items-center font-body-md transition-all duration-200 px-2 py-1 rounded-md hover:bg-surface-container-low">
                        <span class="text-on-surface-variant">PPh 21</span>
                        <span class="font-bold text-error">Rp 100.000</span>
                    </div>
                    <!-- Filler for equal height -->
                    <div class="h-10 opacity-0">Filler</div>
                    <div class="h-4 opacity-0">Filler</div>
                    <div class="pt-3 border-t border-outline-variant flex justify-between items-center">
                        <span class="font-bold text-error">Total Potongan</span>
                        <span class="font-display-lg text-title-sm text-error">Rp 450.000</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Net Salary Summary -->
        <div class="bg-primary-container rounded-xl p-lg text-white mb-xl flex flex-col md:flex-row justify-between items-center shadow-lg transform hover:scale-[1.01] transition-transform">
            <div class="mb-4 md:mb-0">
                <p class="font-label-uppercase opacity-80 uppercase tracking-widest">Total Gaji Bersih Terbayarkan</p>
                <h4 class="font-display-lg text-display-lg font-extrabold">Rp 6.000.000</h4>
                <p class="font-body-sm italic opacity-70">Enam Juta Rupiah</p>
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
        <div class="flex flex-col md:flex-row justify-between items-end">
            <div class="space-y-4 max-w-xs">
                <div class="bg-surface-container p-4 rounded-lg border-l-4 border-outline">
                    <p class="text-[11px] leading-tight text-on-surface-variant">
                        <strong>CATATAN:</strong><br>
                        Ini adalah dokumen yang dihasilkan secara otomatis oleh sistem HRDApps. 
                        Segala bentuk perubahan fisik tanpa persetujuan HRD dianggap tidak sah.
                    </p>
                </div>
                <div class="text-[10px] text-on-surface-variant/60 font-mono">
                    ID Dokumen: HR-SLIP-2026-JUN-001-AF
                </div>
            </div>
            <div class="text-center mt-xl md:mt-0">
                <p class="font-label-uppercase text-on-surface-variant mb-md">Diterbitkan oleh Digital HRD</p>
                <div class="w-32 h-32 mx-auto relative group">
                    <!-- Digital Seal Placeholder -->
                    <div class="absolute inset-0 bg-primary/5 rounded-full border-2 border-dashed border-primary/20 animate-spin-slow"></div>
                    <img class="w-full h-full object-contain relative z-10 opacity-80 grayscale group-hover:grayscale-0 transition-all" alt="Digital Signature" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDhwQ7ReZyH75moikS87Uw4Qv8-y1HQIaJxzBRg1TauBatd7fcL74n6HBDpMqDibuy0dFB3I33TQaqLKuQU-069EgLatMBwpw9Os2hi-b47ycgXhZpsL-oKxleoMLqKSZZ2JguudMPPBqA3ysCw_Xw_hF6JE_MyNVXMumcWN1KiIQBZFfQ6pRLMkfggZL7Ye_e7JaQQX19mztI78UiZVjXb83ar3hKb74x0ePOAFxXbBW9I7q5RZh4p">
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

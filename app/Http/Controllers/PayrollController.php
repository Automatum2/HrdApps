<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\PayrollPeriod;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\Allowance;
use App\Models\Deduction;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index()
    {
        $role = session('user_role');
        
        if (in_array($role, ['karyawan', 'manager_departemen'])) {
            $employeeId = session('employee_id');
            $employee = Employee::with('department', 'position')->find($employeeId);
            
            if (!$employee) {
                return redirect()->route('backoffice.dashboard')->with('error', 'Data karyawan tidak ditemukan.');
            }

            $hrManagerUser = \App\Models\User::where('role', 'hr_manager')->with('employee')->first();
            $hrManagerName = $hrManagerUser && $hrManagerUser->employee ? $hrManagerUser->employee->nama_lengkap : ($hrManagerUser->username ?? 'HR Manager');
            
            $latestPayroll = Payroll::where('employee_id', $employeeId)
                ->whereIn('status', ['approved', 'paid'])
                ->orderBy('id', 'desc')
                ->first();
                
            if ($latestPayroll) {
                $period = PayrollPeriod::find($latestPayroll->period_id);
                $monthName = $period ? $period->nama_periode : Carbon::now()->translatedFormat('F Y');
                
                $month = Carbon::parse($period->tanggal_mulai)->month ?? Carbon::now()->month;
                $year = Carbon::parse($period->tanggal_mulai)->year ?? Carbon::now()->year;
                $calc = self::calculatePayroll($employee, $month, $year);
                
                $data = array_merge($calc, $latestPayroll->toArray()); 
                $data['employee'] = $employee;
                $data['monthName'] = $monthName;
                $data['terbilangGaji'] = trim(self::terbilang($latestPayroll->gaji_bersih)) . " Rupiah";
                $data['gajiPokok'] = $latestPayroll->gaji_pokok;
                $data['totalTunjangan'] = $latestPayroll->total_tunjangan;
                $data['totalPotongan'] = $latestPayroll->total_potongan;
                $data['gajiBersih'] = $latestPayroll->gaji_bersih;
                $data['currentTime'] = Carbon::now();
                $data['hrManagerName'] = $hrManagerName;
                
                return view('backoffice.slip_gaji_karyawan', $data);
            } else {
                $currentMonth = Carbon::now()->month;
                $currentYear = Carbon::now()->year;
                $data = self::calculatePayroll($employee, $currentMonth, $currentYear);
                $data['monthName'] = Carbon::now()->translatedFormat('F Y');
                $data['currentTime'] = Carbon::now();
                $data['hrManagerName'] = $hrManagerName;
                return view('backoffice.slip_gaji_karyawan', $data);
            }
        }

        // For Manager/Super Admin
        $periods = PayrollPeriod::orderBy('tanggal_mulai', 'desc')->get();
        $currentTime = Carbon::now();
        return view('backoffice.penggajian_periode', compact('periods', 'currentTime'));
    }

    public function storePeriod(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        PayrollPeriod::create([
            'nama_periode' => $request->nama_periode,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => 'draft',
        ]);

        return redirect()->route('backoffice.penggajian')->with('success', 'Periode penggajian baru berhasil dibuat.');
    }

    public function show($id)
    {
        $period = PayrollPeriod::findOrFail($id);
        $payrolls = Payroll::with('employee.department', 'employee.position')
            ->where('period_id', $id)
            ->get();
            
        $totalGajiBersih = $payrolls->sum('gaji_bersih');
        $monthName = $period->nama_periode;
        
        // Menghitung tahapan (currentStep) secara dinamis
        $currentStep = 1; // Default: Tarik Data (belum ada data penggajian)
        
        if ($payrolls->isNotEmpty()) {
            $allApproved = $payrolls->every(function ($payroll) {
                return $payroll->status === 'approved';
            });
            
            if ($allApproved) {
                $currentStep = 4; // Semua approved -> Distribusi
            } else {
                $currentStep = 3; // Ada data tapi belum semua approved -> Review & Approve
            }
        }
        
        return view('backoffice.penggajian', compact('period', 'payrolls', 'totalGajiBersih', 'monthName', 'currentStep'));
    }

    public function generate(Request $request, $id)
    {
        $period = PayrollPeriod::findOrFail($id);
        $employees = Employee::whereIn('status', ['aktif', 'tetap', 'kontrak'])->get(); // depending on actual status values, assuming 'aktif' or anything not 'nonaktif'
        if($employees->isEmpty()){
            $employees = Employee::all();
        }
        
        $month = Carbon::parse($period->tanggal_mulai)->month;
        $year = Carbon::parse($period->tanggal_mulai)->year;
        
        foreach ($employees as $employee) {
            if (auth()->check() && !auth()->user()->can('manage-payslip', $employee)) {
                continue;
            }

            $data = self::calculatePayroll($employee, $month, $year);
            
            $payroll = Payroll::updateOrCreate(
                ['employee_id' => $employee->id, 'period_id' => $period->id],
                [
                    'gaji_pokok' => $data['gajiPokok'],
                    'total_tunjangan' => $data['totalTunjangan'],
                    'total_potongan' => $data['totalPotongan'],
                    'total_lembur' => 0,
                    'gaji_kotor' => $data['gajiKotor'],
                    'gaji_bersih' => $data['gajiBersih'],
                    'status' => 'draft'
                ]
            );

            // Sync Payroll Details
            PayrollDetail::where('payroll_id', $payroll->id)->delete();
            
            foreach ($data['allowancesList'] as $allowance) {
                PayrollDetail::create([
                    'payroll_id' => $payroll->id,
                    'komponen' => $allowance['nama'],
                    'tipe' => 'pendapatan',
                    'jumlah' => $allowance['jumlah']
                ]);
            }

            foreach ($data['deductionsList'] as $deduction) {
                PayrollDetail::create([
                    'payroll_id' => $payroll->id,
                    'komponen' => $deduction['nama'],
                    'tipe' => 'potongan',
                    'jumlah' => $deduction['jumlah']
                ]);
            }
        }
        
        if ($period->status === 'draft') {
            $period->update(['status' => 'proses']);
        }
        
        return redirect()->back()->with('success', 'Data penggajian berhasil di-generate/dihitung ulang.');
    }

    public function approve($payroll_id)
    {
        $payroll = Payroll::findOrFail($payroll_id);
        $payroll->update([
            'status' => 'approved',
            'approved_by' => session('user_name')
        ]);
        
        return redirect()->back()->with('success', 'Gaji karyawan berhasil disetujui.');
    }

    public function approveAll($id)
    {
        Payroll::where('period_id', $id)
            ->where('status', 'draft')
            ->update([
                'status' => 'approved',
                'approved_by' => session('user_name')
            ]);
            
        $period = PayrollPeriod::findOrFail($id);
        if ($period->status === 'proses') {
            $period->update(['status' => 'selesai']);
        }
            
        return redirect()->back()->with('success', 'Seluruh data penggajian periode ini berhasil disetujui.');
    }

    public function downloadPdf($id)
    {
        // Cegah IDOR: Karyawan biasa HANYA boleh mengunduh slip gajinya sendiri
        if (session('user_role') === 'karyawan' && session('employee_id') != $id) {
            return redirect()->back()->with('error', 'Akses Ditolak: Anda hanya dapat mengunduh slip gaji milik Anda sendiri.');
        }

        $employee = Employee::with('department', 'position')->find($id);
        
        if (!$employee) {
            return redirect()->back()->with('error', 'Karyawan tidak ditemukan.');
        }

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $data = self::calculatePayroll($employee, $currentMonth, $currentYear);
        $data['monthName'] = Carbon::now()->translatedFormat('F Y');
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('backoffice.pdf.slip_gaji', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $fileName = 'Slip_Gaji_' . str_replace(' ', '_', $employee->nama_lengkap) . '_' . date('M_Y') . '.pdf';
        
        return $pdf->download($fileName);
    }

    public function downloadMassPdf($id)
    {
        $period = PayrollPeriod::findOrFail($id);
        $payrolls = Payroll::with('employee.department', 'employee.position')
            ->where('period_id', $id)
            ->whereIn('status', ['approved', 'paid'])
            ->get();
            
        if ($payrolls->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada slip gaji yang disetujui (Approved) pada periode ini.');
        }

        $month = Carbon::parse($period->tanggal_mulai)->month;
        $year = Carbon::parse($period->tanggal_mulai)->year;
        
        $payrollsData = [];
        foreach($payrolls as $payroll) {
            $data = self::calculatePayroll($payroll->employee, $month, $year);
            $data['monthName'] = $period->nama_periode;
            $payrollsData[] = $data;
        }
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('backoffice.pdf.slip_gaji_massal', ['payrollsData' => $payrollsData, 'monthName' => $period->nama_periode]);
        $pdf->setPaper('A4', 'portrait');
        
        $fileName = 'Slip_Gaji_Massal_' . str_replace(' ', '_', $period->nama_periode) . '.pdf';
        
        return $pdf->download($fileName);
    }

    public static function calculatePayroll($employee, $month, $year)
    {
        $gajiPokok = $employee->gaji_pokok ?? 0;
        
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get();
            
        $hariKerjaNormal = 22;
        $hadir = $attendances->where('status_kehadiran', 'hadir')->count();
        $sakit = $attendances->where('status_kehadiran', 'sakit')->count();
        $izin = $attendances->where('status_kehadiran', 'izin')->count();
        $cuti = $attendances->where('status_kehadiran', 'cuti')->count();
        $alpha = $hariKerjaNormal - ($hadir + $sakit + $izin + $cuti);
        if ($alpha < 0) $alpha = 0;
        
        $allowancesList = [];
        $totalTunjangan = 0;
        
        if ($employee->position && $employee->position->tunjangan_jabatan > 0) {
            $allowancesList[] = [
                'nama' => 'Tunjangan Jabatan',
                'jumlah' => $employee->position->tunjangan_jabatan
            ];
            $totalTunjangan += $employee->position->tunjangan_jabatan;
        }

        foreach ($employee->allowances()->where('is_active', true)->get() as $allowance) {
            $jumlah = $allowance->jumlah;
            if ($allowance->tipe === 'tidak_tetap') {
                $jumlah = $hadir * $allowance->jumlah;
            }
            $allowancesList[] = [
                'nama' => $allowance->nama_tunjangan,
                'jumlah' => $jumlah
            ];
            $totalTunjangan += $jumlah;
        }
        
        $deductionsList = [];
        $totalPotongan = 0;

        $potonganBPJSKesehatan = $gajiPokok * 0.04; 
        $potonganBPJSKetenagakerjaan = $gajiPokok * 0.02; 
        
        $deductionsList[] = [
            'nama' => 'BPJS Kesehatan (4%)',
            'jumlah' => $potonganBPJSKesehatan
        ];
        $deductionsList[] = [
            'nama' => 'BPJS Ketenagakerjaan (2%)',
            'jumlah' => $potonganBPJSKetenagakerjaan
        ];
        $totalPotongan += ($potonganBPJSKesehatan + $potonganBPJSKetenagakerjaan);

        foreach ($employee->deductions()->where('is_active', true)->get() as $deduction) {
            $jumlah = $deduction->jumlah;
            if ($deduction->tipe === 'tidak_tetap') {
                if (stripos($deduction->nama_potongan, 'alpha') !== false || stripos($deduction->nama_potongan, 'mangkir') !== false) {
                     $jumlah = $alpha * $deduction->jumlah;
                } else {
                     $jumlah = $hadir * $deduction->jumlah;
                }
            }
            
            if ($jumlah > 0) {
                $deductionsList[] = [
                    'nama' => $deduction->nama_potongan,
                    'jumlah' => $jumlah
                ];
                $totalPotongan += $jumlah;
            }
        }
        
        $gajiKotor = $gajiPokok + $totalTunjangan;
        $gajiBersih = $gajiKotor - $totalPotongan;
        
        return [
            'employee' => $employee,
            'gajiPokok' => $gajiPokok,
            'hadir' => $hadir,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpha' => $alpha,
            'allowancesList' => $allowancesList,
            'deductionsList' => $deductionsList,
            'totalTunjangan' => $totalTunjangan,
            'totalPotongan' => $totalPotongan,
            'gajiKotor' => $gajiKotor,
            'gajiBersih' => $gajiBersih,
            'terbilangGaji' => trim(self::terbilang($gajiBersih)) . " Rupiah",
        ];
    }
    
    private static function terbilang($angka) {
        $angka = abs(floor($angka));
        $baca = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $hasil = "";
        if ($angka < 12) {
            $hasil = " " . $baca[$angka];
        } else if ($angka < 20) {
            $hasil = self::terbilang($angka - 10) . " Belas";
        } else if ($angka < 100) {
            $hasil = self::terbilang($angka / 10) . " Puluh" . self::terbilang($angka % 10);
        } else if ($angka < 200) {
            $hasil = " Seratus" . self::terbilang($angka - 100);
        } else if ($angka < 1000) {
            $hasil = self::terbilang($angka / 100) . " Ratus" . self::terbilang($angka % 100);
        } else if ($angka < 2000) {
            $hasil = " Seribu" . self::terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $hasil = self::terbilang($angka / 1000) . " Ribu" . self::terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $hasil = self::terbilang($angka / 1000000) . " Juta" . self::terbilang($angka % 1000000);
        }
        return $hasil;
    }
}

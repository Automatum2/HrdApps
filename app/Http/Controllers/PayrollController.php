<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\PayrollPeriod;
use App\Models\Payroll;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index()
    {
        $role = session('user_role', 'manager');
        
        if ($role === 'employee') {
            $employeeId = session('employee_id');
            $employee = Employee::with('department', 'position')->find($employeeId);
            
            if (!$employee) {
                return redirect()->route('backoffice.dashboard')->with('error', 'Data karyawan tidak ditemukan.');
            }
            
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
                
                return view('backoffice.slip_gaji_karyawan', $data);
            } else {
                $currentMonth = Carbon::now()->month;
                $currentYear = Carbon::now()->year;
                $data = self::calculatePayroll($employee, $currentMonth, $currentYear);
                $data['monthName'] = Carbon::now()->translatedFormat('F Y');
                return view('backoffice.slip_gaji_karyawan', $data);
            }
        }

        // For Manager/Super Admin
        $periods = PayrollPeriod::orderBy('tanggal_mulai', 'desc')->get();
        return view('backoffice.penggajian_periode', compact('periods'));
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
        
        return view('backoffice.penggajian', compact('period', 'payrolls', 'totalGajiBersih', 'monthName'));
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
            $data = self::calculatePayroll($employee, $month, $year);
            
            Payroll::updateOrCreate(
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
            'approved_by' => session('user_name', 'HR Manager')
        ]);
        
        return redirect()->back()->with('success', 'Gaji karyawan berhasil disetujui.');
    }

    public function approveAll($id)
    {
        Payroll::where('period_id', $id)
            ->where('status', 'draft')
            ->update([
                'status' => 'approved',
                'approved_by' => session('user_name', 'HR Manager')
            ]);
            
        $period = PayrollPeriod::findOrFail($id);
        if ($period->status === 'proses') {
            $period->update(['status' => 'selesai']);
        }
            
        return redirect()->back()->with('success', 'Seluruh data penggajian periode ini berhasil disetujui.');
    }

    public function downloadPdf($id)
    {
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
        $alpha = $hariKerjaNormal - ($hadir + $sakit + $izin);
        if ($alpha < 0) $alpha = 0;
        
        $tunjanganMakan = $hadir * 30000;
        $tunjanganTransport = $hadir * 20000;
        $tunjanganJabatan = $employee->position ? 300000 : 0; 
        
        $totalTunjangan = $tunjanganMakan + $tunjanganTransport + $tunjanganJabatan;
        
        $potonganBPJSKesehatan = $gajiPokok * 0.04; 
        $potonganBPJSKetenagakerjaan = $gajiPokok * 0.02; 
        $potonganAlpha = $alpha * 100000; 
        $totalPotongan = $potonganBPJSKesehatan + $potonganBPJSKetenagakerjaan + $potonganAlpha;
        
        $gajiKotor = $gajiPokok + $totalTunjangan;
        $gajiBersih = $gajiKotor - $totalPotongan;
        
        return [
            'employee' => $employee,
            'gajiPokok' => $gajiPokok,
            'hadir' => $hadir,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpha' => $alpha,
            'tunjanganMakan' => $tunjanganMakan,
            'tunjanganTransport' => $tunjanganTransport,
            'tunjanganJabatan' => $tunjanganJabatan,
            'totalTunjangan' => $totalTunjangan,
            'potonganBPJSKesehatan' => $potonganBPJSKesehatan,
            'potonganBPJSKetenagakerjaan' => $potonganBPJSKetenagakerjaan,
            'potonganAlpha' => $potonganAlpha,
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

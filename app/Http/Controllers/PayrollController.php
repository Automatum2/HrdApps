<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index()
    {
        $role = session('user_role', 'manager');
        
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $monthName = Carbon::now()->translatedFormat('F Y');
        
        if ($role === 'employee') {
            $employeeId = session('employee_id');
            $employee = Employee::with('department', 'position')->find($employeeId);
            
            if (!$employee) {
                return redirect()->route('backoffice.dashboard')->with('error', 'Data karyawan tidak ditemukan.');
            }
            
            $data = self::calculatePayroll($employee, $currentMonth, $currentYear);
            $data['monthName'] = $monthName;
            
            return view('backoffice.slip_gaji_karyawan', $data);
        }

        // For Manager/Super Admin
        $employees = Employee::with('department', 'position')->get();
        $payrolls = [];
        $totalGajiBersih = 0;
        
        foreach ($employees as $employee) {
            $data = self::calculatePayroll($employee, $currentMonth, $currentYear);
            $data['employee'] = $employee;
            $data['status'] = 'Draft'; // Default for now
            $payrolls[] = $data;
            $totalGajiBersih += $data['gajiBersih'];
        }
        
        return view('backoffice.penggajian', compact('payrolls', 'totalGajiBersih', 'monthName'));
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\User;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Position;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $role = session('user_role');
        
        // Super Admin Dashboard
        if ($role === 'super_admin') {
            $total_karyawan = Employee::where('is_cv_approved', true)->count();
            
            // Hitung persentase kenaikan karyawan dari bulan lalu
            $karyawan_bulan_ini = Employee::where('is_cv_approved', true)
                                                        ->whereMonth('created_at', now()->month)
                                                        ->whereYear('created_at', now()->year)
                                                        ->count();
            $karyawan_bulan_lalu = Employee::where('is_cv_approved', true)
                                                        ->whereMonth('created_at', now()->subMonth()->month)
                                                        ->whereYear('created_at', now()->subMonth()->year)
                                                        ->count();
                                                        
            if ($karyawan_bulan_lalu > 0) {
                $kenaikan_karyawan = round((($karyawan_bulan_ini - $karyawan_bulan_lalu) / $karyawan_bulan_lalu) * 100, 1);
            } else {
                $kenaikan_karyawan = $karyawan_bulan_ini > 0 ? 100 : 0;
            }

            $total_manager = User::where('role', 'hr_manager')->count();
            $latest_managers = User::where('role', 'hr_manager')->with('employee')->orderBy('id', 'desc')->take(5)->get();
            $total_departemen = DB::table('departments')->count();

            return view('backoffice.super_admin_dashboard', compact('total_karyawan', 'kenaikan_karyawan', 'total_manager', 'latest_managers', 'total_departemen'));
        } 
        
        // Employee Dashboard
        elseif ($role === 'karyawan') {
            $employeeId = session('employee_id');
            $employee = Employee::where('id', $employeeId)->orWhere('nik', $employeeId)->first();
            
            $masaKerja = 'N/A';
            if ($employee && $employee->tanggal_masuk) {
                $joinDate = Carbon::parse($employee->tanggal_masuk);
                $now = Carbon::now();
                $diff = $joinDate->diff($now);
                
                $parts = [];
                if ($diff->y > 0) $parts[] = $diff->y . ' Tahun';
                if ($diff->m > 0) $parts[] = $diff->m . ' Bulan';
                
                if (empty($parts)) {
                    $masaKerja = $diff->d . ' Hari';
                } else {
                    $masaKerja = implode(', ', $parts);
                }
            }
            
            $lastMonth = Carbon::now()->subMonth();
            $lastPayroll = null;
            if ($employee) {
                $lastPayrollDb = Payroll::where('employee_id', $employee->id)
                    ->whereIn('status', ['approved', 'paid'])
                    ->orderBy('id', 'desc')
                    ->first();
                
                if ($lastPayrollDb) {
                    $lastPayroll = [
                        'gajiBersih' => $lastPayrollDb->gaji_bersih,
                        'gajiKotor' => $lastPayrollDb->gaji_kotor,
                        'totalPotongan' => $lastPayrollDb->total_potongan,
                        'status' => $lastPayrollDb->status
                    ];
                } else {
                    $lastPayroll = \App\Http\Controllers\PayrollController::calculatePayroll($employee, $lastMonth->month, $lastMonth->year);
                }
            }

            $todayAttendance = Attendance::where('employee_id', $employeeId)->where('tanggal', Carbon::today()->toDateString())->first();
            $currentMonth = $request->input('month', Carbon::now()->month);
            $currentYear = $request->input('year', Carbon::now()->year);
            
            $historyAttendances = Attendance::where('employee_id', $employeeId)
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->orderBy('tanggal', 'desc')
                ->get();
                
            $hadirCount = Attendance::where('employee_id', $employeeId)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status_kehadiran', 'hadir')->count();
            $izinCount = Attendance::where('employee_id', $employeeId)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status_kehadiran', 'izin')->count();
            $sakitCount = Attendance::where('employee_id', $employeeId)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status_kehadiran', 'sakit')->count();
            $alphaCount = Attendance::where('employee_id', $employeeId)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status_kehadiran', 'alpha')->count();
            
            $cutiDanIzinTerpakai = Attendance::where('employee_id', $employeeId)
                ->whereYear('tanggal', $currentYear)
                ->whereIn('status_kehadiran', ['izin', 'cuti'])
                ->count();
            $sisaCutiTahunan = max(0, ($employee->kuota_cuti ?? 12) - $cutiDanIzinTerpakai);
            
            $monthlyAttendances = Attendance::where('employee_id', $employeeId)
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->get()
                ->keyBy('tanggal');
                
            $targetDate = Carbon::create($currentYear, $currentMonth, 1);
            $firstDayOfMonth = $targetDate->dayOfWeekIso; // 1 = Senin, 7 = Minggu
            $daysInMonth = $targetDate->daysInMonth;
            
            $monthlyTrainings = collect();
            if ($employee) {
                $monthlyTrainings = $employee->trainings()
                    ->where(function($q) use ($currentYear, $currentMonth) {
                        $q->whereYear('tanggal_mulai', $currentYear)->whereMonth('tanggal_mulai', $currentMonth)
                          ->orWhere(function($q2) use ($currentYear, $currentMonth) {
                              $q2->whereYear('tanggal_selesai', $currentYear)->whereMonth('tanggal_selesai', $currentMonth);
                          });
                    })->get();
            }
            
            return view('backoffice.dashboard_karyawan', compact('todayAttendance', 'historyAttendances', 'hadirCount', 'izinCount', 'sakitCount', 'alphaCount', 'masaKerja', 'lastPayroll', 'lastMonth', 'sisaCutiTahunan', 'monthlyAttendances', 'currentMonth', 'currentYear', 'daysInMonth', 'firstDayOfMonth', 'monthlyTrainings'));
        }
        
        // Manager Dashboard (default fallback for 'manager')
        $user = Auth::user();
        $employeeId = session('employee_id') ?? ($user ? ($user->employee_id ?? ($user->employee ? $user->employee->id : null)) : null);
        $todayAttendance = $employeeId ? Attendance::where('employee_id', $employeeId)->where('tanggal', Carbon::today()->toDateString())->first() : null;
        
        if ($role === 'manager_departemen' && $user && $user->employee) {
            $department_id = $user->employee->department_id;
            
            $total_karyawan_dept = Employee::where('is_cv_approved', true)->where('department_id', $department_id)->count();
            
            $hadir_hari_ini_dept = Attendance::where('tanggal', Carbon::today()->toDateString())
                ->where('status_kehadiran', 'hadir')
                ->whereHas('employee', function($q) use ($department_id) {
                    $q->where('department_id', $department_id);
                })->count();
                
            $belum_absen_dept = $total_karyawan_dept > $hadir_hari_ini_dept ? $total_karyawan_dept - $hadir_hari_ini_dept : 0;
            
            $total_karyawan_perusahaan = Employee::where('is_cv_approved', true)->count();
            
            $latest_employees = Employee::where('is_cv_approved', true)
                ->where('department_id', $department_id)
                ->with('department', 'position')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
                
            $status_tetap = Employee::where('is_cv_approved', true)->where('status_kerja', 'tetap')->where('department_id', $department_id)->count();
            $status_kontrak = Employee::where('is_cv_approved', true)->where('status_kerja', 'kontrak')->where('department_id', $department_id)->count();
            $status_magang = Employee::where('is_cv_approved', true)->where('status_kerja', 'magang')->where('department_id', $department_id)->count();
            
            $attendance_trend = ['labels' => [], 'data' => []];
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $weeks = [
                'Minggu 1' => [1, 7],
                'Minggu 2' => [8, 14],
                'Minggu 3' => [15, 21],
                'Minggu 4' => [22, Carbon::now()->endOfMonth()->day]
            ];
            foreach ($weeks as $weekName => $range) {
                $start = Carbon::create($currentYear, $currentMonth, $range[0])->toDateString();
                $end = Carbon::create($currentYear, $currentMonth, $range[1])->toDateString();
                $count = Attendance::whereBetween('tanggal', [$start, $end])
                    ->where('status_kehadiran', 'hadir')
                    ->whereHas('employee', function($q) use ($department_id) {
                        $q->where('department_id', $department_id);
                    })->count();
                $attendance_trend['labels'][] = $weekName;
                $attendance_trend['data'][] = $count;
            }
            
            return view('backoffice.dashboard_manager', compact(
                'total_karyawan_dept', 'hadir_hari_ini_dept', 'belum_absen_dept', 
                'total_karyawan_perusahaan', 'latest_employees', 
                'status_tetap', 'status_kontrak', 'status_magang', 'attendance_trend', 'todayAttendance'
            ));
        }
        
        // General Dashboard (HR Manager / Admin fallback)
        $employeeQuery = Employee::query();
        $attendanceQuery = Attendance::query();
        
        $total_karyawan = Employee::where('is_cv_approved', true)->count();
        $hadir_hari_ini = Attendance::where('tanggal', Carbon::today()->toDateString())->where('status_kehadiran', 'hadir')->count();
        $belum_absen = $total_karyawan > $hadir_hari_ini ? $total_karyawan - $hadir_hari_ini : 0;
        
        $latestEmployeesQuery = Employee::where('is_cv_approved', true)->with('department', 'position')->orderBy('created_at', 'desc')->take(5);
        if ($role === 'manager_departemen' && $user && $user->employee) {
            $latestEmployeesQuery->where('department_id', $user->employee->department_id);
        }
        $latest_employees = $latestEmployeesQuery->get();
        
        // Calculate total payroll for current month
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $employeesQueryWithRel = Employee::where('is_cv_approved', true)->with('department', 'position');
        if ($role === 'manager_departemen' && $user && $user->employee) {
            $employeesQueryWithRel->where('department_id', $user->employee->department_id);
        }
        $employees = $employeesQueryWithRel->get();
        $total_gaji_bulan_ini = 0;
        
        $status_tetap = Employee::where('is_cv_approved', true)->where('status_kerja', 'tetap')->count();
        $status_kontrak = Employee::where('is_cv_approved', true)->where('status_kerja', 'kontrak')->count();
        $status_magang = Employee::where('is_cv_approved', true)->where('status_kerja', 'magang')->count();
        
        foreach ($employees as $emp) {
            $data = \App\Http\Controllers\PayrollController::calculatePayroll($emp, $currentMonth, $currentYear);
            $total_gaji_bulan_ini += $data['gajiBersih'];
        }
        
        // Attendance trend logic
        $attendance_trend = ['labels' => [], 'data' => []];
        $weeks = [
            'Minggu 1' => [1, 7],
            'Minggu 2' => [8, 14],
            'Minggu 3' => [15, 21],
            'Minggu 4' => [22, Carbon::now()->endOfMonth()->day]
        ];
        foreach ($weeks as $weekName => $range) {
            $start = Carbon::create($currentYear, $currentMonth, $range[0])->toDateString();
            $end = Carbon::create($currentYear, $currentMonth, $range[1])->toDateString();
            $count = Attendance::whereBetween('tanggal', [$start, $end])->where('status_kehadiran', 'hadir')->count();
            $attendance_trend['labels'][] = $weekName;
            $attendance_trend['data'][] = $count;
        }
        
        // For Modal Tambah Karyawan Baru (assign department)
        $unassigned_employees = Employee::where('is_cv_approved', true)
            ->where(function($q) { 
                $q->whereNull('department_id')->orWhere('department_id', 0); 
            })
            ->whereDoesntHave('user', function ($q) {
                $q->whereIn('role', ['hr_manager', 'super_admin', 'manager_departemen']);
            })
            ->get();
        
        $departments = Department::orderBy('nama_department', 'asc')->get();
        $positions = Position::orderBy('nama_jabatan', 'asc')->get();

        return view('backoffice.dashboard', compact('total_karyawan', 'hadir_hari_ini', 'belum_absen', 'latest_employees', 'total_gaji_bulan_ini', 'status_tetap', 'status_kontrak', 'status_magang', 'unassigned_employees', 'attendance_trend', 'departments', 'positions', 'todayAttendance'));
    }
}

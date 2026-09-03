<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SuperAdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $role = session('user_role');
        if ($role !== 'super_admin') {
            abort(403, 'Akses khusus Super Admin.');
        }

        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $departmentId = $request->input('department_id');
        $search = $request->input('search');

        $query = Attendance::with(['employee.department', 'employee.user'])
            ->whereDate('tanggal', $tanggal);

        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($search) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $attendances = $query->orderBy('jam_masuk', 'desc')->get();
        $departments = Department::orderBy('nama_department')->get();

        // Calculate summary stats for today
        $totalHadir = Attendance::whereDate('tanggal', $tanggal)->whereIn('status_kehadiran', ['hadir', 'wfo', 'wfh'])->count();
        $totalIzin = Attendance::whereDate('tanggal', $tanggal)->whereIn('status_kehadiran', ['izin', 'sakit', 'cuti'])->count();
        $totalAlpha = Attendance::whereDate('tanggal', $tanggal)->where('status_kehadiran', 'alpha')->count();

        return view('backoffice.super_admin_absensi', compact(
            'attendances',
            'departments',
            'tanggal',
            'departmentId',
            'search',
            'totalHadir',
            'totalIzin',
            'totalAlpha'
        ));
    }
}

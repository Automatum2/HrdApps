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

        $dari = $request->input('dari_tanggal', Carbon::now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai_tanggal', Carbon::now()->endOfMonth()->toDateString());
        $departmentId = $request->input('departemen_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Attendance::with(['employee.department', 'employee.user'])
            ->whereBetween('tanggal', [$dari, $sampai]);

        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($status) {
            $query->where('status_kehadiran', $status);
        }

        if ($search) {
            $escapedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
            $query->whereHas('employee', function ($q) use ($escapedSearch) {
                $q->where('nama_lengkap', 'like', "%{$escapedSearch}%")
                  ->orWhere('nik', 'like', "%{$escapedSearch}%");
            });
        }

        // Hitung statistik untuk seluruh data hasil filter (sebelum dipaginate)
        $allFiltered = (clone $query)->get();
        $stats = [
            'hadir' => $allFiltered->where('status_kehadiran', 'hadir')->count(),
            'izin' => $allFiltered->where('status_kehadiran', 'izin')->count(),
            'sakit' => $allFiltered->where('status_kehadiran', 'sakit')->count(),
            'alpha' => $allFiltered->where('status_kehadiran', 'alpha')->count(),
            'cuti' => $allFiltered->where('status_kehadiran', 'cuti')->count(),
        ];

        $attendances = $query->orderBy('tanggal', 'desc')->orderBy('jam_masuk', 'desc')->paginate(10)->withQueryString();
        $departments = Department::orderBy('nama_department')->get();

        return view('backoffice.super_admin_absensi', compact(
            'attendances',
            'stats',
            'departments',
            'dari',
            'sampai',
            'departmentId',
            'status',
            'search'
        ));
    }
}

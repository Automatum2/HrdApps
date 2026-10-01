<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;

class EmployeeController extends Controller
{
    public function index()
    {
        if (session('user_role') !== 'super_admin') {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak. Halaman ini hanya untuk Super Admin.');
        }

        $employees = Employee::where('is_cv_approved', true)
            ->whereDoesntHave('user', function ($q) {
                $q->whereIn('role', ['hr_manager', 'super_admin', 'manager_departemen']);
            })
            ->orderBy('id', 'desc')->paginate(10);
        $departments = Department::orderBy('nama_department', 'asc')->get();
        $positions = Position::orderBy('nama_jabatan', 'asc')->get();

        return view('backoffice.super_admin_kelola_karyawan', compact('employees', 'departments', 'positions'));
    }

    public function manage(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $dbRole = $user ? $user->role : session('user_role');

        $employeesQuery = Employee::whereNotNull('department_id')
                        ->where('department_id', '>', 0)
                        ->where('is_cv_approved', true)
                        ->whereDoesntHave('user', function ($q) {
                            $q->whereIn('role', ['hr_manager', 'super_admin', 'manager_departemen']);
                        })
                        ->with(['department', 'position']);

        $unassignedQuery = Employee::where(function($q) {
                            $q->whereNull('department_id')
                              ->orWhere('department_id', 0);
                        })
                        ->where('is_cv_approved', true)
                        ->whereDoesntHave('user', function ($q) {
                            $q->whereIn('role', ['hr_manager', 'super_admin', 'manager_departemen']);
                        });

        if ($dbRole === 'manager_departemen' && $user && $user->employee) {
            $department_id = $user->employee->department_id;
            $employeesQuery->where('department_id', $department_id);
        }

        // Stats counter
        $statsBaseQuery = clone $employeesQuery;
        $allAssigned = $statsBaseQuery->get();
        $totalStaff = $allAssigned->count();
        $totalAktif = $allAssigned->where('status', 'aktif')->count();
        
        $todayStr = \Carbon\Carbon::today()->toDateString();
        $totalCuti = \App\Models\Attendance::where('tanggal', $todayStr)
            ->whereIn('status_kehadiran', ['cuti', 'izin'])
            ->whereIn('employee_id', $allAssigned->pluck('id'))
            ->count();
            
        $totalBaru = $allAssigned->filter(function($emp) {
            return $emp->created_at && $emp->created_at->isCurrentMonth();
        })->count();

        $stats = [
            'total_staff' => $totalStaff,
            'aktif' => $totalAktif,
            'cuti' => $totalCuti,
            'baru' => $totalBaru
        ];

        if ($request->filled('status')) {
            $statusVal = strtolower($request->status);
            if (in_array($statusVal, ['aktif', 'nonaktif'])) {
                $employeesQuery->where('status', $statusVal);
            } else {
                $employeesQuery->where('status_kerja', $statusVal);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $employeesQuery->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $employees = $employeesQuery->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        if ($dbRole === 'manager_departemen') {
            $unassigned_employees = collect([]);
        } else {
            $unassigned_employees = $unassignedQuery->orderBy('created_at', 'desc')->get();
        }

        $departments = Department::orderBy('nama_department', 'asc')->get();
        $positions = Position::orderBy('nama_jabatan', 'asc')->get();

        return view('backoffice.karyawan', compact('employees', 'unassigned_employees', 'departments', 'positions', 'stats'));
    }

    public function export(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $dbRole = $user->role;

        $employeesQuery = Employee::whereNotNull('department_id')
                        ->where('department_id', '>', 0)
                        ->where('is_cv_approved', true)
                        ->with(['department', 'position']);

        if ($dbRole === 'manager_departemen' && $user && $user->employee) {
            $department_id = $user->employee->department_id;
            $employeesQuery->where('department_id', $department_id);
        }

        $employees = $employeesQuery->orderBy('created_at', 'desc')->get();

        $csvFileName = 'Data_Karyawan_' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = array('NIK', 'Nama Lengkap', 'Email', 'Departemen', 'Jabatan', 'Status', 'Gaji Pokok');

        $callback = function() use($employees, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($employees as $emp) {
                $row['NIK']  = $emp->nik;
                $row['Nama Lengkap'] = $emp->nama_lengkap;
                $row['Email']  = $emp->email;
                $row['Departemen'] = $emp->department ? $emp->department->nama_department : '-';
                $row['Jabatan'] = $emp->position ? $emp->position->nama_jabatan : '-';
                $row['Status'] = ucfirst($emp->status_kerja);
                $row['Gaji Pokok'] = 'Rp ' . number_format($emp->gaji_pokok, 0, ',', '.');
                fputcsv($file, array($row['NIK'], $row['Nama Lengkap'], $row['Email'], $row['Departemen'], $row['Jabatan'], $row['Status'], $row['Gaji Pokok']));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updateGaji(Request $request, $id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->role !== 'hr_manager') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya HR Manager yang dapat mengubah gaji pokok.');
        }

        $request->validate([
            'gaji_pokok' => 'required|numeric'
        ]);

        $employee = Employee::findOrFail($id);
        $employee->gaji_pokok = $request->gaji_pokok;
        $employee->save();

        return redirect()->back()->with('success', 'Gaji pokok berhasil diperbarui.');
    }

    public function lepasDepartemen(Request $request)
    {
        $emp = Employee::where('nik', $request->nik)->first();
        if ($emp) {
            $emp->department_id = null;
            $emp->save();
            return redirect()->back()->with('success', 'Karyawan berhasil dilepas dari departemen.');
        }
        return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
    }

    public function assignDepartemen(Request $request)
    {
        $emp = Employee::where('nik', $request->nik)->first();
        if ($emp) {
            $dept = Department::where('id', $request->departemen)
                ->orWhere('nama_department', $request->departemen)
                ->first();

            if ($dept) {
                $emp->department_id = $dept->id;
            } else {
                return redirect()->back()->with('error', 'Departemen tidak ditemukan.');
            }

            if ($request->filled('jabatan')) {
                $pos = Position::firstOrCreate(
                    ['nama_jabatan' => trim($request->jabatan)],
                    ['level' => 'staff', 'tunjangan_jabatan' => 0]
                );
                $emp->position_id = $pos->id;
            }

            if ($request->filled('status')) {
                $statusMap = [
                    'Tetap' => 'tetap',
                    'Kontrak' => 'kontrak',
                    'Magang' => 'magang',
                    'Musiman' => 'musiman',
                    'Harian (DW)' => 'harian',
                    'Harian' => 'harian',
                    'Tidak Tetap' => 'tenaga_lepas',
                    'Tenaga Lepas' => 'tenaga_lepas',
                ];
                $emp->status_kerja = $statusMap[$request->status] ?? strtolower($request->status);
            }

            $emp->save();
            return redirect()->back()->with('success', 'Karyawan berhasil ditempatkan ke departemen.');
        }
        return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
    }

    public function show($id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $role = $user ? $user->role : session('user_role');

        if (!in_array($role, ['super_admin', 'hr_manager', 'manager_departemen'])) {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $employee = Employee::with(['department', 'position', 'user', 'documents'])->findOrFail($id);

        if ($role === 'manager_departemen' && $user && $user->employee) {
            if ($employee->department_id !== $user->employee->department_id) {
                return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak. Karyawan berada di luar departemen Anda.');
            }
        }
        
        // Pass a 'back_route' variable to know where the "Kembali" button should point
        $back_route = $role === 'super_admin' ? route('backoffice.super_admin.kelola_karyawan') : route('backoffice.karyawan'); 

        return view('backoffice.detail_karyawan', compact('employee', 'back_route'));
    }

    public function update(Request $request, $id)
    {
        if (session('user_role') !== 'super_admin') {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $employee = Employee::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => [
                'required', 'string', 'email', 'max:255',
                \Illuminate\Validation\Rule::unique('employees')->ignore($employee->id),
                $employee->user ? \Illuminate\Validation\Rule::unique('users')->ignore($employee->user->id) : ''
            ],
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'status_kerja' => 'nullable|in:tetap,kontrak,magang,musiman,harian,tenaga_lepas',
        ]);

        $employee->update([
            'nama_lengkap' => $request->nama,
            'email' => $request->email,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'gaji_pokok' => $request->gaji_pokok ?? 0,
            'status_kerja' => $request->status_kerja ?? 'tetap',
        ]);

        if ($employee->user) {
            $employee->user->update([
                'email' => $request->email,
            ]);
        }

        return redirect()->back()->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (session('user_role') !== 'super_admin') {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $employee = Employee::findOrFail($id);
        
        $employee->update(['status' => 'nonaktif']);
        
        return redirect()->back()->with('success', 'Status karyawan berhasil diubah menjadi Nonaktif.');
    }
}

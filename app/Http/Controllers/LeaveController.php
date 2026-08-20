<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index()
    {
        $role = session('user_role');
        $user = \Illuminate\Support\Facades\Auth::user();
        
        $query = LeaveRequest::with('employee.department');
        
        if ($role === 'manager_departemen' && $user && $user->employee) {
            $query->whereHas('employee', function($q) use ($user) {
                $q->where('department_id', $user->employee->department_id);
            });
            $query->whereIn('status', ['menunggu_manager', 'menunggu_hr', 'disetujui', 'ditolak']);
        } elseif (in_array($role, ['hr_manager', 'hr_admin_manager', 'hr_training_manager', 'superadmin'])) {
            $query->whereIn('status', ['menunggu_hr', 'disetujui', 'ditolak']);
        } else {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $leaves = $query->orderBy('created_at', 'desc')->get();
        return view('backoffice.leaves.index', compact('leaves'));
    }

    public function approve(Request $request, LeaveRequest $leave)
    {
        $role = session('user_role');
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($role === 'manager_departemen') {
            if ($leave->status !== 'menunggu_manager') {
                return back()->with('error', 'Cuti ini tidak dalam status menunggu persetujuan manager.');
            }
            $leave->status = 'menunggu_hr';
            $leave->manager_approved_at = now();
            $leave->manager_approved_by = $user->id;
            $leave->save();
            return back()->with('success', 'Cuti disetujui Manager. Diteruskan ke HR.');
        }

        if (in_array($role, ['hr_manager', 'hr_admin_manager', 'hr_training_manager', 'superadmin'])) {
            if ($leave->status !== 'menunggu_hr') {
                return back()->with('error', 'Cuti ini belum disetujui Manager atau sudah diproses.');
            }
            
            $leave->status = 'disetujui';
            $leave->hr_approved_at = now();
            $leave->hr_approved_by = $user->id;
            $leave->save();

            // Insert into Attendances
            $mulai = Carbon::parse($leave->tanggal_mulai);
            $selesai = Carbon::parse($leave->tanggal_selesai);
            
            for ($date = clone $mulai; $date->lte($selesai); $date->addDay()) {
                Attendance::updateOrCreate(
                    ['employee_id' => $leave->employee_id, 'tanggal' => $date->toDateString()],
                    [
                        'status_kerja' => 'WFO',
                        'status_kehadiran' => $leave->tipe,
                        'keterangan' => $leave->keterangan,
                        'dokumen_pendukung' => $leave->dokumen_pendukung
                    ]
                );
            }

            // Send Notification
            if ($leave->employee && $leave->employee->user) {
                $leave->employee->user->notify(new \App\Notifications\LeaveApprovedNotification($leave));
            }

            return back()->with('success', 'Cuti disetujui sepenuhnya.');
        }

        return back()->with('error', 'Tidak memiliki hak akses.');
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        $request->validate(['alasan_penolakan' => 'required|string']);
        
        $role = session('user_role');
        if ($role === 'manager_departemen' && $leave->status !== 'menunggu_manager') {
            return back()->with('error', 'Tidak valid.');
        }
        if (in_array($role, ['hr_manager', 'hr_admin_manager', 'hr_training_manager', 'superadmin']) && $leave->status !== 'menunggu_hr') {
            return back()->with('error', 'Tidak valid.');
        }

        $leave->status = 'ditolak';
        $leave->alasan_penolakan = $request->alasan_penolakan;
        $leave->save();

        return back()->with('success', 'Cuti ditolak.');
    }
}

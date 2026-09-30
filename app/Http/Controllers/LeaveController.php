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
        $user = \Illuminate\Support\Facades\Auth::user();
        $role = $user ? $user->role : session('user_role');
        
        $query = LeaveRequest::with('employee.department', 'employee.user');
        
        if ($role === 'manager_departemen' && $user && $user->employee) {
            $query->whereHas('employee', function($q) use ($user) {
                $q->where('department_id', $user->employee->department_id);
            });
            $query->whereIn('status', ['menunggu_manager', 'menunggu_hr', 'disetujui', 'ditolak']);
        } elseif (in_array($role, ['hr_manager', 'hr_admin_manager', 'hr_training_manager'])) {
            // HR Manager melihat cuti staf yang menunggu HR dan cuti Manager Departemen
            $query->whereIn('status', ['menunggu_hr', 'disetujui', 'ditolak']);
        } elseif ($role === 'super_admin') {
            // Super Admin melihat semua cuti, terutama yang menunggu persetujuan Super Admin (cuti HR Manager)
            $query->whereIn('status', ['menunggu_super_admin', 'menunggu_hr', 'menunggu_manager', 'disetujui', 'ditolak']);
        } else {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $leaves = $query->orderBy('created_at', 'desc')->get();
        return view('backoffice.leaves.index', compact('leaves'));
    }

    public function approve(Request $request, LeaveRequest $leave)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $role = $user ? $user->role : session('user_role');

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

        if (in_array($role, ['hr_manager', 'hr_admin_manager', 'hr_training_manager'])) {
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
                try {
                    $leave->employee->user->notify(new \App\Notifications\LeaveApprovedNotification($leave));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Gagal kirim notifikasi cuti: ' . $e->getMessage());
                }
            }

            return back()->with('success', 'Cuti disetujui sepenuhnya.');
        }

        if ($role === 'super_admin') {
            if (!in_array($leave->status, ['menunggu_super_admin', 'menunggu_hr'])) {
                return back()->with('error', 'Cuti tidak dalam status menunggu persetujuan.');
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

            if ($leave->employee && $leave->employee->user) {
                try {
                    $leave->employee->user->notify(new \App\Notifications\LeaveApprovedNotification($leave));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Gagal kirim notifikasi cuti: ' . $e->getMessage());
                }
            }

            return back()->with('success', 'Cuti disetujui oleh Super Admin.');
        }

        return back()->with('error', 'Tidak memiliki hak akses.');
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        $request->validate(['alasan_penolakan' => 'required|string']);
        
        $user = \Illuminate\Support\Facades\Auth::user();
        $role = $user ? $user->role : session('user_role');

        if ($role === 'manager_departemen' && $leave->status !== 'menunggu_manager') {
            return back()->with('error', 'Tidak valid.');
        }
        if (in_array($role, ['hr_manager', 'hr_admin_manager', 'hr_training_manager']) && $leave->status !== 'menunggu_hr') {
            return back()->with('error', 'Tidak valid.');
        }
        if ($role === 'super_admin' && !in_array($leave->status, ['menunggu_super_admin', 'menunggu_hr', 'menunggu_manager'])) {
            return back()->with('error', 'Tidak valid.');
        }

        $leave->status = 'ditolak';
        $leave->alasan_penolakan = $request->alasan_penolakan;
        $leave->save();

        return back()->with('success', 'Cuti ditolak.');
    }
}

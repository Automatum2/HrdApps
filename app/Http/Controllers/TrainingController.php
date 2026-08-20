<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Training;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use App\Notifications\TrainingAssignedNotification;
use Illuminate\Support\Facades\Notification;

class TrainingController extends Controller
{
    public function index()
    {
        $trainings = Training::with(['employees', 'manager'])->orderBy('created_at', 'desc')->get();
        // Hanya ambil karyawan aktif yang sudah disetujui CV-nya dan bukan HR Manager/Super Admin/Manager Departemen
        $employees = Employee::where('is_cv_approved', true)
            ->whereNotNull('department_id')
            ->whereDoesntHave('user', function($q) {
                $q->whereIn('role', ['hr_manager', 'super_admin', 'manager_departemen']);
            })
            ->orderBy('nama_lengkap', 'asc')
            ->get();
            
        return view('backoffice.training.index', compact('trainings', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_training' => 'nullable|string|max:255',
            'skill_dipelajari' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'employee_ids' => 'required|array|min:1|max:5',
            'employee_ids.*' => 'exists:employees,id'
        ], [
            'employee_ids.max' => 'Maksimal karyawan yang dapat ditugaskan adalah 5 orang.'
        ]);

        $training = Training::create([
            'nama_training' => $request->nama_training,
            'skill_dipelajari' => $request->skill_dipelajari,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'manager_id' => Auth::id()
        ]);

        $training->employees()->attach($request->employee_ids, ['status' => 'assigned']);

        // Mengirim notifikasi menggunakan custom notification
        $employees = Employee::whereIn('id', $request->employee_ids)->with('user')->get();
        foreach ($employees as $employee) {
            if ($employee->user) {
                $employee->user->notify(new TrainingAssignedNotification($training));
            }
        }

        return redirect()->route('backoffice.training.index')->with('success', 'Pelatihan berhasil dibuat dan ditugaskan.');
    }

    public function destroy($id)
    {
        $training = Training::findOrFail($id);
        $training->delete();
        return redirect()->route('backoffice.training.index')->with('success', 'Pelatihan berhasil dihapus.');
    }
}

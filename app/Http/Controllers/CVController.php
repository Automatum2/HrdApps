<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CVController extends Controller
{
    public function index()
    {
        // Tampilkan semua employee yang belum di-approve (pelamar)
        $applicants = Employee::where('is_cv_approved', false)->orderBy('created_at', 'desc')->get();
        return view('backoffice.cv.index', compact('applicants'));
    }

    public function approve(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        // Jika sudah di-approve, abaikan
        if ($employee->is_cv_approved) {
            return redirect()->back()->with('error', 'Pelamar ini sudah disetujui sebelumnya.');
        }

        // Generate NIK random
        $nik = 'EMP-' . rand(1000, 9999);

        // Update employee
        $employee->update([
            'nik' => $nik,
            'is_cv_approved' => true,
            'status_kerja' => 'magang', // masuk masa magang
            'status' => 'aktif'
        ]);

        // Create User account
        $user = User::create([
            'username' => strtolower(str_replace(' ', '', $employee->nama_lengkap)) . rand(10,99),
            'email' => $employee->email,
            'password' => Hash::make(Str::random(24)), // Random temporary password
            'role' => 'karyawan',
            'employee_id' => $employee->id
        ]);

        // Send activation link
        $token = \Illuminate\Support\Facades\Password::broker()->createToken($user);
        $user->notify(new \App\Notifications\AccountActivation($token));

        return redirect()->back()->with('success', 'Pelamar berhasil diterima. Karyawan baru telah didaftarkan dengan status Magang dan email aktivasi telah dikirim.');
    }
    
    public function reject(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        if ($employee->is_cv_approved) {
            return redirect()->back()->with('error', 'Pelamar ini sudah disetujui sebelumnya.');
        }

        $email = $employee->email;
        $name = $employee->nama_lengkap;
        
        // Hapus file CV jika ada
        if ($employee->cv_file && \Illuminate\Support\Facades\Storage::disk('public')->exists($employee->cv_file)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($employee->cv_file);
        }

        // Hapus data pelamar
        $employee->delete();

        // Kirim email penolakan
        try {
            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\CVRejectedMail($name));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send CV rejection email: ' . $e->getMessage());
            return redirect()->back()->with('success', 'Pelamar berhasil ditolak, namun gagal mengirim email notifikasi.');
        }

        return redirect()->back()->with('success', 'Pelamar berhasil ditolak dan email pemberitahuan telah dikirim.');
    }
}

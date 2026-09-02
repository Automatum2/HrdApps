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

        // Generate 6-digit OTP aktivasi akun
        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Update employee (pertahankan status_kerja dari form lamaran jika ada, default 'tetap')
        $statusKerja = in_array($employee->status_kerja, ['tetap', 'kontrak', 'harian', 'tenaga_lepas'])
            ? $employee->status_kerja
            : 'tetap';

        $employee->update([
            'nik' => $nik,
            'is_cv_approved' => true,
            'status_kerja' => $statusKerja,
            'status' => 'aktif',
            'activation_otp' => Hash::make($otp),
            'activation_otp_expires_at' => now()->addHours(24),
        ]);

        // Create User account
        $user = User::create([
            'username' => strtolower(str_replace(' ', '', $employee->nama_lengkap)) . rand(10,99),
            'email' => $employee->email,
            'password' => Hash::make(Str::random(24)), // Random temporary password
            'role' => 'karyawan',
            'employee_id' => $employee->id
        ]);

        // Send activation link with OTP
        $token = \Illuminate\Support\Facades\Password::broker()->createToken($user);
        $user->notify(new \App\Notifications\AccountActivation($token, $otp));

        $statusLabel = [
            'tetap' => 'Tetap',
            'kontrak' => 'Kontrak',
            'harian' => 'Harian',
            'tenaga_lepas' => 'Tenaga Lepas',
        ][$statusKerja] ?? ucfirst($statusKerja);

        return redirect()->back()->with('success', 'Pelamar ' . $employee->nama_lengkap . ' berhasil disetujui (Status: ' . $statusLabel . '). Email aktivasi beserta Kode OTP telah dikirim ke ' . $employee->email . '.');
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

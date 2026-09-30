<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CVController extends Controller
{
    private function authorizeCvAccess()
    {
        $role = session('user_role');
        if (!in_array($role, ['hr_manager', 'hr_admin_manager', 'manager_departemen'])) {
            abort(403, 'Akses ditolak. Pengelolaan CV hanya untuk HR Manager.');
        }
    }

    public function index()
    {
        $this->authorizeCvAccess();

        // Tampilkan semua employee yang belum di-approve (pelamar) dengan pagination
        $applicants = Employee::where('is_cv_approved', false)->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        return view('backoffice.cv.index', compact('applicants'));
    }

    public function approve(Request $request, $id)
    {
        $this->authorizeCvAccess();
        $employee = Employee::find($id);

        if (!$employee) {
            return redirect()->route('backoffice.cv.index')->with('error', 'Data pelamar tidak ditemukan atau sudah diproses.');
        }

        // Jika sudah di-approve, abaikan
        if ($employee->is_cv_approved) {
            return redirect()->route('backoffice.cv.index')->with('error', 'Pelamar ini sudah disetujui sebelumnya.');
        }

        // Generate NIK random unik
        do {
            $nik = 'EMP-' . random_int(1000, 9999);
        } while (Employee::where('nik', $nik)->exists());

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

        // Generate username ringkas & mudah diingat (nama depan + 2 digit angka)
        $firstName = explode(' ', trim($employee->nama_lengkap))[0];
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName));
        if (strlen($baseUsername) < 3) {
            $parts = explode(' ', trim($employee->nama_lengkap));
            $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', ($parts[0] ?? '') . ($parts[1] ?? '')));
        }
        $baseUsername = substr($baseUsername ?: 'user', 0, 8);
        $username = $baseUsername . rand(10, 99);
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . rand(100, 999);
        }

        // Create User account
        $user = User::create([
            'username' => $username,
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

        return redirect()->route('backoffice.cv.index')->with('success', 'Pelamar ' . $employee->nama_lengkap . ' berhasil disetujui (Status: ' . $statusLabel . '). Email aktivasi beserta Kode OTP telah dikirim ke ' . $employee->email . '.');
    }
    
    public function reject(Request $request, $id)
    {
        $this->authorizeCvAccess();
        $employee = Employee::find($id);

        if (!$employee) {
            return redirect()->route('backoffice.cv.index')->with('error', 'Data pelamar tidak ditemukan atau sudah dihapus.');
        }

        if ($employee->is_cv_approved) {
            return redirect()->route('backoffice.cv.index')->with('error', 'Pelamar ini sudah disetujui sebelumnya.');
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
            return redirect()->route('backoffice.cv.index')->with('success', 'Pelamar berhasil ditolak, namun gagal mengirim email notifikasi.');
        }

        return redirect()->route('backoffice.cv.index')->with('success', 'Pelamar berhasil ditolak dan email pemberitahuan telah dikirim.');
    }
}

<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    if (session()->has('user_role')) {
        return redirect()->route('backoffice.dashboard');
    }
    return view('auth.login');
})->name('login');

use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute untuk aktivasi / reset password dari link email
Route::get('/reset-password/{token}', function (string $token) {
    $email = request()->query('email');
    $user = \App\Models\User::where('email', $email)->first();
    $username = $user ? $user->username : 'Tidak ditemukan';
    
    return view('auth.reset-password', [
        'token' => $token, 
        'email' => $email,
        'username' => $username
    ]);
})->name('password.reset');
Route::post('/reset-password', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'password' => 'required|confirmed',
    ]);
    
    $user = \App\Models\User::where('email', $request->email)->first();
    if ($user) {
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();
    }

    return redirect()->route('login')->with('success', 'Password Anda berhasil diatur. Silakan login menggunakan username Anda.');
})->name('password.update');

Route::get('/backoffice/dashboard', function () {
    $role = session('user_role', 'manager');
    if ($role === 'super_admin') {
        $total_karyawan = \App\Models\Employee::count();
        $total_manager = \App\Models\User::where('role', 'hr_manager')->count();
        $latest_managers = \App\Models\User::where('role', 'hr_manager')->with('employee')->orderBy('id', 'desc')->take(5)->get();
        $total_departemen = \Illuminate\Support\Facades\DB::table('departments')->count();

        return view('backoffice.super_admin_dashboard', compact('total_karyawan', 'total_manager', 'latest_managers', 'total_departemen'));
    } elseif ($role === 'employee') {
        $employeeId = session('employee_id');
        $todayAttendance = \App\Models\Attendance::where('employee_id', $employeeId)->where('tanggal', \Carbon\Carbon::today()->toDateString())->first();
        $historyAttendances = \App\Models\Attendance::where('employee_id', $employeeId)->orderBy('tanggal', 'desc')->take(5)->get();
        $currentMonth = \Carbon\Carbon::now()->month;
        $hadirCount = \App\Models\Attendance::where('employee_id', $employeeId)->whereMonth('tanggal', $currentMonth)->where('status_kehadiran', 'hadir')->count();
        $izinCount = \App\Models\Attendance::where('employee_id', $employeeId)->whereMonth('tanggal', $currentMonth)->where('status_kehadiran', 'izin')->count();
        $sakitCount = \App\Models\Attendance::where('employee_id', $employeeId)->whereMonth('tanggal', $currentMonth)->where('status_kehadiran', 'sakit')->count();
        $alphaCount = \App\Models\Attendance::where('employee_id', $employeeId)->whereMonth('tanggal', $currentMonth)->where('status_kehadiran', 'alpha')->count();
        
        return view('backoffice.dashboard_karyawan', compact('todayAttendance', 'historyAttendances', 'hadirCount', 'izinCount', 'sakitCount', 'alphaCount'));
    }
    
    // Manager Dashboard (default fallback for 'manager')
    $total_karyawan = \App\Models\Employee::count();
    $hadir_hari_ini = \App\Models\Attendance::where('tanggal', \Carbon\Carbon::today()->toDateString())->where('status_kehadiran', 'hadir')->count();
    $belum_absen = max(0, $total_karyawan - $hadir_hari_ini);
    $latest_employees = \App\Models\Employee::with('department')->orderBy('created_at', 'desc')->take(5)->get();
    
    return view('backoffice.dashboard', compact('total_karyawan', 'hadir_hari_ini', 'belum_absen', 'latest_employees'));
})->name('backoffice.dashboard');


use App\Http\Controllers\HrManagerController;

Route::get('/backoffice/super-admin/kelola-hr', [HrManagerController::class, 'index'])->name('backoffice.super_admin.kelola_hr');
Route::post('/backoffice/super-admin/kelola-hr', [HrManagerController::class, 'store'])->name('backoffice.super_admin.kelola_hr.store');
Route::put('/backoffice/super-admin/kelola-hr/{id}', [HrManagerController::class, 'update'])->name('backoffice.super_admin.kelola_hr.update');
Route::delete('/backoffice/super-admin/kelola-hr/{id}', [HrManagerController::class, 'destroy'])->name('backoffice.super_admin.kelola_hr.destroy');

use App\Http\Controllers\EmployeeController;

Route::get('/backoffice/super-admin/kelola-karyawan', [EmployeeController::class, 'index'])->name('backoffice.super_admin.kelola_karyawan');
Route::post('/backoffice/super-admin/kelola-karyawan', [EmployeeController::class, 'store'])->name('backoffice.super_admin.kelola_karyawan.store');
Route::put('/backoffice/super-admin/kelola-karyawan/{id}', [EmployeeController::class, 'update'])->name('backoffice.super_admin.kelola_karyawan.update');
Route::delete('/backoffice/super-admin/kelola-karyawan/{id}', [EmployeeController::class, 'destroy'])->name('backoffice.super_admin.kelola_karyawan.destroy');
Route::get('/backoffice/super-admin/kelola-karyawan/{id}/detail', [EmployeeController::class, 'show'])->name('backoffice.super_admin.kelola_karyawan.show');

Route::get('/backoffice/karyawan', function () {
    // Proteksi Role: Hanya dapat diakses oleh Manager
    if (session('user_role', 'manager') !== 'manager') {
        return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak. Halaman Karyawan hanya dapat diakses oleh Manager.');
    }
    return view('backoffice.karyawan');
})->name('backoffice.karyawan');

Route::get('/backoffice/absensi', function (\Illuminate\Http\Request $request) {
    // Proteksi Role: Hanya dapat diakses oleh Manager
    if (session('user_role', 'manager') !== 'manager') {
        return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak. Halaman Absensi hanya dapat diakses oleh Manager.');
    }
    
    $query = \App\Models\Attendance::with('employee.department');
    
    // Filter tanggal
    $dari = $request->input('dari_tanggal', \Carbon\Carbon::now()->startOfMonth()->toDateString());
    $sampai = $request->input('sampai_tanggal', \Carbon\Carbon::now()->endOfMonth()->toDateString());
    $query->whereBetween('tanggal', [$dari, $sampai]);
    
    // Filter departemen
    if ($request->filled('departemen_id')) {
        $query->whereHas('employee', function($q) use ($request) {
            $q->where('department_id', $request->departemen_id);
        });
    }
    
    // Filter status
    if ($request->filled('status')) {
        $query->where('status_kehadiran', $request->status);
    }
    
    $attendances = $query->orderBy('tanggal', 'desc')->get();
    
    // Statistik berdasarkan tanggal yang dipilih
    $stats = [
        'hadir' => $attendances->where('status_kehadiran', 'hadir')->count(),
        'izin' => $attendances->where('status_kehadiran', 'izin')->count(),
        'sakit' => $attendances->where('status_kehadiran', 'sakit')->count(),
        'alpha' => $attendances->where('status_kehadiran', 'alpha')->count(),
        'cuti' => $attendances->where('status_kehadiran', 'cuti')->count(),
    ];
    
    $departments = \Illuminate\Support\Facades\DB::table('departments')->get();
    
    return view('backoffice.absensi', compact('attendances', 'stats', 'departments', 'dari', 'sampai'));
})->name('backoffice.absensi');

use App\Http\Controllers\PayrollController;
Route::get('/backoffice/penggajian', [PayrollController::class, 'index'])->name('backoffice.penggajian');

Route::get('/backoffice/laporan', function () {
    // Proteksi Role: Hanya dapat diakses oleh Manager
    if (session('user_role', 'manager') !== 'manager') {
        return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak. Halaman Laporan hanya dapat diakses oleh Manager.');
    }
    return view('backoffice.laporan');
})->name('backoffice.laporan');

Route::get('/backoffice/pengaturan', function () {
    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user) {
        $user->load('employee.position', 'employee.department', 'employee.documents');
    }
    return view('backoffice.pengaturan', compact('user'));
})->name('backoffice.pengaturan');

Route::post('/backoffice/pengaturan/upload-document', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Log::info('Upload document route hit. Files:', $_FILES);
    \Illuminate\Support\Facades\Log::info('Request All:', $request->all());
    
    $request->validate([
        'document' => 'required|array',
        'document.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
    ], [
        'document.required' => 'Pilih setidaknya satu file dokumen terlebih dahulu.',
        'document.*.required' => 'Ada file dokumen yang kosong atau gagal diunggah.',
        'document.*.file' => 'File yang diunggah tidak valid.',
        'document.*.mimes' => 'Format file tidak didukung. Harap gunakan format PDF, JPG, atau PNG.',
        'document.*.max' => 'Ukuran salah satu file melebihi 5 MB.',
    ]);

    $user = \Illuminate\Support\Facades\Auth::user();
    if (!$user || !$user->employee) {
        return redirect()->back()->with('error', 'Akses ditolak, akun Anda tidak tertaut dengan data Karyawan.');
    }

    if ($request->hasFile('document')) {
        $files = $request->file('document');
        $count = 0;
        
        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $sizeKb = round($file->getSize() / 1024);
            $extension = $file->getClientOriginalExtension();
            
            $path = $file->store('documents', 'public');
            
            \App\Models\EmployeeDocument::create([
                'employee_id' => $user->employee->id,
                'file_name' => $originalName,
                'file_path' => $path,
                'file_size' => $sizeKb,
                'file_type' => strtolower($extension),
            ]);
            $count++;
        }
        
        return redirect()->back()->with('success', "$count Dokumen berhasil diunggah.");
    }

    return redirect()->back()->with('error', 'Gagal mengunggah dokumen.');
})->name('backoffice.pengaturan.upload.document');

Route::delete('/backoffice/pengaturan/delete-document/{id}', function ($id) {
    $user = \Illuminate\Support\Facades\Auth::user();
    if (!$user || !$user->employee) {
        return redirect()->back()->with('error', 'Akses ditolak.');
    }

    $document = \App\Models\EmployeeDocument::where('id', $id)
        ->where('employee_id', $user->employee->id)
        ->first();

    if ($document) {
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($document->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();
        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }

    return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
})->name('backoffice.pengaturan.delete.document');

Route::post('/backoffice/pengaturan/upload-photo', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
    ], [
        'photo.required' => 'Pilih foto profil terlebih dahulu sebelum mengunggah.',
        'photo.image' => 'File yang diunggah harus berupa gambar.',
        'photo.mimes' => 'Format foto tidak didukung. Harap gunakan format JPG atau PNG.',
        'photo.max' => 'Ukuran foto tidak boleh lebih dari 5 MB.',
    ]);

    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('photos', 'public');
        
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->employee) {
            $user->employee->foto = $path;
            $user->employee->save();
        }
        
        session(['user_photo' => asset('storage/' . $path)]);
        return redirect()->back()->with('success', 'Foto profil berhasil diunggah.');
    }

    return redirect()->back()->with('error', 'Gagal mengunggah foto profil.');
})->name('backoffice.pengaturan.upload.photo');


Route::post('/backoffice/pengaturan/delete-document/{index}', function ($index) {
    $documents = session('user_documents', []);
    if (isset($documents[$index])) {
        // Optionally, delete file from storage if needed. For now just removing from session.
        unset($documents[$index]);
        session(['user_documents' => array_values($documents)]);
        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }
    return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
})->name('backoffice.pengaturan.delete.document');

use App\Http\Controllers\AttendanceController;
Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock_in');
Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock_out');
Route::post('/attendance/leave', [AttendanceController::class, 'submitLeave'])->name('attendance.leave');

// Temporary route to reset attendance for testing
Route::get('/reset-absen', function () {
    \App\Models\Attendance::where('tanggal', \Carbon\Carbon::today()->toDateString())->delete();
    return 'Berhasil reset data absensi hari ini!';
});

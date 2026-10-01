<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HrManagerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AllowanceController;
use App\Http\Controllers\DeductionController;
use App\Http\Controllers\PositionController;

// --- PUBLIC ROUTES ---
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!\Illuminate\Support\Facades\File::exists($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath);
})->where('path', '.*')->middleware('auth');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('backoffice.dashboard');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Rute untuk Karir (Submit CV)
Route::get('/karir', function () {
    return view('karir');
})->name('karir.index');

Route::get('/karir/submit', function () {
    return redirect()->route('karir.index');
});

Route::post('/karir/submit', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'nama' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:employees,email',
        'status_kerja' => 'required|in:tetap,kontrak,harian,tenaga_lepas',
        'cv_text' => 'nullable|string',
        'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        'cv_url' => 'nullable|url|max:255',
    ], [
        'email.email' => 'Format email tidak valid. Pastikan email Anda aktif.',
        'status_kerja.required' => 'Pilih jenis status kerja yang dilamar.',
        'cv_file.mimes' => 'File CV harus berformat PDF, DOC, atau DOCX.',
        'cv_file.max' => 'Ukuran file CV maksimal 5MB.',
        'cv_url.url' => 'Format URL tidak valid (contoh: https://linkedin.com/in/username).',
    ]);

    $filePath = null;
    if ($request->hasFile('cv_file')) {
        $filePath = $request->file('cv_file')->store('cv_files', 'public');
    }

    do {
        $tempNik = 'APP-' . random_int(1000, 9999);
    } while (\App\Models\Employee::where('nik', $tempNik)->exists());

    \App\Models\Employee::create([
        'nik' => $tempNik, // Temporary NIK unik untuk pelamar
        'nama_lengkap' => $request->nama,
        'email' => $request->email,
        'cv_text' => $request->cv_text,
        'cv_file' => $filePath,
        'cv_url' => $request->cv_url,
        'status_kerja' => $request->status_kerja,
        'is_cv_approved' => false,
        'status' => 'nonaktif',
        'gaji_pokok' => 0
    ]);

    return redirect()->back()->with('success', 'Lamaran Anda berhasil dikirim! Tim HRD kami akan meninjau CV Anda. Pastikan email Anda aktif untuk menerima link aktivasi akun & OTP jika diterima.');
})->name('karir.submit');

// Rute untuk Lupa Password
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email|exists:users,email'
    ], [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.exists' => 'Email tidak terdaftar di sistem kami.'
    ]);

    // Generate 6-digit OTP
    $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

    \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $request->email],
        ['token' => \Illuminate\Support\Facades\Hash::make($otp), 'created_at' => now()]
    );

    try {
        \Illuminate\Support\Facades\Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Kode OTP Reset Password HRDApps');
        });
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Gagal mengirim email reset password: ' . $e->getMessage());
    }

    return redirect()->route('password.verify-otp', ['email' => $request->email])
                     ->with('success', 'Kode OTP telah dikirim ke email Anda. Silakan cek kotak masuk atau folder spam.');
})->name('password.email');

// Rute untuk Halaman Verifikasi OTP
Route::get('/verify-otp', function (\Illuminate\Http\Request $request) {
    $email = $request->query('email');
    if (!$email) {
        return redirect()->route('password.request')->withErrors(['email' => 'Silakan masukkan email terlebih dahulu.']);
    }
    return view('auth.verify-otp', compact('email'));
})->name('password.verify-otp');

Route::post('/verify-otp', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|string|size:6'
    ], [
        'otp.required' => 'Kode OTP wajib diisi.',
        'otp.size' => 'Kode OTP harus berupa 6 angka.'
    ]);

    $dbToken = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();

    if (!$dbToken || !\Illuminate\Support\Facades\Hash::check($request->otp, $dbToken->token)) {
        return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluarsa.']);
    }

    // Jika OTP benar, arahkan ke form reset password dengan memberikan OTP sebagai token
    return redirect()->route('password.reset', ['token' => $request->otp, 'email' => $request->email]);
})->name('password.verify-otp.post');

// Rute untuk aktivasi / reset password dari link email
Route::get('/reset-password/{token}', function (string $token) {
    $email = request()->query('email');
    $type = request()->query('type', 'reset');
    $user = \App\Models\User::where('email', $email)->first();
    $username = $user ? $user->username : 'Tidak ditemukan';
    $namaLengkap = $user && $user->employee ? $user->employee->nama_lengkap : request()->query('name', '');
    
    return view('auth.reset-password', [
        'token' => $token, 
        'email' => $email,
        'username' => $username,
        'namaLengkap' => $namaLengkap,
        'type' => $type
    ]);
})->name('password.reset');

Route::post('/reset-password', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'token' => 'required|string',
        'password' => 'required|confirmed',
        'type' => 'nullable|string',
        'otp' => 'nullable|string',
    ]);
    
    $user = \App\Models\User::where('email', $request->email)->first();

    // Jika flow aktivasi akun pelamar yang memiliki OTP
    if ($request->type === 'activation') {
        if ($user && $user->employee && $user->employee->activation_otp) {
            if (!$request->filled('otp')) {
                return redirect()->back()->withErrors(['otp' => 'Kode OTP wajib diisi untuk aktivasi akun baru.']);
            }
            if ($user->employee->activation_otp_expires_at && now()->greaterThan($user->employee->activation_otp_expires_at)) {
                return redirect()->back()->withErrors(['otp' => 'Kode OTP aktivasi telah kadaluarsa. Silakan hubungi HRD.']);
            }
            if (!\Illuminate\Support\Facades\Hash::check($request->otp, $user->employee->activation_otp)) {
                return redirect()->back()->withErrors(['otp' => 'Kode OTP aktivasi tidak sesuai.']);
            }
            // Bersihkan OTP setelah dipakai
            $user->employee->update([
                'activation_otp' => null,
                'activation_otp_expires_at' => null,
            ]);
        }
    }

    // Validasi token password broker yang dikirim dari form
    $dbToken = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();
        
    if (!$dbToken || !\Illuminate\Support\Facades\Hash::check($request->token, $dbToken->token)) {
        return redirect()->back()->withErrors(['email' => 'Token tautan aktivasi/reset tidak valid atau sudah kadaluarsa.']);
    }
    
    if ($user) {
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();
        
        // Bersihkan token dan sisa activation_otp jika ada
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        if ($user->employee && $user->employee->activation_otp) {
            $user->employee->update([
                'activation_otp' => null,
                'activation_otp_expires_at' => null,
            ]);
        }
    }

    $msg = $request->type === 'activation'
        ? 'Akun Anda berhasil diaktifkan dan password telah dibuat. Silakan login menggunakan username Anda: ' . ($user ? $user->username : '')
        : 'Password Anda berhasil diatur. Silakan login menggunakan username Anda.';

    return redirect()->route('login')->with('success', $msg);
})->name('password.update');


// --- AUTHENTICATED ROUTES ---
Route::middleware(['auth'])->group(function () {

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // -- DASHBOARD --
    Route::get('/backoffice/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('backoffice.dashboard');

    // -- NOTIFICATIONS (All Roles) --
    Route::post('/backoffice/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('backoffice.notifications.read');
    Route::post('/backoffice/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('backoffice.notifications.read_all');
    Route::delete('/backoffice/notifications/{id}', [NotificationController::class, 'destroy'])->name('backoffice.notifications.destroy');
    Route::delete('/backoffice/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('backoffice.notifications.clear_all');

    // -- LEAVES (Approval workflow) --
    Route::get('/backoffice/leaves', [App\Http\Controllers\LeaveController::class, 'index'])->name('backoffice.leaves.index');
    Route::post('/backoffice/leaves/{leave}/approve', [App\Http\Controllers\LeaveController::class, 'approve'])->name('backoffice.leaves.approve');
    Route::post('/backoffice/leaves/{leave}/reject', [App\Http\Controllers\LeaveController::class, 'reject'])->name('backoffice.leaves.reject');

    // -- PAYROLL (General, Employee can view index & download their PDF) --
    Route::get('/backoffice/penggajian', [PayrollController::class, 'index'])->name('backoffice.penggajian');
    Route::get('/backoffice/penggajian/download-pdf/{id}', [PayrollController::class, 'downloadPdf'])->name('backoffice.penggajian.download.pdf');

    // -- PENGATURAN (All Roles) --
    Route::get('/backoffice/pengaturan', [\App\Http\Controllers\SettingsController::class, 'index'])->name('backoffice.pengaturan');
    Route::post('/backoffice/pengaturan/cv', [\App\Http\Controllers\SettingsController::class, 'updateCv'])->name('backoffice.pengaturan.cv');
    Route::post('/backoffice/pengaturan/bank', [\App\Http\Controllers\SettingsController::class, 'updateBank'])->name('backoffice.pengaturan.bank');
    Route::post('/backoffice/pengaturan/notifikasi', [\App\Http\Controllers\SettingsController::class, 'updateNotification'])->name('backoffice.pengaturan.notifikasi');
    Route::post('/backoffice/pengaturan/password', [\App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('backoffice.pengaturan.password');
    Route::post('/backoffice/pengaturan/upload-document', [\App\Http\Controllers\SettingsController::class, 'uploadDocument'])->name('backoffice.pengaturan.upload.document');
    Route::delete('/backoffice/pengaturan/delete-document/{id}', [\App\Http\Controllers\SettingsController::class, 'deleteDocument'])->name('backoffice.pengaturan.delete.document');
    Route::post('/backoffice/pengaturan/upload-photo', [\App\Http\Controllers\SettingsController::class, 'uploadPhoto'])->name('backoffice.pengaturan.upload.photo');
    Route::post('/backoffice/pengaturan/delete-document/{index}', [\App\Http\Controllers\SettingsController::class, 'deleteDocumentSession'])->name('backoffice.pengaturan.delete.document_session');


    // -- EMPLOYEE ROUTES --
    Route::middleware(['employee.session'])->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock_in');
        Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock_out');
        Route::post('/attendance/leave', [AttendanceController::class, 'submitLeave'])->name('attendance.leave');
    });

    // -- CV / REKRUTMEN (HR Manager & Manager Departemen, TIDAK untuk Super Admin) --
    Route::middleware(['role:hr_manager,manager_departemen'])->group(function () {
        Route::get("/backoffice/cv", [App\Http\Controllers\CVController::class, "index"])->name("backoffice.cv.index");
        Route::post("/backoffice/cv/{id}/approve", [App\Http\Controllers\CVController::class, "approve"])->name("backoffice.cv.approve");
        Route::post("/backoffice/cv/{id}/reject", [App\Http\Controllers\CVController::class, "reject"])->name("backoffice.cv.reject");
        Route::get("/backoffice/cv/{id}/approve", function() { return redirect()->route('backoffice.cv.index'); });
        Route::get("/backoffice/cv/{id}/reject", function() { return redirect()->route('backoffice.cv.index'); });
    });

    // -- MANAGER & SUPER ADMIN ROUTES --
    Route::middleware(['role:hr_manager,manager_departemen,super_admin'])->group(function () {
        
        // Karyawan
        Route::get("/backoffice/training", [App\Http\Controllers\TrainingController::class, "index"])->name("backoffice.training.index");
        Route::post("/backoffice/training/store", [App\Http\Controllers\TrainingController::class, "store"])->name("backoffice.training.store");
        Route::delete("/backoffice/training/{id}", [App\Http\Controllers\TrainingController::class, "destroy"])->name("backoffice.training.destroy");

        Route::get('/backoffice/karyawan', [\App\Http\Controllers\EmployeeController::class, 'manage'])->name('backoffice.karyawan');
        Route::get('/backoffice/karyawan/export', [\App\Http\Controllers\EmployeeController::class, 'export'])->name('backoffice.karyawan.export');
        Route::get('/backoffice/karyawan/{id}/detail', [\App\Http\Controllers\EmployeeController::class, 'show'])->name('backoffice.karyawan.show');
        Route::post('/backoffice/karyawan/{id}/gaji', [\App\Http\Controllers\EmployeeController::class, 'updateGaji'])->name('backoffice.karyawan.update_gaji');
        Route::post('/backoffice/karyawan/lepas', [\App\Http\Controllers\EmployeeController::class, 'lepasDepartemen'])->name('backoffice.karyawan.lepas');
        Route::post('/backoffice/karyawan/assign', [\App\Http\Controllers\EmployeeController::class, 'assignDepartemen'])->name('backoffice.karyawan.assign');
        Route::delete('/backoffice/karyawan/{id}/force-delete', [\App\Http\Controllers\EmployeeController::class, 'forceDelete'])->name('backoffice.karyawan.force_delete');

        // Tunjangan & Potongan
        Route::post('/backoffice/karyawan/{id}/allowances', [AllowanceController::class, 'store'])->name('backoffice.allowances.store');
        Route::put('/backoffice/allowances/{id}', [AllowanceController::class, 'update'])->name('backoffice.allowances.update');
        Route::delete('/backoffice/allowances/{id}', [AllowanceController::class, 'destroy'])->name('backoffice.allowances.destroy');

        Route::post('/backoffice/karyawan/{id}/deductions', [DeductionController::class, 'store'])->name('backoffice.deductions.store');
        Route::put('/backoffice/deductions/{id}', [DeductionController::class, 'update'])->name('backoffice.deductions.update');
        Route::delete('/backoffice/deductions/{id}', [DeductionController::class, 'destroy'])->name('backoffice.deductions.destroy');
        // Absensi
        Route::get('/backoffice/absensi', function (\Illuminate\Http\Request $request) {
            $query = \App\Models\Attendance::with('employee.department');
            
            $user = \Illuminate\Support\Facades\Auth::user();
            $role = session('user_role');
            
            if ($role === 'manager_departemen' && $user && $user->employee) {
                $department_id = $user->employee->department_id;
                $query->whereHas('employee', function($q) use ($department_id) {
                    $q->where('department_id', $department_id);
                });
            }
            
            $dari = $request->input('dari_tanggal', \Carbon\Carbon::now()->startOfMonth()->toDateString());
            $sampai = $request->input('sampai_tanggal', \Carbon\Carbon::now()->endOfMonth()->toDateString());
            $query->whereBetween('tanggal', [$dari, $sampai]);
            
            if ($request->filled('departemen_id')) {
                $query->whereHas('employee', function($q) use ($request) {
                    $q->where('department_id', $request->departemen_id);
                });
            }
            
            if ($request->filled('status')) {
                $query->where('status_kehadiran', $request->status);
            }
            
            // Hitung statistik untuk seluruh filter (sebelum dipaginate)
            $allFiltered = (clone $query)->get();
            $stats = [
                'hadir' => $allFiltered->where('status_kehadiran', 'hadir')->count(),
                'izin' => $allFiltered->where('status_kehadiran', 'izin')->count(),
                'sakit' => $allFiltered->where('status_kehadiran', 'sakit')->count(),
                'alpha' => $allFiltered->where('status_kehadiran', 'alpha')->count(),
                'cuti' => $allFiltered->where('status_kehadiran', 'cuti')->count(),
            ];
            
            // Pagination dinamis 10 data per halaman
            $attendances = $query->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();
            
            if (isset($department_id)) {
                $departments = \Illuminate\Support\Facades\DB::table('departments')->where('id', $department_id)->get();
            } else {
                $departments = \Illuminate\Support\Facades\DB::table('departments')->get();
            }
            
            return view('backoffice.absensi', compact('attendances', 'stats', 'departments', 'dari', 'sampai'));
        })->name('backoffice.absensi');

        Route::put('/backoffice/absensi/{id}', function (\Illuminate\Http\Request $request, $id) {
            $request->validate([
                'jam_masuk' => 'nullable|date_format:H:i',
                'jam_keluar' => 'nullable|date_format:H:i',
                'status_kehadiran' => 'required|in:hadir,izin,sakit,cuti,alpha',
            ]);
            
            $attendance = \App\Models\Attendance::findOrFail($id);
            
            $total_jam_kerja = $attendance->total_jam_kerja;
            if ($request->filled('jam_masuk') && $request->filled('jam_keluar')) {
                $masuk = \Carbon\Carbon::parse($request->jam_masuk);
                $keluar = \Carbon\Carbon::parse($request->jam_keluar);
                $total_jam_kerja = round($masuk->diffInMinutes($keluar) / 60, 2);
            } elseif (!$request->filled('jam_masuk') || !$request->filled('jam_keluar')) {
                $total_jam_kerja = null;
            }
            
            $attendance->update([
                'jam_masuk' => $request->jam_masuk,
                'jam_keluar' => $request->jam_keluar,
                'status_kehadiran' => $request->status_kehadiran,
                'total_jam_kerja' => $total_jam_kerja,
            ]);
            
            return redirect()->back()->with('success', 'Data absensi berhasil diperbarui.');
        })->name('backoffice.absensi.update');

        Route::get('/backoffice/absensi/export', function (\Illuminate\Http\Request $request) {
            $query = \App\Models\Attendance::with('employee.department');
            
            $dari = $request->input('dari_tanggal', \Carbon\Carbon::now()->startOfMonth()->toDateString());
            $sampai = $request->input('sampai_tanggal', \Carbon\Carbon::now()->endOfMonth()->toDateString());
            $query->whereBetween('tanggal', [$dari, $sampai]);
            
            if ($request->filled('departemen_id')) {
                $query->whereHas('employee', function($q) use ($request) {
                    $q->where('department_id', $request->departemen_id);
                });
            }
            
            if ($request->filled('status')) {
                $query->where('status_kehadiran', $request->status);
            }
            
            $attendances = $query->orderBy('tanggal', 'desc')->get();
            
            $csvFileName = 'Laporan_Absensi_' . $dari . '_sd_' . $sampai . '.csv';
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$csvFileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];
            
            $columns = ['No', 'Nama Karyawan', 'NIK', 'Departemen', 'Tanggal', 'Jam Masuk', 'Jam Keluar', 'Status Kehadiran', 'Total Jam Kerja'];
            
            $callback = function() use($attendances, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                
                $rowNumber = 1;
                foreach ($attendances as $att) {
                    fputcsv($file, [
                        $rowNumber++,
                        $att->employee->nama_lengkap ?? '-',
                        $att->employee->nik ?? '-',
                        $att->employee->department->nama_department ?? 'Umum',
                        $att->tanggal,
                        $att->jam_masuk ?? '--:--',
                        $att->jam_keluar ?? '--:--',
                        ucfirst($att->status_kehadiran),
                        $att->total_jam_kerja ?? '--'
                    ]);
                }
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        })->name('backoffice.absensi.export');

        // Penggajian Actions
        Route::post('/backoffice/penggajian/periode', [PayrollController::class, 'storePeriod'])->name('backoffice.penggajian.store_period');
        Route::get('/backoffice/penggajian/periode/{id}', [PayrollController::class, 'show'])->name('backoffice.penggajian.show');
        Route::post('/backoffice/penggajian/periode/{id}/generate', [PayrollController::class, 'generate'])->name('backoffice.penggajian.generate');
        Route::post('/backoffice/penggajian/approve/{payroll_id}', [PayrollController::class, 'approve'])->name('backoffice.penggajian.approve');
        Route::post('/backoffice/penggajian/periode/{id}/approve-all', [PayrollController::class, 'approveAll'])->name('backoffice.penggajian.approve_all');
        Route::get('/backoffice/penggajian/periode/{id}/download-mass', [PayrollController::class, 'downloadMassPdf'])->name('backoffice.penggajian.download_mass');

        // Laporan
        Route::get('/backoffice/laporan', [ReportController::class, 'index'])->name('backoffice.laporan');
        Route::post('/backoffice/laporan/generate', [ReportController::class, 'generate'])->name('backoffice.laporan.generate');
        Route::get('/backoffice/laporan/download/{id}', [ReportController::class, 'download'])->name('backoffice.laporan.download');
        Route::get('/backoffice/laporan/kinerja', [ReportController::class, 'kinerja'])->name('backoffice.laporan.kinerja');
    });

    // 6. SUPER ADMIN & HR MANAGER ROUTES
    Route::middleware(['role:super_admin,hr_manager'])->group(function () {
        Route::get('/backoffice/super-admin/kelola-hr', [HrManagerController::class, 'index'])->name('backoffice.super_admin.kelola_hr');
        Route::post('/backoffice/super-admin/kelola-hr', [HrManagerController::class, 'store'])->name('backoffice.super_admin.kelola_hr.store');
        Route::put('/backoffice/super-admin/kelola-hr/{id}', [HrManagerController::class, 'update'])->name('backoffice.super_admin.kelola_hr.update');
        Route::delete('/backoffice/super-admin/kelola-hr/{id}', [HrManagerController::class, 'destroy'])->name('backoffice.super_admin.kelola_hr.destroy');
    });

    // 7. SUPER ADMIN ONLY ROUTES
    Route::middleware(['role:super_admin'])->group(function () {
        // Monitoring Absensi Global
        Route::get('/backoffice/super-admin/absensi', [\App\Http\Controllers\SuperAdminAttendanceController::class, 'index'])->name('backoffice.super_admin.absensi');

        // Departemen
        Route::get('/backoffice/departemen', [\App\Http\Controllers\DepartmentController::class, 'index'])->name('backoffice.departemen.index');
        Route::post('/backoffice/departemen', [\App\Http\Controllers\DepartmentController::class, 'store'])->name('backoffice.departemen.store');
        Route::put('/backoffice/departemen/{id}', [\App\Http\Controllers\DepartmentController::class, 'update'])->name('backoffice.departemen.update');
        Route::delete('/backoffice/departemen/{id}', [\App\Http\Controllers\DepartmentController::class, 'destroy'])->name('backoffice.departemen.destroy');

        // Jabatan (Positions)
        Route::get('/backoffice/posisi', [PositionController::class, 'index'])->name('backoffice.posisi.index');
        Route::post('/backoffice/posisi', [PositionController::class, 'store'])->name('backoffice.posisi.store');
        Route::put('/backoffice/posisi/{id}', [PositionController::class, 'update'])->name('backoffice.posisi.update');
        Route::delete('/backoffice/posisi/{id}', [PositionController::class, 'destroy'])->name('backoffice.posisi.destroy');

        Route::get('/backoffice/super-admin/kelola-karyawan', [EmployeeController::class, 'index'])->name('backoffice.super_admin.kelola_karyawan');
        Route::put('/backoffice/super-admin/kelola-karyawan/{id}', [EmployeeController::class, 'update'])->name('backoffice.super_admin.kelola_karyawan.update');
        Route::delete('/backoffice/super-admin/kelola-karyawan/{id}', [EmployeeController::class, 'destroy'])->name('backoffice.super_admin.kelola_karyawan.destroy');
        Route::delete('/backoffice/super-admin/kelola-karyawan/{id}/force-delete', [EmployeeController::class, 'forceDelete'])->name('backoffice.super_admin.kelola_karyawan.force_delete');
        Route::get('/backoffice/super-admin/kelola-karyawan/{id}/detail', [EmployeeController::class, 'show'])->name('backoffice.super_admin.kelola_karyawan.show'); // duplicate fallback
    });
});

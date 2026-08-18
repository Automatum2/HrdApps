# Revisi Implementasi HRDApps (Berdasarkan Feedback Presentasi)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Mengimplementasikan perbaikan fitur absensi, sistem hierarki (Superadmin -> HR Manager -> Manager Departemen -> Karyawan), sistem persetujuan cuti dua lapis, dan kontrol akses spesifik sesuai daftar revisi yang telah ditetapkan tanpa mengubah maknanya.

**Architecture:** 
- Penambahan role dan relasi hierarki di database (roles: `superadmin`, `hr_training_manager`, `hr_admin_manager`, `manager_departemen`, `karyawan`).
- Sistem *2-step approval* pada tabel cuti (`acc_manager_dept_at`, `acc_hr_at`).
- Geolocation radius (Haversine formula) untuk absensi WFD.
- Laravel Authorization (Policies & Gates) untuk isolasi data tiap level hierarki.

**Tech Stack:** Laravel 11, Blade, MySQL, TailwindCSS.

## Global Constraints
- Bahasa Indonesia wajib digunakan pada seluruh antarmuka dan notifikasi.
- Tidak mengubah arti dari 12 daftar revisi yang telah diberikan pengguna.
- Setiap implementasi harus dapat dites secara fungsional.

---

### Task 1: Pembaruan Skema Database & Hierarki Role

**Files:**
- Create: `database/migrations/xxxx_xx_xx_add_roles_and_approval_columns.php`
- Modify: `app/Models/User.php`
- Modify: `app/Models/Employee.php`

**Interfaces:**
- Produces: Kolom `role` pada `users` mendukung (`superadmin`, `hr_training_manager`, `hr_admin_manager`, `manager_departemen`, `karyawan`). Kolom `manager_id` pada `departments`. Kolom `status_kerja` pada `employees` mendukung (`harian`, `musiman`, `tidak tetap`).

- [ ] **Step 1: Write the failing test**
```php
public function test_user_has_specific_hierarchy_roles()
{
    $user = User::factory()->create(['role' => 'hr_training_manager']);
    $this->assertEquals('hr_training_manager', $user->role);
}
```

- [ ] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter test_user_has_specific_hierarchy_roles`
Expected: FAIL 

- [ ] **Step 3: Write minimal implementation**
Buat *migration* untuk mengubah ENUM `role` pada `users` menjadi: `superadmin`, `hr_manager` (yang nanti logicnya dipecah menjadi `hr_training_manager` dan `hr_admin_manager`), `manager_departemen`, `karyawan`. Tambahkan ENUM `status_kerja` pada tabel `employees` menjadi Harian, Musiman, Tidak Tetap. Tambahkan relasi manager di Model Department.

- [ ] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter test_user_has_specific_hierarchy_roles`
Expected: PASS

- [ ] **Step 5: Commit**
```bash
git add database/migrations/ app/Models/
git commit -m "feat: add explicit hierarchy roles and work statuses"
```

---

### Task 2: Hak Akses Pembuatan Slip Gaji & Tunjangan

**Files:**
- Modify: `app/Policies/PayrollPolicy.php`
- Modify: `app/Policies/AllowancePolicy.php`
- Modify: `app/Providers/AuthServiceProvider.php`

**Interfaces:**
- Consumes: Role user dari Task 1.
- Produces: Authorization logic (Superadmin membuat slip gaji Manager, penambahan tunjangan sesuai hierarki).

- [ ] **Step 1: Write the failing test**
```php
public function test_only_superadmin_can_generate_manager_payslip()
{
    $superadmin = User::factory()->create(['role' => 'superadmin']);
    $hr_manager = User::factory()->create(['role' => 'hr_admin_manager']);
    $managerTarget = Employee::factory()->create(['role' => 'manager_departemen']);
    
    $this->assertTrue($superadmin->can('createPayslip', $managerTarget));
    $this->assertFalse($hr_manager->can('createPayslip', $managerTarget));
}
```

- [ ] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter test_only_superadmin_can_generate_manager_payslip`
Expected: FAIL 

- [ ] **Step 3: Write minimal implementation**
Definisikan Gate/Policy `createPayslip`. Izinkan `superadmin` mencetak untuk `manager_departemen` dan `hr_manager`. Definisikan Gate `addAllowance` sesuai hierarki (hanya role yang lebih tinggi yang bisa menambah tunjangan ke bawahannya).

- [ ] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter test_only_superadmin_can_generate_manager_payslip`
Expected: PASS

- [ ] **Step 5: Commit**
```bash
git add app/Policies/ app/Providers/AuthServiceProvider.php
git commit -m "feat: implement hierarchy based authorization for payslip and allowances"
```

---

### Task 3: Pemisahan Akses HR & Visibilitas Sidebar Training

**Files:**
- Modify: `app/Http/Controllers/TrainingController.php` (buat jika belum ada)
- Modify: `app/Http/Controllers/CVController.php` (buat jika belum ada)
- Modify: `resources/views/layouts/admin.blade.php` (atau file sidebar navigasi)

**Interfaces:**
- Consumes: User Role (`hr_training_manager`, `hr_admin_manager`).
- Produces: Sidebar `Training / Trainer` HANYA muncul untuk `hr_training_manager`. Sidebar pengelolaan CV HANYA muncul untuk `hr_admin_manager`. Pembagian wewenang yang tegas di dalam divisi HR.

- [ ] **Step 1: Write the failing test**
```php
public function test_hr_admin_can_approve_cv()
{
    $adminHR = User::factory()->create(['role' => 'hr_admin_manager']);
    $this->assertTrue($adminHR->can('approveCV', Employee::class));
}
```

- [ ] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter test_hr_admin_can_approve_cv`

- [ ] **Step 3: Write minimal implementation**
Tambahkan kolom `is_cv_approved` di tabel employees. Buat Gate `manageTraining` khusus untuk `hr_training_manager`. Buat Gate `approveCV` khusus untuk `hr_admin_manager`. Modifikasi file layout sidebar Blade (misal `admin.blade.php`) agar menu "Trainer / Pelatihan" **hanya muncul** jika role adalah `hr_training_manager`, dan menu "Kelola CV" **hanya muncul** jika role adalah `hr_admin_manager`.

- [ ] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter test_hr_admin_can_approve_cv`

- [ ] **Step 5: Commit**
```bash
git add app/Http/Controllers/ app/Providers/
git commit -m "feat: split hr manager duties into training and admin cv"
```

---

### Task 4: Visibilitas Laporan & Container Dashboard

**Files:**
- Modify: `app/Http/Controllers/DashboardController.php`
- Modify: `app/Http/Controllers/ReportController.php`
- Modify: `resources/views/backoffice/dashboard.blade.php`

**Interfaces:**
- Consumes: Relasi Manager dan Hierarki Departemen.
- Produces: Data widget spesifik per role. HR Manager melihat semua, Dept Manager melihat departemennya saja. Statistik dilihat Superadmin/Supervisor.

- [ ] **Step 1: Write the failing test**
```php
public function test_dashboard_stats_visibility()
{
    // Test that HR sees global total, Dept Manager sees dept total
}
```

- [ ] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter test_dashboard_stats_visibility`

- [ ] **Step 3: Write minimal implementation**
Di `DashboardController`, hitung `$total_karyawan` dan `$total_departemen`. Jika user = `hr_manager`, kirim semua variabel. Jika user = `manager_departemen`, kirim `$total_karyawan_departemen` saja (berdasarkan `$user->employee->department_id`) dan hapus total global dari view-nya menggunakan kondisi `@if(Auth::user()->role === 'hr_manager')`. Pastikan Statistik Absensi bisa diakses `superadmin`.

- [ ] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter test_dashboard_stats_visibility`

- [ ] **Step 5: Commit**
```bash
git add app/Http/Controllers/ resources/views/backoffice/
git commit -m "feat: adjust dashboard containers and report visibility based on roles"
```

---

### Task 5: 2-Step Approval Cuti (Leave Requests)

**Files:**
- Modify: `database/migrations/xxxx_xx_xx_add_approval_to_leaves.php`
- Modify: `app/Http/Controllers/LeaveController.php`

**Interfaces:**
- Consumes: Cuti (Leave Request)
- Produces: Alur persetujuan (Manager Departemen -> HR Manager).

- [ ] **Step 1: Write the failing test**
```php
public function test_leave_requires_dept_manager_and_hr_approval()
{
    // Cuti diapprove dept manager, status = partial, diapprove hr = approved
}
```

- [x] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter test_leave_requires_dept_manager_and_hr_approval`

- [x] **Step 3: Write minimal implementation**
- [x] **5. Approval Cuti/Izin/Sakit**
    - Tambahkan kolom `manager_approved_at` dan `hr_approved_at` pada tabel cuti (Atau buat tabel baru `leave_requests`).
    - Alur: Karyawan Mengajukan -> Menunggu Manager -> Di-acc Manager -> Menunggu HR -> Di-acc HR -> Masuk ke list Cuti yang Valid.
    - Dan ada notifikasi ke karyawannya jika cutinya disetujui.

- [x] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter test_leave_requires_dept_manager_and_hr_approval`

- [x] **Step 5: Commit**
```bash
git add database/migrations/ app/Http/Controllers/LeaveController.php
git commit -m "feat: implement 2-step leave approval workflow"
```

---

### Task 6: Refactor Absensi (Perencanaan Harian & WFD Radius)

**Files:**
- Modify: `resources/views/backoffice/absensi.blade.php`
- Modify: `app/Http/Controllers/AttendanceController.php`

**Interfaces:**
- Consumes: Koordinat latitude/longitude WFD.
- Produces: Validasi jarak <= 100 meter dari titik WFD menggunakan Haversine Formula. Perubahan label Clock In/Out.

- [ ] **Step 1: Write the failing test**
```php
public function test_wfd_attendance_must_be_within_100_meters()
{
    // Test distance validation throws error if > 100m
}
```

- [ ] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter test_wfd_attendance_must_be_within_100_meters`

- [ ] **Step 3: Write minimal implementation**
Ubah label "Clock In" menjadi "Update Perencanaan harian (opsional)". Pastikan "Clock Out" wajib mengisi laporan (hapus sifat opsionalnya). Tambahkan logika fungsi Haversine di Controller. Jika tipe absen `wfd`, validasi jarak posisi user dan target `< 100` meter. Jika gagal, return error.

- [ ] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter test_wfd_attendance_must_be_within_100_meters`

- [ ] **Step 5: Commit**
```bash
git add app/Http/Controllers/AttendanceController.php resources/views/backoffice/absensi.blade.php
git commit -m "feat: apply haversine geolocation validation for WFD and rename clock in"
```

---

### Task 7: Tampilkan Hierarki di Halaman Pengaturan

**Files:**
- Modify: `app/Http/Controllers/SettingsController.php` (Atau controller yang handle pengaturan)
- Modify: `resources/views/backoffice/pengaturan.blade.php`

**Interfaces:**
- Consumes: Seluruh user dengan role spesifik.
- Produces: Visualisasi pohon/bagan organisasi di menu pengaturan.

- [ ] **Step 1: Write the failing test**
```php
public function test_hierarchy_tree_is_rendered()
{
    // Assert response contains hierarchy UI structure
}
```

- [ ] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter test_hierarchy_tree_is_rendered`

- [ ] **Step 3: Write minimal implementation**
Ambil data hierarki: 1 Superadmin -> HR Managers -> Dept Managers -> Karyawan. Buat satu tab HTML khusus di `pengaturan.blade.php` menggunakan desain *ul li* bersarang (nested) atau desain kartu yang menunjukan hubungan vertikal dari pimpinan tertinggi ke bawahan.

- [ ] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter test_hierarchy_tree_is_rendered`

- [ ] **Step 5: Commit**
```bash
git add resources/views/backoffice/pengaturan.blade.php
git commit -m "feat: display company hierarchy org-chart in settings"
```

---

### Task 8: Modul Manajemen Penjadwalan Training

**Files:**
- Create: `database/migrations/xxxx_xx_xx_create_trainings_table.php`
- Create: `app/Models/Training.php`
- Modify: `app/Http/Controllers/TrainingController.php`
- Create: `resources/views/backoffice/training/index.blade.php`

**Interfaces:**
- Consumes: Filter pencarian berdasarkan departemen dan riwayat keahlian/CV karyawan. Sistem notifikasi Laravel.
- Produces: HR Trainer dapat menjadwalkan training, mengirim notifikasi otomatis ke karyawan terpilih, dan meng-upload sertifikat ke profil karyawan pasca-training.

- [ ] **Step 1: Write the failing test**
```php
public function test_hr_trainer_can_schedule_training_and_notify_employee()
{
    // Test logic untuk penjadwalan training dan pengiriman notifikasi
}
```

- [ ] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter test_hr_trainer_can_schedule_training_and_notify_employee`

- [ ] **Step 3: Write minimal implementation**
Buat tabel `trainings` (berisi nama pelatihan, tanggal, `employee_id`, `status`, `certificate_path`). Di UI, HR Trainer dapat mencari karyawan berdasarkan departemen. Jika dijadwalkan, gunakan `Notification::send()` untuk memberi notifikasi ke karyawan tersebut. Tambahkan form *upload* sertifikat di UI setelah status training selesai, yang hasilnya muncul di profil karyawan (CV/Portofolio).

- [ ] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter test_hr_trainer_can_schedule_training_and_notify_employee`

- [ ] **Step 5: Commit**
```bash
git add database/migrations/ app/Models/ app/Http/Controllers/ resources/views/backoffice/training/
git commit -m "feat: implement training module with scheduling, notification, and certificate uploads"
```

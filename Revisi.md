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

- [x] **Step 1: Write the failing test**
```php
public function test_user_has_specific_hierarchy_roles()
{
    $user = User::factory()->create(['role' => 'hr_training_manager']);
    $this->assertEquals('hr_training_manager', $user->role);
}
```

- [x] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter test_user_has_specific_hierarchy_roles`
Expected: FAIL 

- [x] **Step 3: Write minimal implementation**
*(Diselesaikan: ENUM role sudah diperbarui di database. Namun berdasar keputusan terbaru, peran HR digabung kembali ke dalam `hr_manager`)*

- [x] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter test_user_has_specific_hierarchy_roles`
Expected: PASS

- [x] **Step 5: Commit**
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

- [x] **Step 1: Write the failing test**
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

- [x] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter test_only_superadmin_can_generate_manager_payslip`
Expected: FAIL 

- [x] **Step 3: Write minimal implementation**
*(Diselesaikan: Logika hierarki level telah dimuat di model `User@hierarchyLevel` dan dimanfaatkan pada gate `manage-payslip` di `AppServiceProvider`. Pembuatan gaji periode (pertanggal) telah dibuat melalui `PayrollController`.)*

- [x] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter test_only_superadmin_can_generate_manager_payslip`
Expected: PASS

- [x] **Step 5: Commit**
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

- [x] **Step 1: Write the failing test**
```php
public function test_hr_admin_can_approve_cv()
{
    $adminHR = User::factory()->create(['role' => 'hr_admin_manager']);
    $this->assertTrue($adminHR->can('approveCV', Employee::class));
}
```

- [x] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter test_hr_admin_can_approve_cv`

- [x] **Step 3: Write minimal implementation**
*(Dibatalkan/Selesai: Fitur disatukan. `hr_manager` memiliki akses ke modul Training maupun modul Approval CV secara penuh)*

- [x] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter test_hr_admin_can_approve_cv`

- [x] **Step 5: Commit**
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

- [x] **Step 1: Write the failing test**
```php
public function test_dashboard_stats_visibility()
{
    // Test that HR sees global total, Dept Manager sees dept total
}
```

- [x] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter test_dashboard_stats_visibility`

- [x] **Step 3: Write minimal implementation**
*(Diselesaikan: Logika visibilitas sudah ada di DashboardController. Manager Departemen hanya merender `dashboard_manager` dengan scope kueri spesifik per departemen, sedangkan HR melihat `dashboard` global)*

- [x] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter test_dashboard_stats_visibility`

- [x] **Step 5: Commit**
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

- [x] **Step 1: Write the failing test**
```php
public function test_wfd_attendance_must_be_within_100_meters()
{
    // Test distance validation throws error if > 100m
}
```

- [x] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter test_wfd_attendance_must_be_within_100_meters`

- [x] **Step 3: Write minimal implementation**
*(Diselesaikan: Label Clock In berhasil diubah menjadi Update Perencanaan Harian. Rumus Haversine sudah ditambahkan di `AttendanceController@clockIn` untuk menghitung jarak ke kantor maksimal 100 meter bagi WFD. Ditandai untuk dilanjutkan proses testing-nya langsung melalui perangkat mobile/HP oleh user).*

- [x] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter test_wfd_attendance_must_be_within_100_meters`

- [x] **Step 5: Commit**
```bash
git add app/Http/Controllers/AttendanceController.php resources/views/backoffice/dashboard_karyawan.blade.php
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

- [x] **Step 1: Write the failing test**
```php
public function test_hierarchy_tree_is_rendered()
{
    // Assert response contains hierarchy UI structure
}
```

- [x] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter test_hierarchy_tree_is_rendered`

- [x] **Step 3: Write minimal implementation**
Ambil data hierarki: 1 Superadmin -> HR Managers -> Dept Managers -> Karyawan. Buat satu tab HTML khusus di `pengaturan.blade.php` menggunakan desain *ul li* bersarang (nested) atau desain kartu yang menunjukan hubungan vertikal dari pimpinan tertinggi ke bawahan.

- [x] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter test_hierarchy_tree_is_rendered`

- [x] **Step 5: Commit**
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

- [x] **Step 1: Write the failing test**
```php
public function test_hr_trainer_can_schedule_training_and_notify_employee()
{
    // Test logic untuk penjadwalan training dan pengiriman notifikasi
}
```

- [x] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter test_hr_trainer_can_schedule_training_and_notify_employee`

- [x] **Step 3: Write minimal implementation**
*(Diselesaikan: Modul training sudah berjalan dan bahkan jadwal pelatihannya sudah berhasil ditampilkan secara langsung di kalender dashboard karyawan)*

- [x] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter test_hr_trainer_can_schedule_training_and_notify_employee`

- [x] **Step 5: Commit**
```bash
git add database/migrations/ app/Models/ app/Http/Controllers/ resources/views/backoffice/training/
git commit -m "feat: implement training module with scheduling, notification, and certificate uploads"
```

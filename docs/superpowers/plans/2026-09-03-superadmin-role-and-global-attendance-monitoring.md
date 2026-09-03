# SuperAdmin Role & Global Attendance Monitoring Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Restrukturisasi hak akses Super Admin sebagai *Executive Platform Owner* yang berfokus pada Pengelolaan HR Manager, Master Sistem, dan Monitoring Absensi Seluruh Perusahaan.

**Architecture:** Memisahkan peran operasional SDM (Rekrutmen & Manager Departemen) ke HR Manager, serta menambahkan rute, controller, dan tampilan baru bagi Super Admin untuk memantau data absensi seluruh karyawan dan manager di semua departemen.

**Tech Stack:** Laravel 11, Blade, Tailwind CSS, Pest/PHPUnit Test.

---

### Task 1: Penyekatan Hak Akses Route Super Admin & HR Manager

**Files:**
- Modify: `routes/web.php`
- Modify: `app/Http/Controllers/CVController.php`
- Test: `tests/Feature/SuperAdminAccessTest.php`

- [ ] **Step 1: Write failing test for Super Admin route access restrictions**

```php
// tests/Feature/SuperAdminAccessTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SuperAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_cannot_access_cv_management()
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($superAdmin)
                         ->withSession(['user_role' => 'super_admin'])
                         ->get(route('backoffice.cv.index'));

        $response->assertStatus(403);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=SuperAdminAccessTest`
Expected: FAIL (403 assertion failed because route currently allows Super Admin)

- [ ] **Step 3: Modify route middleware and CVController access check**

Update `routes/web.php` and `CVController.php` so `/backoffice/cv` strictly checks for `hr_manager` or `manager_departemen`.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=SuperAdminAccessTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add tests/Feature/SuperAdminAccessTest.php routes/web.php app/Http/Controllers/CVController.php
git commit -m "security: restrict CV management route access to HR Manager only"
```

---

### Task 2: Backend Controller & Route untuk Global Attendance Monitoring

**Files:**
- Create: `app/Http/Controllers/SuperAdminAttendanceController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/GlobalAttendanceTest.php`

- [ ] **Step 1: Write failing test for Super Admin Global Attendance**

```php
// tests/Feature/GlobalAttendanceTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GlobalAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_global_attendance()
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($superAdmin)
                         ->withSession(['user_role' => 'super_admin'])
                         ->get('/backoffice/super-admin/absensi');

        $response->assertStatus(200);
        $response->assertSee('Monitoring Absensi Global');
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=GlobalAttendanceTest`
Expected: FAIL (404 Not Found)

- [ ] **Step 3: Create SuperAdminAttendanceController and register route**

Implement `index` method fetching attendances with relations (`employee.department`, `employee.user`) and filters.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=GlobalAttendanceTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/SuperAdminAttendanceController.php routes/web.php tests/Feature/GlobalAttendanceTest.php
git commit -m "feat: add global attendance monitoring controller and route for super admin"
```

---

### Task 3: Views & Navigation Update

**Files:**
- Create: `resources/views/backoffice/super_admin_absensi.blade.php`
- Modify: `resources/views/layouts/admin.blade.php`

- [ ] **Step 1: Create super_admin_absensi.blade.php view**

Build filterable data table and daily attendance summary stats (Hadir, Izin, Alpha, WFO/WFH).

- [ ] **Step 2: Update sidebar navigation in admin.blade.php**

Add "Monitoring Absensi Global" item under Super Admin sidebar section pointing to `/backoffice/super-admin/absensi`.

- [ ] **Step 3: Run full feature tests**

Run: `php artisan test`
Expected: ALL PASS

- [ ] **Step 4: Commit**

```bash
git add resources/views/backoffice/super_admin_absensi.blade.php resources/views/layouts/admin.blade.php
git commit -m "ui: add global attendance monitoring view and update super admin sidebar navigation"
```

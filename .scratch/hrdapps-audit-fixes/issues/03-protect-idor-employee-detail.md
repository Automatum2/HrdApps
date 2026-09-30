# 03 — Proteksi IDOR Detail Karyawan untuk Manager Departemen

**What to build:** Tambahkan batasan otorisasi di `EmployeeController::show` agar user dengan role `manager_departemen` hanya dapat melihat detail karyawan yang departemennya sama dengan departemen manager tersebut.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] Validasi `department_id` pada `EmployeeController::show` untuk role `manager_departemen`
- [ ] Return 403 Forbidden atau redirect error jika karyawan berada di luar departemen manager

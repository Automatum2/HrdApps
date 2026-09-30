# 07 — Dinamisasi Pagination & Widget Counter Statistik Manajemen Karyawan

**What to build:** Ubah query list karyawan di `EmployeeController::manage` menjadi `paginate(10)` dan pasang `$employees->links()` pada `resources/views/backoffice/karyawan.blade.php`. Hitung kartu statistik bawah secara dinamis dari database.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] `EmployeeController::manage` mem-passing data statistik dinamis dan query berpaginasi
- [ ] Tampilan tabel dan footer menggunakan pagination dinamis
- [ ] Widget Total Staff, Aktif, Cuti, dan Baru Bulan Ini menampilkan angka riil

# 09 — Dinamisasi Kop dan Penandatangan Slip Gaji Karyawan

**What to build:** Ambil nama manager HR penandatangan slip gaji secara dinamis dari database (user dengan role `hr_manager`) dan gunakan konfigurasi nama perusahaan di `backoffice/slip_gaji_karyawan.blade.php`.

**Blocked by:** 05 — Koreksi Kalkulasi Potongan Alpha & Status Cuti di Payroll

**Status:** ready-for-agent

- [ ] Ambil data HR Manager penandatangan aktif di `PayrollController::index`
- [ ] Tampilkan nama penandatangan dinamis pada slip gaji view
- [ ] Ganti nama perusahaan statis dengan `config('app.name')`

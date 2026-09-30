# 04 — Perbaiki Typo Kolom Departemen pada Modul Laporan

**What to build:** Koreksi pemanggilan properti `$department->nama_departemen` menjadi `$department->nama_department` di `ReportController` untuk mencegah error saat generate CSV dan PDF laporan absensi & gaji.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] Ganti semua `nama_departemen` menjadi `nama_department` di `ReportController.php`
- [ ] Laporan absensi & gaji CSV/PDF berhasil digenerate tanpa crash

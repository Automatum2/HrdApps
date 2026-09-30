# 05 — Koreksi Kalkulasi Potongan Alpha & Status Cuti di Payroll

**What to build:** Perbaiki rumus penghitungan hari mangkir (alpha) di `PayrollController::calculatePayroll` dengan menyertakan status kehadiran 'cuti' sebagai pengurang hari kerja normal sehingga karyawan yang mengambil cuti sah tidak terpotong gajinya.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] Ambil jumlah kehadiran status 'cuti' pada periode payroll
- [ ] Hitung `$alpha = max(0, $hariKerjaNormal - ($hadir + $sakit + $izin + $cuti))`

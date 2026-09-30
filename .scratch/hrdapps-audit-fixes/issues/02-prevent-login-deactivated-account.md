# 02 — Cegah Login Akun Karyawan Nonaktif

**What to build:** Tambahkan validasi saat proses login di `AuthController::login` sehingga user yang status karyawan terkaitnya 'nonaktif' tidak diizinkan masuk dan session langsung di-logout.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] `AuthController::login` memeriksa status karyawan terkait
- [ ] Menampilkan pesan error yang jelas jika akun nonaktif

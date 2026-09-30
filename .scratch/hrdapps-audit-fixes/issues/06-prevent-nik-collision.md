# 06 — Generate NIK Unik Anti-Tabrakan (Collision Prevention)

**What to build:** Implementasikan mekanisme loop verifikasi keunikan (`do-while where('nik', $nik)->exists()`) pada saat pembuatan NIK pelamar di route `/karir/submit` dan saat approval pelamar di `CVController::approve`.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] Loop generate NIK di `routes/web.php` (`/karir/submit`)
- [ ] Loop generate NIK di `app/Http/Controllers/CVController.php` (`approve`)

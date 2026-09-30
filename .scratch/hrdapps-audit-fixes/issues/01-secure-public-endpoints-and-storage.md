# 01 — Amankan Endpoint Publik Insecure & Akses Storage

**What to build:** Hapus route berbahaya `/reset-absen` dan amankan rute akses berkas privat `/storage/{path}` agar hanya bisa diakses setelah login.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] Route `/reset-absen` dihapus dari `routes/web.php`
- [ ] Route `/storage/{path}` dilindungi dengan middleware `auth`

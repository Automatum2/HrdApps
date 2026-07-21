# Catatan Penting Selama Development

## 1. Masalah OPcache (Cache Server)
Jika Anda sering mendapati bahwa sistem tidak memuat rute, logika controller, atau struktur form yang baru saja diubah, hal itu kemungkinan besar karena **PHP OPcache**.

OPcache bertugas menyimpan kode yang sudah pernah dibaca agar aplikasi berjalan lebih cepat. Tapi saat *development*, ini bisa menyebabkan kode baru tidak terbaca secara *real-time*.

**Solusi:**
Selalu gunakan perintah ini saat menjalankan server di tahap *development* agar *cache* otomatis dimatikan:
```bash
php -d opcache.enable=0 artisan serve
```

Jika terlanjur *error* atau ada sisa *cache*, jalankan perintah ini di terminal terpisah:
```bash
php artisan optimize:clear
```

## 2. Fitur Email Simulasi
Saat ini *driver* email (di `.env`) diatur menjadi `MAIL_MAILER=log`. Ini artinya email simulasi aktivasi karyawan tidak akan benar-benar dikirimkan ke kotak masuk Gmail/Yahoo, melainkan hanya akan dicetak secara lokal di file:
`storage/logs/laravel.log`

Ketika HRDApps siap untuk dipasarkan atau diujicoba secara luas, Anda harus mengubah konfigurasi `.env` ke SMTP sungguhan (seperti Brevo, Mailtrap, atau Mailgun) agar karyawan benar-benar menerima link aktivasi di *smartphone* mereka.

# Flowchart Sistem HRDApps

## 1. Alur Utama Sistem

```mermaid
flowchart TD
    A([Mulai]) --> B[Halaman Login]
    B --> C{Verifikasi Login}
    C -->|Gagal| B
    C -->|Berhasil| D{Cek Role User}
    
    D -->|Super Admin| G[Dashboard Super Admin]
    D -->|Admin/HRD| E[Dashboard Admin]
    D -->|Karyawan| F[Dashboard Karyawan]
    
    G --> G1[Kelola HRD Manager]
    G --> G2[Kelola Karyawan]
    
    E --> E1[Lihat Data Karyawan]
    E --> E2[Manajemen Absensi]
    E --> E3[Manajemen Penggajian]
    E --> E4[Laporan & Rekap]
    E --> E5[Pengaturan Sistem]
    
    F --> F1[Absensi Saya]
    F --> F2[Slip Gaji Saya]
    
    G1 --> AG1[CRUD HRD Manager]
    G2 --> AG2[CRUD Karyawan & Gaji Pokok - Soft Delete]
    E1 --> A1[Lihat & Filter Karyawan]
    E2 --> A2[Kelola Absensi]
    E3 --> A3[Proses Penggajian]
    E4 --> A4[Generate Laporan]

    style A fill:#667eea,stroke:#5a67d8,color:#fff
    style B fill:#f6e05e,stroke:#d69e2e,color:#333
    style C fill:#fc8181,stroke:#e53e3e,color:#fff
    style D fill:#fc8181,stroke:#e53e3e,color:#fff
    style G fill:#a0aec0,stroke:#4a5568,color:#333
    style E fill:#68d391,stroke:#38a169,color:#333
    style F fill:#63b3ed,stroke:#3182ce,color:#fff
    style E3 fill:#f687b3,stroke:#d53f8c,color:#fff
```

---

## 2. Alur Penggajian Digital (Fitur Utama)

```mermaid
flowchart TD
    START([Mulai Proses Gaji]) --> A[Pilih Periode Gaji]
    A --> B[Tarik Data Kehadiran Karyawan]
    B --> C[Hitung Komponen Gaji]
    
    C --> C1[Gaji Pokok]
    C --> C2[Tunjangan]
    C --> C3[Potongan]
    C --> C4[Lembur]
    
    C1 --> D[Total Gaji Bersih]
    C2 --> D
    C3 --> D
    C4 --> D
    
    D --> E{Review & Approval}
    E -->|Revisi| C
    E -->|Setuju| F[Generate Slip Gaji]
    
    F --> G[Distribusi Digital]
    G --> G1[Kirim Slip via Email]
    G --> G2[Slip Tersedia di Dashboard Karyawan]
    G --> G3[Export PDF]
    
    G1 --> H[Simpan Riwayat]
    G2 --> H
    G3 --> H
    H --> I([Selesai])

    style START fill:#667eea,stroke:#5a67d8,color:#fff
    style D fill:#68d391,stroke:#38a169,color:#333
    style E fill:#fc8181,stroke:#e53e3e,color:#fff
    style F fill:#f687b3,stroke:#d53f8c,color:#fff
    style G fill:#f6ad55,stroke:#dd6b20,color:#333
    style I fill:#667eea,stroke:#5a67d8,color:#fff
```

---

## 3. Alur Login & Autentikasi

```mermaid
flowchart TD
    A([User Akses Website]) --> B[Tampilkan Form Login]
    B --> C[Input Username & Password]
    C --> D[Pilih Status Kerja]
    D --> E[Klik Login]
    E --> F{Validasi Kredensial}
    
    F -->|Username/Password Salah| G[Tampilkan Error]
    G --> B
    
    F -->|Valid| H[Buat Session]
    H --> I{Cek Role}
    I -->|Super Admin| JN[Redirect ke Dashboard Super Admin]
    I -->|Admin| J[Redirect ke Dashboard Admin]
    I -->|Karyawan| K[Redirect ke Dashboard Karyawan]
    
    L[Klik Logout] --> M[Hapus Session]
    M --> B

    style A fill:#667eea,stroke:#5a67d8,color:#fff
    style F fill:#fc8181,stroke:#e53e3e,color:#fff
    style I fill:#fc8181,stroke:#e53e3e,color:#fff
    style JN fill:#a0aec0,stroke:#4a5568,color:#333
    style J fill:#68d391,stroke:#38a169,color:#333
    style K fill:#63b3ed,stroke:#3182ce,color:#fff
```

---

## 4. Alur CRUD Karyawan (Super Admin)

```mermaid
flowchart TD
    A[Menu Kelola Karyawan Super Admin] --> B[Tampilkan Daftar Karyawan]
    B --> C{Aksi}
    
    C -->|Tambah| D[Form/Modal Tambah Karyawan]
    D --> D1[Input Data: Nama, NIK, Jabatan, Gaji Pokok, dll]
    D1 --> D2[Simpan ke Database]
    D2 --> B
    
    C -->|Lihat Detail| E[Modal/Detail Karyawan]
    E --> B
    
    C -->|Edit| F[Modal/Form Edit Karyawan]
    F --> F1[Update Data & Gaji Pokok]
    F1 --> B
    
    C -->|Hapus| G{Konfirmasi Nonaktifkan?}
    G -->|Ya| G1[Soft Delete / Ubah Status Jadi Nonaktif]
    G1 --> B
    G -->|Tidak| B
    
    C -->|Cari| H[Filter & Search]
    H --> B

    style A fill:#f6ad55,stroke:#dd6b20,color:#333
    style C fill:#fc8181,stroke:#e53e3e,color:#fff
    style G fill:#fc8181,stroke:#e53e3e,color:#fff
```

---

## 5. Alur Absensi

```mermaid
flowchart TD
    A[Menu Absensi] --> B{Role User}
    
    B -->|Karyawan| C[Dashboard Absensi Saya]
    C --> C1[Clock In - Catat Jam Masuk]
    C --> C2[Clock Out - Catat Jam Keluar]
    C --> C3[Lihat Riwayat Absensi]
    
    C1 --> C4[Simpan dengan Timestamp & Status Kerja]
    C2 --> C4
    C4 --> C
    
    B -->|Admin| D[Dashboard Kelola Absensi]
    D --> D1[Lihat Rekap Absensi Semua Karyawan]
    D --> D2[Edit Absensi Manual]
    D --> D3[Export Rekap Absensi]
    D --> D4[Filter by Tanggal / Karyawan / Status]

    style A fill:#9ae6b4,stroke:#38a169,color:#333
    style B fill:#fc8181,stroke:#e53e3e,color:#fff
    style C fill:#63b3ed,stroke:#3182ce,color:#fff
    style D fill:#68d391,stroke:#38a169,color:#333
```
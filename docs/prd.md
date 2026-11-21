# 📘 Product Requirements Document (PRD)
## G-English — Learning Management System

**Versi:** 1.0  
**Terakhir diperbarui:** 27 Juli 2026  
**Teknologi:** Laravel 13 + Filament v4 + MySQL + Cloudflare R2  

---

## 1. Ringkasan Produk

**G-English** adalah sistem manajemen kursus bahasa Inggris (*Learning Management System*) yang dirancang khusus untuk lembaga kursus/bimbingan belajar. Sistem ini mengelola seluruh siklus operasional mulai dari pendaftaran siswa baru, penjadwalan kelas, absensi digital (QR Code), jurnal mengajar, ujian berbasis komputer (CBT), hingga keuangan (pembayaran SPP & penggajian tutor).

Sistem memiliki **4 portal terpisah** berdasarkan peran pengguna, masing-masing dengan fitur dan akses yang disesuaikan.

---

## 2. Pengguna & Portal

### 2.1 Panel Admin (`/admin`)
**Peran:** Administrator sistem / Pemilik lembaga  
**Akses:** Penuh ke seluruh fitur dan data

| Modul | Fitur |
|---|---|
| **Dashboard** | Statistik siswa aktif, pendapatan bulan ini, total tutor & staf. Grafik kehadiran, grafik pendapatan, jadwal hari ini, aktivitas terbaru, quick links |
| **Administrasi** | Kelola Pendaftaran Siswa Baru (Admissions), Data Siswa, Data Tutor, Data Pengguna (User) |
| **Akademik** | Tahun Ajaran, Semester, Kelas, Mata Pelajaran, Jadwal Kelas, Materi Belajar (Video/Mindmap/File), Ujian CBT |
| **Harian** | Scan Absensi (QR Code), Display QR Code, Rekap Absensi, Jurnal Mengajar, Validasi Jurnal |
| **Evaluasi** | Evaluasi Siswa, Hasil Ujian CBT |
| **Keuangan** | Pembayaran SPP Siswa, Penggajian Tutor, Pengeluaran Operasional, Penghargaan/Honor |
| **Laporan** | Laporan Kehadiran, Rapor Siswa, Laporan Keuangan |
| **Master Data** | Pengaturan Sistem (nama lembaga, logo, dll) |
| **Pengaturan Akun** | Edit Profil, Ganti Avatar (Cloudflare R2), Ganti Password |

### 2.2 Panel Tutor (`/tutor`)
**Peran:** Pengajar / Guru  
**Akses:** Data kelas sendiri, jurnal, absensi, evaluasi

| Modul | Fitur |
|---|---|
| **Dashboard** | Statistik performa, jadwal hari ini |
| **Akademik** | Ujian CBT (buat & kelola untuk kelas sendiri), Materi Belajar |
| **Harian** | Scan Absensi, Jurnal Mengajar (CRUD), Jadwal Kelas |
| **Evaluasi** | Evaluasi Siswa |
| **Keuangan** | Riwayat Gaji Sendiri |

### 2.3 Panel Siswa (`/student`)
**Peran:** Peserta didik  
**Akses:** Data pribadi, absensi, materi, ujian

| Modul | Fitur |
|---|---|
| **Dashboard** | Statistik performa, jadwal hari ini |
| **Akademik** | Akses Materi Belajar (Video YouTube, Mindmap, File), Mengerjakan Ujian CBT |
| **Harian** | Scan Absensi (QR Code), Riwayat Kehadiran, Lihat Jurnal Kelas |
| **Keuangan** | Riwayat Pembayaran SPP |
| **Rapor** | Lihat rapor dan nilai |

### 2.4 Panel Orang Tua (`/parent`)
**Peran:** Wali murid  
**Akses:** Monitoring anak (read-only)

| Modul | Fitur |
|---|---|
| **Dashboard** | Statistik anak (kehadiran, nilai), notifikasi |
| **Monitoring** | Pantau Kehadiran Anak (kalender), Lihat Jurnal Kelas Anak |
| **Keuangan** | Riwayat Pembayaran SPP Anak |
| **Notifikasi** | Push Notification (WebPush) |

---

## 3. Fitur Utama

### 3.1 Autentikasi & Otorisasi
- **Login satu pintu** (`/login`) dengan routing otomatis berdasarkan role
- Sistem role: `admin`, `tutor`, `siswa`, `parent`
- RBAC menggunakan Spatie Permission (opsional)
- Edit profil dengan upload avatar ke **Cloudflare R2**
- Ganti password dengan validasi password lama
- **Auto-provisioning akun:** Saat siswa atau tutor dibuat, akun user otomatis dibuatkan

### 3.2 Pendaftaran Siswa Baru (Admissions)
- Form pendaftaran publik (`/pendaftaran-siswa`)
- Data lengkap: biodata siswa, data orang tua (ayah & ibu), level kursus
- Nomor registrasi otomatis (`REG-YYYY-XXXX`)
- Status: pending → accepted → enrolled / rejected
- Informasi pembayaran pendaftaran (biaya, diskon, metode pembayaran)

### 3.3 Manajemen Kelas & Jadwal
- Kelas terkait dengan tahun ajaran, tutor, dan mata pelajaran
- Siswa terdaftar di kelas melalui *pivot table* (`class_student`)
- Jadwal kelas (JSON-based): hari, jam mulai, jam selesai
- Jadwal individual per tutor

### 3.4 Absensi Digital (QR Code)
- QR Code yang dapat ditampilkan di layar (`/display-qr`)
- Scan absensi via kamera smartphone
- Pencatatan: status (hadir/izin/sakit/alpa), waktu masuk, waktu keluar
- Mendukung absensi tutor DAN siswa
- Rekap absensi per kelas dan per individu

### 3.5 Jurnal Mengajar
- Tutor mengisi jurnal setiap selesai mengajar
- Data: topik, deskripsi, materi, jumlah JP, foto dokumentasi, link
- Alur validasi: tutor submit → admin validasi (approve/reject)
- Jurnal tervalidasi menjadi dasar perhitungan gaji tutor

### 3.6 Ujian Berbasis Komputer (CBT)
- Admin/Tutor membuat soal ujian dengan opsi A-E + gambar/attachment
- Konfigurasi ujian: durasi, jumlah percobaan, waktu mulai/selesai
- Fitur anti-kecurangan: *shuffle* soal, *shuffle* opsi, batas pelanggaran (*violation count*), *auto-save*
- Siswa mengerjakan ujian dengan timer dan navigasi soal
- Penilaian otomatis dengan skor dan riwayat percobaan

### 3.7 Materi Belajar
- Tipe: Video (YouTube embed), Mindmap (gambar), File (PDF/dokumen)
- Terkait dengan mata pelajaran
- Status publikasi (published/draft)
- Akses oleh siswa dan tutor

### 3.8 Evaluasi Siswa
- Penilaian per siswa per mata pelajaran
- Input oleh tutor, divalidasi oleh admin
- Dapat dicetak sebagai rapor/sertifikat

### 3.9 Keuangan
#### Pembayaran SPP Siswa
- Tagihan bulanan per siswa
- Status: paid / unpaid / overdue
- Tanggal jatuh tempo dan tanggal pembayaran
- Catatan pembayaran

#### Penggajian Tutor
- Perhitungan berdasarkan total JP (Jam Pelajaran) dari jurnal tervalidasi
- Status: paid / unpaid
- Riwayat gaji per bulan

#### Pengeluaran Operasional
- Pencatatan pengeluaran harian
- Kategori, jumlah, deskripsi

### 3.10 Laporan & Cetak
- Laporan Kehadiran (per kelas / per siswa)
- Rapor Siswa / Sertifikat
- Slip Gaji Tutor (cetak PDF)
- Kwitansi Pembayaran SPP (cetak PDF)
- Laporan Keuangan (pendapatan vs pengeluaran)

### 3.11 Notifikasi
- Push Notification via WebPush (terutama untuk orang tua)
- Database notifications (Filament built-in)

### 3.12 Multi-Bahasa
- Dukungan 7 bahasa: Indonesia, English, 中文, Pinyin, 日本語, Romaji, العربية, Arabizi
- Pergantian bahasa langsung (live-switch) dengan animasi overlay

### 3.13 Desain & UX
- Dark mode / Light mode toggle
- Animasi transisi halaman (View Transitions API)
- Desain premium: glassmorphism, gradient, micro-animations
- Responsive untuk mobile & desktop
- Ikon sidebar berwarna-warni
- Tombol & card dengan efek hover interaktif

---

## 4. Infrastruktur & Teknologi

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 13.20.0 |
| Admin Panel | Filament v4 |
| Database | MySQL 8.4.3 |
| PHP | 8.3.30 |
| File Storage | Cloudflare R2 (S3-compatible) |
| Authentication | Laravel Built-in + Spatie Permission |
| Notifications | WebPush (laravel-notification-channels/webpush) |
| Import/Export | Filament Import/Export Actions |

---

## 5. Alur Kerja Utama

### 5.1 Alur Pendaftaran Siswa
```
Calon Siswa → Isi Form Online → Status: Pending
  → Admin Review → Accept / Reject
    → Accept → Buat data Student + User otomatis
      → Akun Parent otomatis (dari nomor HP orang tua)
```

### 5.2 Alur Kegiatan Harian
```
Tutor & Siswa → Scan QR Absensi → Tercatat otomatis
Tutor → Isi Jurnal Mengajar → Submit
Admin → Validasi Jurnal → Approved / Rejected
  → Approved → Masuk hitungan JP untuk gaji
```

### 5.3 Alur Ujian CBT
```
Tutor → Buat Ujian + Soal → Aktifkan
Siswa → Mulai Ujian → Timer berjalan → Submit / Auto-submit
Sistem → Koreksi otomatis → Skor tersimpan
Tutor/Admin → Lihat hasil & statistik
```

### 5.4 Alur Pembayaran
```
Admin → Buat tagihan SPP bulanan
Siswa/Ortu → Lihat tagihan
Pembayaran dilakukan (offline) → Admin update status: Paid
Admin → Cetak kwitansi (PDF)
```

---

## 6. Keamanan

- Password di-hash menggunakan bcrypt (Laravel default)
- Validasi password lama saat ganti password
- Panel akses terbatas berdasarkan role (`canAccessPanel`)
- File upload melalui Cloudflare R2 (bukan local storage)
- CSRF protection pada semua form
- Session management dengan Laravel

---

## 7. Batasan & Catatan

- Pembayaran bersifat **offline** (admin yang mengupdate status secara manual, belum ada payment gateway)
- QR Code absensi memerlukan kamera pada perangkat pengguna
- CBT menggunakan browser-based (bukan aplikasi terpisah)
- Push notification memerlukan browser yang mendukung Web Push API
- Multi-bahasa menggunakan file terjemahan statis (bukan AI translation)

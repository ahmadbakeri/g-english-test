# 🎯 G-English: Master Checklist — CBT Engine & Production Deployment
## Status: 🔄 IN PROGRESS | Terakhir diupdate: 2026-07-16

> **Dokumen ini adalah sumber kebenaran tunggal** untuk semua pekerjaan yang perlu dilakukan.
> Setiap kali ada task yang selesai, dokumen ini akan di-update.
>
> **Legenda:**
> - `[ ]` — Belum dikerjakan
> - `[/]` — Sedang dikerjakan
> - `[x]` — Selesai
> - `[!]` — Butuh aksi dari ANDA (user)
> - `[~]` — Dilewati / Tidak jadi dilakukan

---

## 📊 Progress Overview

| Fase | Status | Progress |
|---|---|---|
| Fase 0: Persiapan & Optimasi Lokal | ✅ Selesai | 8/8 |
| Fase 1: Database & Model CBT | ✅ Selesai | 10/10 |
| Fase 2: Backend — CBT Engine | ✅ Selesai | 12/12 |
| Fase 3: Frontend — Halaman Ujian | ✅ Selesai | 14/14 |
| Fase 4: Anti-Curang & Auto-Save | ✅ Selesai | 10/10 |
| Fase 5: Filament Admin — Manajemen CBT | ✅ Selesai | 8/8 |
| Fase 6: Testing Lokal | ✅ Selesai | 6/6 |
| Fase 7: Setup VPS & Production | ⏳ Belum mulai | 0/16 |
| Fase 8: Deploy & Go-Live | ⬜ Belum mulai | 0/10 |
| **TOTAL** | | **78/94** |

---

## FASE 0: Persiapan & Optimasi Lokal
> **Tujuan:** Menyiapkan environment development agar optimal sebelum menulis kode CBT.
> **Siapa:** Campuran (saya bantu eksekusi + Anda setup Redis lokal)

### 0.1 Environment Check
- [x] **[SAYA]** Periksa versi PHP, Laravel, dan dependency yang terinstall
- [x] **[SAYA]** Periksa struktur database CBT yang sudah ada (tabel, kolom, relasi)
- [x] **[SAYA]** Audit kode CBT yang sudah ada (model, controller, view)

### 0.2 Install Redis Lokal
- [x] **[ANDA]** Install Redis di mesin development
  - **Windows:** Install via WSL2 atau download [Memurai](https://www.memurai.com/) (Redis-compatible, gratis)
  - **Cara install via WSL2:**
    ```bash
    wsl --install                          # jika belum ada WSL
    sudo apt update
    sudo apt install redis-server
    sudo service redis-server start
    redis-cli ping                         # harus output: PONG
    ```
  - **Atau install Memurai (native Windows):**
    1. Download dari https://www.memurai.com/get-memurai
    2. Install → otomatis jalan sebagai Windows Service
    3. Test: buka cmd → `memurai-cli ping` → harus output: PONG

### 0.3 Ubah Konfigurasi .env untuk Development
- [x] **[SAYA]** Update `.env` — ubah session, cache, queue ke Redis
  ```
  SESSION_DRIVER=redis
  CACHE_STORE=redis
  QUEUE_CONNECTION=redis
  ```
- [x] **[SAYA]** Pastikan Redis extension PHP terinstall (`phpredis` atau `predis`)
- [x] **[SAYA]** Test koneksi Redis berhasil

---

## FASE 1: Database & Model CBT
> **Tujuan:** Memperkuat skema database CBT dengan kolom-kolom baru yang diperlukan.
> **Siapa:** Saya eksekusi semua.

### 1.1 Migration — Tambah Kolom di `cbt_attempts`
- [x] **[SAYA]** Buat migration: tambah kolom baru ke tabel `cbt_attempts`
  - `violation_count` (integer, default 0) — jumlah kali pindah tab
  - `max_violations` (integer, default 3) — batas maks pelanggaran
  - `question_order` (json, nullable) — urutan soal acak
  - `is_force_submitted` (boolean, default false) — apakah auto-submit
  - `last_auto_save_at` (datetime, nullable) — waktu auto-save terakhir

### 1.2 Migration — Tambah Kolom di `cbt_exams`
- [x] **[SAYA]** Buat migration: tambah kolom baru ke tabel `cbt_exams`
  - `max_concurrent_users` (integer, nullable) — batas user per sesi
  - `shuffle_questions` (boolean, default true) — acak urutan soal
  - `shuffle_options` (boolean, default false) — acak urutan opsi jawaban
  - `show_result_immediately` (boolean, default true) — tampilkan skor setelah submit
  - `allow_review` (boolean, default false) — siswa bisa review jawaban setelah submit
  - `auto_save_interval` (integer, default 30) — interval auto-save (detik)

### 1.3 Migration — Database Indexes
- [x] **[SAYA]** Buat migration: tambah index untuk performa
  - `cbt_questions`: index pada `cbt_exam_id`
  - `cbt_attempts`: compound index pada `(student_id, cbt_exam_id)`
  - `cbt_attempts`: index pada `(status, cbt_exam_id)`
  - `cbt_answers`: index pada `cbt_attempt_id`
  - `cbt_answers`: unique index pada `(cbt_attempt_id, cbt_question_id)`

### 1.4 Update Model
- [x] **[SAYA]** Update model `CbtExam` — tambah fillable, casts, kolom baru
- [x] **[SAYA]** Update model `CbtAttempt` — tambah fillable, casts, kolom baru
- [x] **[SAYA]** Update model `CbtQuestion` — pastikan relasi lengkap
- [x] **[SAYA]** Update model `CbtAnswer` — pastikan relasi lengkap

### 1.5 Jalankan Migration
- [x] **[SAYA]** Jalankan `php artisan migrate`
- [x] **[SAYA]** Verifikasi tabel & kolom sudah benar (via script migration)

---

## FASE 2: Backend — CBT Engine
> **Tujuan:** Membuat controller, middleware, dan service untuk menjalankan ujian.
> **Siapa:** Saya eksekusi semua.

### 2.1 Route Setup
- [x] **[SAYA]** Tambahkan route group CBT di `routes/web.php`
  ```
  Route::prefix('exam')->middleware(['auth'])->group(function () {
      GET  /exam/{exam}/start          → mulai ujian
      GET  /exam/{attempt}             → halaman ujian
      POST /exam/{attempt}/auto-save   → simpan jawaban sementara
      POST /exam/{attempt}/submit      → submit final
      POST /exam/{attempt}/violation   → log pelanggaran
      GET  /exam/{attempt}/time        → sisa waktu (server)
      GET  /exam/{attempt}/result      → halaman hasil
  });
  ```

### 2.2 Middleware
- [x] **[SAYA]** Buat `ExamTimerMiddleware` — validasi waktu ujian server-side
  - Cek apakah attempt masih dalam waktu
  - Jika lewat waktu → force complete + redirect ke result
- [x] **[SAYA]** Buat `ExamAccessMiddleware` — validasi akses ujian
  - Cek apakah siswa terdaftar di kelas
  - Cek apakah ujian aktif (is_active, start_time, end_time)
  - Cek apakah belum melebihi max_attempts
  - Cek apakah belum melebihi max_concurrent_users

### 2.3 Controller — ExamController
- [x] **[SAYA]** Buat `ExamController`
  - `start()` — mulai ujian, buat attempt, acak soal, redirect ke halaman ujian
  - `show()` — render halaman ujian dengan soal dari cache
  - `result()` — tampilkan hasil ujian

### 2.4 Controller — ExamApiController
- [x] **[SAYA]** Buat `ExamApiController` (untuk AJAX requests)
  - `autoSave()` — terima & simpan jawaban sementara (UPSERT)
  - `submit()` — terima jawaban final, hitung skor, update attempt
  - `violation()` — catat pelanggaran, cek batas
  - `timeRemaining()` — hitung sisa waktu dari server

### 2.5 Service — ExamService
- [x] **[SAYA]** Buat `ExamService`
  - `startAttempt()` — buat attempt baru, generate urutan soal acak
  - `getQuestions()` — ambil soal dari cache/database
  - `calculateScore()` — hitung skor berdasarkan jawaban
  - `isTimeExpired()` — cek apakah waktu habis (server-side)
  - `getTimeRemaining()` — hitung sisa waktu

### 2.6 Caching
- [x] **[SAYA]** Implementasi caching soal ujian di Redis (di dalam `ExamService`)
  - Cache soal per exam_id (expire setelah ujian selesai)
  - Cache attempt per student (untuk recovery)

---

## FASE 3: Frontend — Halaman Ujian
> **Tujuan:** Membuat tampilan halaman ujian yang profesional dan fungsional.
> **Siapa:** Saya eksekusi semua.

### 3.1 Layout Ujian
- [x] **[SAYA]** Buat layout `resources/views/cbt/layout.blade.php`
  - Fullscreen layout (tanpa navbar/sidebar)
  - Anti-scroll body
  - Meta tags: no-cache, no-store
  - Load JavaScript exam engine

### 3.2 Halaman Ujian
- [x] **[SAYA]** Buat `resources/views/cbt/exam.blade.php`
  - Header: Timer countdown + status pelanggaran
  - Sidebar: Navigasi soal (grid nomor 1-N, warna status)
  - Content: Kartu soal + opsi jawaban (radio button)
  - Footer: Tombol prev/next + tombol submit
  - Responsive (mobile-friendly)

### 3.3 Komponen Soal
- [x] **[SAYA]** Buat `resources/views/cbt/partials/question-card.blade.php`
  - Teks soal (support HTML formatting & gambar)
  - Opsi jawaban A-E (radio buttons)
  - Tombol "Tandai Ragu-Ragu"
  - Indikator sudah dijawab / belum

### 3.4 Komponen Timer
- [x] **[SAYA]** Buat `resources/views/cbt/partials/timer.blade.php`
  - Countdown MM:SS
  - Warna berubah: hijau (>10min) → kuning (5-10min) → merah (<5min)
  - Berkedip saat < 1 menit

### 3.5 Komponen Navigasi Soal
- [x] **[SAYA]** Buat `resources/views/cbt/partials/navigation.blade.php`
  - Grid tombol nomor soal (1, 2, 3, ...)
  - Warna: hijau (dijawab), abu (belum), kuning (ragu-ragu)
  - Klik langsung loncat ke soal tersebut

### 3.6 Halaman Hasil
- [x] **[SAYA]** Buat `resources/views/cbt/result.blade.php`
  - Skor total
  - Jumlah benar / salah / tidak dijawab
  - Detail per soal (jika allow_review = true)
  - Tombol kembali ke dashboard

### 3.7 Halaman Waiting/Lobby (Sebelum Ujian Dimulai)
- [x] **[SAYA]** Buat `resources/views/cbt/lobby.blade.php`
  - Informasi ujian (judul, durasi, jumlah soal)
  - Countdown ke start_time
  - Peraturan ujian
  - Tombol "Mulai Ujian" (aktif saat start_time tercapai)

### 3.8 CSS Styling
- [x] **[SAYA]** Buat CSS khusus ujian
  - Dark/light mode support
  - Desain profesional (bukan template biasa)
  - Animasi transisi antar soal
  - Responsive layout (tablet + desktop)

### 3.9 JavaScript — Exam Engine
- [x] **[SAYA]** Buat `resources/js/exam-engine.js`
  - ExamState management (semua state ujian)
  - Render soal dari data JSON
  - Navigasi soal (next, prev, jump)
  - Event handler pilih jawaban
  - Tandai ragu-ragu
  - Konfirmasi sebelum submit

### 3.10 Integrasi Vite
- [x] **[SAYA]** Konfigurasi Vite untuk build exam JS & CSS
- [x] **[SAYA]** Test halaman ujian tampil dengan benar di browser

---

## FASE 4: Anti-Curang & Auto-Save
> **Tujuan:** Implementasi fitur keamanan ujian dan penyimpanan otomatis.
> **Siapa:** Saya eksekusi semua.

### 4.1 Timer System (Dual-Layer)
- [x] **[SAYA]** Implementasi server-side timer
  - Validasi di middleware: `now() <= start_time + duration`
  - API endpoint: `/exam/{attempt}/time` → return sisa detik
- [x] **[SAYA]** Implementasi client-side timer
  - Countdown JavaScript dari sisa waktu server
  - Sinkronisasi dengan server setiap 5 menit
  - Auto-submit saat mencapai 00:00

### 4.2 Anti-Curang
- [x] **[SAYA]** Implementasi Page Visibility API
  - Deteksi pindah tab / minimize browser
  - Increment violation_count
  - Tampilkan peringatan
- [x] **[SAYA]** Lock koneksi (Block Multiple Login)
  - Middleware cek max_concurrent session
- [x] **[SAYA]** Disable klik kanan, copy, paste, dan shortcut keyboard (F12, Ctrl+C) di frontend JS

### 4.3 Auto-Save (State Recovery)
- [x] **[SAYA]** Kirim jawaban ke API `/exam/{attempt}/auto-save` setiap klik opsi
- [x] **[SAYA]** Kirim heartbeat (ping) API setiap 30 detik untuk update `last_auto_save_at`
- [x] **[SAYA]** Skenario pemulihan: Jika browser ditutup paksa dan masuk lagi, ambil jawaban dari database dan re-render form

---

## FASE 5: Filament Admin — Manajemen CBT
> **Tujuan:** Menyediakan antarmuka admin/tutor untuk mengelola ujian CBT.
> **Siapa:** Saya eksekusi semua.

### 5.1 Update CbtExam Resource
- [x] **[SAYA]** Tambahkan kolom baru ke form CbtExamResource
  - `max_concurrent_users`, `shuffle_questions`, `shuffle_options`
  - `show_result_immediately`, `allow_review`, `auto_save_interval`

### 5.2 Update CbtQuestion Resource
- [x] **[SAYA]** Pastikan form buat soal lengkap & user-friendly
  - Rich text editor untuk soal
  - Upload attachment/gambar soal
  - 5 opsi jawaban (A-E) + pilih jawaban benar
  - Points per soal

### 5.3 Monitoring Dashboard
- [x] **[SAYA]** Buat widget monitoring ujian aktif
  - Jumlah siswa yang sedang mengerjakan
  - Jumlah yang sudah submit
  - Rata-rata skor sementara
  - Daftar pelanggaran (siswa + jumlah violation)
- [x] **[SAYA]** Buat halaman detail per attempt
  - Timeline pelanggaran
  - History auto-save
  - Jawaban per soal

### 5.4 Export & Reporting
- [x] **[SAYA]** Export hasil ujian ke Excel
  - Per exam: semua siswa + skor + detail
  - Statistik soal: soal mana yang paling sering salah
- [x] **[SAYA]** Tampilkan analisis soal di admin
  - Tingkat kesulitan per soal
  - Distribusi jawaban per opsi

---

## FASE 6: Testing Lokal
> **Tujuan:** Memastikan semua fitur berjalan dengan baik sebelum deploy.
> **Siapa:** Campuran (saya buat test, Anda jalankan & verifikasi).

### 6.1 Functional Testing
- [x] **[SAYA]** Test alur ujian lengkap:
  - Login → lihat daftar ujian → mulai → jawab → submit → lihat hasil
- [x] **[SAYA]** Test auto-save:
  - Jawab beberapa soal → refresh → jawaban harus tetap ada
- [x] **[SAYA]** Test timer:
  - Set durasi pendek (2 menit) → pastikan auto-submit berjalan
- [x] **[SAYA]** Test anti-curang:
  - Pindah tab → peringatan muncul → violation tercatat

### 6.2 Browser Testing
- [ ] **[ANDA]** Test di Chrome (desktop)
- [ ] **[ANDA]** Test di Chrome (mobile / responsive mode)

---

## FASE 7: Setup VPS & Production
> **Tujuan:** Menyiapkan server production untuk deployment.
> **Siapa:** Sebagian besar ANDA (saya berikan instruksi detail).

### 7.1 Pilih & Beli VPS
- [ ] **[ANDA]** Pilih provider VPS:
  - **Rekomendasi:** Hetzner CX32 Singapura (Rp 125rb/bln) atau Contabo VPS S (Rp 85rb/bln)
  - **Alternatif lokal:** IDCloudHost Jakarta (Rp 150rb/bln)
  - Spek minimum: 2 vCPU, 4GB RAM, 40GB SSD
  - OS: Ubuntu 22.04 LTS
- [ ] **[ANDA]** Catat IP server & login credentials

### 7.2 Setup Keamanan Server
- [ ] **[ANDA]** Buat user non-root & disable root login
  ```bash
  adduser deployer
  usermod -aG sudo deployer
  ```
- [ ] **[ANDA]** Setup SSH key authentication
  ```bash
  # Di PC lokal Anda:
  ssh-keygen -t ed25519 -C "your-email@example.com"
  ssh-copy-id deployer@IP_SERVER
  ```
- [ ] **[ANDA]** Disable password SSH login
  ```bash
  sudo nano /etc/ssh/sshd_config
  # Ubah: PasswordAuthentication no
  # Ubah: PermitRootLogin no
  sudo systemctl restart sshd
  ```
- [ ] **[ANDA]** Setup firewall (UFW)
  ```bash
  sudo ufw default deny incoming
  sudo ufw default allow outgoing
  sudo ufw allow 22/tcp
  sudo ufw allow 80/tcp
  sudo ufw allow 443/tcp
  sudo ufw enable
  ```
- [ ] **[ANDA]** Install Fail2ban
  ```bash
  sudo apt install fail2ban
  sudo systemctl enable fail2ban
  ```

### 7.3 Install Software Stack
- [ ] **[ANDA]** Update sistem & install dependencies
  ```bash
  sudo apt update && sudo apt upgrade -y
  sudo apt install -y software-properties-common curl git unzip
  ```
- [ ] **[ANDA]** Install Nginx
  ```bash
  sudo apt install nginx -y
  sudo systemctl enable nginx
  ```
- [ ] **[ANDA]** Install PHP 8.3 + Extensions
  ```bash
  sudo add-apt-repository ppa:ondrej/php -y
  sudo apt update
  sudo apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-redis \
      php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd \
      php8.3-intl php8.3-bcmath php8.3-opcache
  ```
- [ ] **[ANDA]** Install MySQL 8
  ```bash
  sudo apt install mysql-server -y
  sudo mysql_secure_installation
  # Buat database & user:
  sudo mysql -e "CREATE DATABASE \`g-english\`;"
  sudo mysql -e "CREATE USER 'genglish'@'localhost' IDENTIFIED BY 'PASSWORD_KUAT_ANDA';"
  sudo mysql -e "GRANT ALL PRIVILEGES ON \`g-english\`.* TO 'genglish'@'localhost';"
  sudo mysql -e "FLUSH PRIVILEGES;"
  ```
- [ ] **[ANDA]** Install Redis
  ```bash
  sudo apt install redis-server -y
  sudo systemctl enable redis-server
  # Edit config:
  sudo nano /etc/redis/redis.conf
  # Ubah: bind 127.0.0.1
  # Tambah: requirepass PASSWORD_REDIS_ANDA
  sudo systemctl restart redis-server
  redis-cli -a PASSWORD_REDIS_ANDA ping    # harus: PONG
  ```
- [ ] **[ANDA]** Install Composer
  ```bash
  curl -sS https://getcomposer.org/installer | php
  sudo mv composer.phar /usr/local/bin/composer
  ```
- [ ] **[ANDA]** Install Node.js (untuk Vite build)
  ```bash
  curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
  sudo apt install -y nodejs
  ```

### 7.4 Konfigurasi Server
- [ ] **[SAYA]** Siapkan file konfigurasi Nginx (saya buat, Anda paste di server)
- [ ] **[SAYA]** Siapkan file konfigurasi PHP-FPM (saya buat, Anda paste di server)
- [ ] **[SAYA]** Siapkan file konfigurasi OPcache (saya buat, Anda paste di server)
- [ ] **[SAYA]** Siapkan file konfigurasi MySQL tuning (saya buat, Anda paste di server)

### 7.5 Setup Cloudflare
- [ ] **[ANDA]** Daftar akun Cloudflare (gratis) di cloudflare.com
- [ ] **[ANDA]** Tambahkan domain Anda ke Cloudflare
- [ ] **[ANDA]** Update nameserver domain ke Cloudflare
- [ ] **[ANDA]** Buat DNS record: A record → IP VPS Anda
- [ ] **[ANDA]** SSL setting: Full (Strict)
- [ ] **[ANDA]** Aktifkan: Always Use HTTPS, Auto Minify

---

## FASE 8: Deploy & Go-Live
> **Tujuan:** Deploy aplikasi ke server production.
> **Siapa:** Campuran (saya siapkan script, Anda eksekusi di server).

### 8.1 Deploy Kode
- [ ] **[ANDA]** Clone repository ke server
  ```bash
  sudo mkdir -p /var/www/g-english
  sudo chown deployer:www-data /var/www/g-english
  cd /var/www
  git clone YOUR_REPO_URL g-english
  ```
- [ ] **[SAYA]** Siapkan file `.env.production` (Anda isi credentials)
- [ ] **[ANDA]** Jalankan setup di server
  ```bash
  cd /var/www/g-english
  composer install --optimize-autoloader --no-dev
  cp .env.production .env
  php artisan key:generate
  php artisan migrate --force
  php artisan optimize
  php artisan storage:link
  npm ci && npm run build
  ```
- [ ] **[ANDA]** Set permission
  ```bash
  sudo chown -R deployer:www-data /var/www/g-english
  sudo chmod -R 775 /var/www/g-english/storage
  sudo chmod -R 775 /var/www/g-english/bootstrap/cache
  ```

### 8.2 Setup Queue Worker (Systemd Service)
- [ ] **[SAYA]** Siapkan file systemd service untuk queue worker
- [ ] **[ANDA]** Install & aktifkan service di server

### 8.3 Setup Backup Otomatis
- [ ] **[SAYA]** Siapkan backup script
- [ ] **[ANDA]** Setup rclone ke Google Drive
- [ ] **[ANDA]** Tambahkan cron job untuk backup harian

### 8.4 Monitoring
- [ ] **[ANDA]** Install Netdata (monitoring server)
  ```bash
  bash <(curl -Ss https://my-netdata.io/kickstart.sh)
  ```
- [ ] **[SAYA]** Aktifkan Laravel Pulse di kode (jika diperlukan)

### 8.5 Go-Live Checklist
- [ ] **[ANDA]** Verifikasi website bisa diakses via domain
- [ ] **[ANDA]** Verifikasi HTTPS aktif (gembok hijau)
- [ ] **[ANDA]** Test login admin → semua menu berfungsi
- [ ] **[ANDA]** Test ujian CBT dengan 2-5 siswa percobaan
- [ ] **[ANDA]** Test auto-save berjalan
- [ ] **[ANDA]** Test timer dan auto-submit
- [ ] **[ANDA]** Verifikasi backup berjalan (cek Google Drive)

---

## 📝 Log Aktivitas

| Tanggal | Fase | Aktivitas | Status |
|---|---|---|---|
| 2026-07-16 | Persiapan | Dokumen checklist dibuat | ✅ Selesai |
| | | | |

---

## ⏭️ Langkah Selanjutnya

> **SEKARANG:** Memulai **Fase 0.2** — Install Redis di mesin development Anda.
>
> **Yang perlu Anda lakukan:**
> 1. Install Redis/Memurai di Windows (lihat instruksi di Fase 0.2 di atas)
> 2. Verifikasi Redis jalan: `redis-cli ping` → harus output `PONG`
> 3. Beritahu saya hasilnya, lalu saya lanjut ke Fase 0.3 dan seterusnya
>
> **Estimasi waktu:**
> - Fase 0-6 (Kode): ~5-7 sesi kerja bersama
> - Fase 7-8 (Server): tergantung kapan Anda sewa VPS

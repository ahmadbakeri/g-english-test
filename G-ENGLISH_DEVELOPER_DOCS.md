# Panduan Pengembang G-English (G-English Developer Docs)

Dokumen ini berisi panduan teknis dan standar arsitektur pengembangan sistem manajemen G-English. Struktur ini dirancang untuk memudahkan skalabilitas dan pemeliharaan jangka panjang.

---

## 1. Arsitektur Folder Filament (Domain-Driven Structure)

Seluruh *Resources* Filament (CRUD) kini dikelompokkan berdasarkan **Domain / Bidang Bisnis** untuk menghindari penumpukan *file* di satu direktori utama.

Semua *Resources* berada di dalam `app/Filament/Resources/` dengan sub-folder berikut:

### 📁 Academic (Akademik & Kurikulum)
Menangani entitas yang berkaitan dengan sistem belajar-mengajar.
- `AcademicYearResource`: Tahun Ajaran (misal: 2023/2024 Genap).
- `SemesterResource`: Semester aktif.
- `SubjectResource`: Mata pelajaran.
- `ClassRoomResource`: Data kelas fisik/virtual.
- `ClassScheduleResource`: Jadwal rutin kelas (dipecah menjadi Pages, Schemas, dan Tables untuk modul yang kompleks).

### 📁 HR (Sumber Daya Manusia & Pengguna)
Menangani data personalia dan pengguna sistem.
- `UserResource`: Administrator dan Staf.
- `TutorResource`: Data Pengajar/Tutor.
- `StudentResource`: Data Siswa aktif.
- `AttendanceResource`: Rekap presensi.
- `EvaluationResource`: Penilaian kinerja/evaluasi.

### 📁 Finance (Keuangan)
Menangani sirkulasi uang masuk dan keluar.
- `StudentPaymentResource`: Pembayaran SPP/Pendaftaran Siswa.
- `TutorSalaryResource`: Penggajian Tutor.
- `HonorResource`: Honorarium khusus.
- `ExpenseResource`: Pengeluaran operasional.

### 📁 Operations (Operasional)
Menangani kegiatan sehari-hari yang bersifat transaksional.
- `AdmissionResource`: Pendaftaran calon siswa baru.
- `JournalResource`: Jurnal harian mengajar.
- `JournalValidationResource`: Validasi jurnal oleh admin.
- `ScheduleResource`: Jadwal spesifik pertemuan.

### 📁 Settings (Pengaturan)
- `SettingResource`: Konfigurasi global sistem.

> **⚠️ PENTING SAAT MEMBUAT RESOURCE BARU**
> Jika Anda membuat *resource* baru menggunakan perintah artisan (misal: `php artisan make:filament-resource Course`), pindahkan folder dan file tersebut secara manual ke sub-folder domain yang sesuai (misal: `Academic/`), lalu **jangan lupa ubah deklarasi `namespace`** di setiap file PHP-nya agar sesuai dengan path baru (contoh: `namespace App\Filament\Resources\Academic;`).

---

## 2. Alur Pendaftaran Siswa (Livewire)

Formulir Pendaftaran Calon Siswa (PPDB) yang diakses oleh publik tidak menggunakan antarmuka Filament, melainkan dibangun murni menggunakan **Livewire v3**.

*   **Komponen:** `app/Livewire/AdmissionForm.php`
*   **Tampilan (View):** `resources/views/livewire/admission-form.blade.php`

**Cara Kerja:**
1. Form ini menggunakan `wire:model` untuk mengikat data *input* secara *real-time*.
2. Memiliki fungsi validasi (`$rules`) yang terintegrasi.
3. Saat berhasil mendaftar (fungsi `submit()`), status form akan diubah menjadi `pending` dan data akan masuk ke `AdmissionResource` di panel Admin untuk ditinjau.

---

## 3. Penanganan Unggah & Potong Foto (Image Cropping)

Aplikasi ini menggunakan komponen bawaan `FileUpload` milik Filament yang berbasis FilePond. Kita telah melakukan konfigurasi khusus untuk modul **Tutor** dan **Student** agar foto profil dapat dipotong (di-*crop*) otomatis tanpa memerlukan jendela editor terpisah.

**Konfigurasi Kode (Contoh di `StudentResource.php`):**
```php
Forms\Components\FileUpload::make('photo')
    ->label(__('Photo'))
    ->avatar()                      // Menjadikan tampilan unggahan berbentuk lingkaran
    ->directory('students')
    ->imageEditor()                 // Mengaktifkan fitur Editor Bawaan Filament
    ->imageEditorAspectRatios(['1:1']) // Membatasi crop harus persegi
    ->imageEditorMode(2)            // 2 = Membuka otomatis layar editor saat file dipilih!
    ->automaticallyOpenImageEditorForAspectRatio() 
    ->disk('public')                // WAJIB: Agar URL dapat diakses publik
```

**Konfigurasi Tabel (`ImageColumn`):**
```php
Tables\Columns\ImageColumn::make('photo')
    ->label(__('Photo'))
    ->circular()
    ->disk('public') // WAJIB disetel ke public jika FileUpload menggunakan disk public
```

---

## 4. Tips Perbaikan Masalah (Troubleshooting)

1. **Gambar Pecah (Broken Image) di Panel Admin**
   *   **Penyebab:** Konfigurasi `.env` untuk `APP_URL` tidak sesuai dengan alamat server lokal (misal: port tidak tertulis).
   *   **Solusi:** Pastikan `APP_URL=http://127.0.0.1:8000` (atau sesuai port Anda) di `.env`. Setelah diubah, matikan dan nyalakan ulang server `php artisan serve`.
   *   Pastikan juga Anda sudah pernah menjalankan `php artisan storage:link`.

2. **Class Not Found Setelah Memindahkan File**
   *   **Penyebab:** *Namespace* di dalam file PHP belum disesuaikan dengan folder barunya.
   *   **Solusi:** Periksa baris paling atas di file tersebut (misal `namespace App\Filament\Resources\HR;`). Setelah itu jalankan `composer dump-autoload` di terminal.

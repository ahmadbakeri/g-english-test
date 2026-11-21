# ?? Cloudflare R2 Integration Checklist

Dokumen ini berisi langkah-langkah untuk menyiapkan dan mengaktifkan **Cloudflare R2** sebagai media penyimpanan cloud gratis untuk aplikasi G-English.

## 1. Persiapan Akun Cloudflare R2
- [ ] Buka [Cloudflare Dashboard](https://dash.cloudflare.com/) dan login/daftar akun baru.
- [ ] Buka menu **R2 Object Storage** di sidebar kiri. (Catatan: Anda perlu menambahkan kartu kredit/debit untuk verifikasi anti-spam, namun 10GB pertama dan bandwidth/egress 100% **gratis**).
- [ ] Klik **Create bucket**. Beri nama bucket (misalnya: `g-english-storage`).
- [ ] Pilih **Automatic** untuk lokasi.

## 2. Membuat API Token (Credentials)
- [ ] Di menu R2, klik **Manage R2 API Tokens** di sebelah kanan atas.
- [ ] Klik **Create API token**.
- [ ] Beri nama token (misal: `g-english-app`).
- [ ] Pada bagian Permissions, pilih **Object Read & Write**.
- [ ] Klik **Create API Token**.
- [ ] **PENTING:** Salin nilai `Access Key ID` dan `Secret Access Key`. Kunci ini hanya akan ditampilkan satu kali.

## 3. Konfigurasi Domain Publik (Opsional tapi Direkomendasikan)
- [ ] Masuk ke detail bucket yang baru dibuat (`g-english-storage`).
- [ ] Klik tab **Settings**.
- [ ] Di bagian **Public Access**, klik **Allow Access** pada kotak *R2.dev subdomain*. Ini akan mengizinkan file gambar/dokumen untuk diakses secara publik di internet.
- [ ] Salin URL *Public URL* tersebut (Contoh: `https://pub-xxxxxx.r2.dev`).

## 4. Konfigurasi di Aplikasi G-English (File `.env`)
Setelah mendapatkan data-data di atas, buka file `.env` di *server/hosting* Anda, dan isi variabel berikut:

```env
FILESYSTEM_DISK=r2

CLOUDFLARE_R2_ACCESS_KEY_ID=masukkan_access_key_disini
CLOUDFLARE_R2_SECRET_ACCESS_KEY=masukkan_secret_key_disini
CLOUDFLARE_R2_BUCKET=g-english-storage
CLOUDFLARE_R2_ENDPOINT=https://<ACCOUNT_ID>.r2.cloudflarestorage.com
CLOUDFLARE_R2_URL=https://pub-xxxxxx.r2.dev
```

*(Catatan: `<ACCOUNT_ID>` dapat ditemukan di halaman R2 Dashboard di sebelah kanan).*

## 5. Selesai! ??
Setelah langkah di atas dilakukan, semua unggahan gambar (avatar, materi jurnal, bukti pembayaran) dari portal Admin, Tutor, maupun Siswa akan otomatis masuk ke dalam penyimpanan *cloud* Cloudflare R2 secara gratis tanpa membebani penyimpanan server Anda.

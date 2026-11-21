# Pedoman Desain (Design Guidelines) - G-English [Detailed Version]

Pedoman ini adalah fondasi rancangan visual (UI) dan pengalaman pengguna (UX) untuk seluruh tim pengembang G-English. Karena kita menggunakan arsitektur antarmuka hibrida (gabungan komponen bawaan *Filament PHP* dan *Custom Blade Views*), kepatuhan terhadap dokumen ini mutlak diperlukan.

## 1. Identitas Visual (Brand Identity) & Palet Warna

Sistem warna diatur di tingkat *Panel Provider* Filament agar konsisten. Warna-warna ini akan dirender menjadi CSS Variables secara otomatis oleh sistem, contoh: `--primary-500`.

### Warna Inti (Core Colors)
- **Primary (Utama):** Biru G-English / Navy Blue (`#1a368b`). Memberikan kesan profesional, edukatif, elegan, dan terpercaya. Digunakan untuk: Header Kwitansi PDF, Header Utama Aplikasi, Tombol Aksi Primer, dan Indikator Navigasi Aktif.
- **Secondary (Sekunder):** Kuning Emas (`#fbb900`). Memberikan kesan energik dan menonjol. Digunakan untuk: *Badge*, Ikon peringatan (*Warning*), Sorotan nilai (CBT Score), dan Aksen estetis.

### Warna Semantik (Semantic Colors)
Sistem menggunakan nomenklatur standar untuk kondisi spesifik:
- **Success (Keberhasilan):** *Emerald* (`#10b981`). Untuk status: Ujian Selesai, SPP Lunas, Presensi Berhasil.
- **Danger (Kritis / Bahaya):** *Rose* (`#f43f5e`). Untuk status: Tombol Hapus, Nilai Merah/Remedial, Ujian Gagal, Sisa Saldo Belum Lunas.
- **Info (Informasi):** *Sky* (`#0ea5e9`). Untuk pop-up notifikasi umum atau status *Pending*.
- **Neutral (Abu-abu / Netral):** *Slate* (`#64748b` hingga `#f3f4f6`). Digunakan untuk teks paragraf, *background* tajuk tabel PDF, garis pinggir (*borders*), dan *background* kartu.

## 2. Tipografi (Typography) & Skala Hierarki

Seluruh sistem wajib menggunakan Font **`Outfit`** untuk Panel Web. Untuk Dokumen Cetak (Kwitansi), gunakan sans-serif murni (seperti Arial/Helvetica) agar ukuran *file* ringan dan kompatibel penuh dengan *engine* rendering.

### Skala Hierarki Teks (Text Scale) - Panel UI
Jika menggunakan *Custom Inline CSS*, gunakan nilai `rem` ini:
- **Display / Page Title (H1):** `1.875rem` / `font-weight: 800;`
- **Section Heading (H2):** `1.5rem` / `font-weight: 700;`
- **Sub-heading / Card Title (H3):** `1.25rem` / `font-weight: 600;`
- **Body Text (Normal):** `1rem` / `font-weight: 400;`
- **Small Print (Caption/Notes):** `0.875rem` / `color: #6b7280;`

## 3. Standarisasi Pembuatan Dokumen Cetak (DomPDF Guidelines)

Kwitansi dan sertifikat G-English harus 100% didukung oleh DomPDF (PHP PDF Generator). Oleh karena itu, hukum ketat berikut harus dipatuhi:

### 3.1 Gunakan *Table-based Layout*
**DILARANG** menggunakan CSS modern seperti *Flexbox* (`display: flex;`) atau CSS Grid dalam *file* `.blade.php` untuk ekspor dokumen (seperti `reports/student-payment.blade.php`).
**WAJIB** menggunakan `<table>` tradisional dengan atribut kolom/persentase kuno (`<table width="100%">`, `<td style="width: 50%">`) agar jarak dan kolom dapat dirender dengan sejajar sempurna (*pixel-perfect*).

### 3.2 Isolasi Background & Box-Shadow
**DILARANG** menggunakan *CSS Box-Shadow* secara langsung berdampingan dengan warna latar gelap atau transparan PNG jika hal itu memunculkan gangguan garis hitam. 
**WAJIB** membungkus logo berwarna-gelap dalam kotak *background* putih padat (`background-color: #ffffff; padding: 10px; border-radius: 8px;`) apabila diletakkan di atas bidang *header* berwarna gelap, agar kontras dan terbaca.

## 4. Aturan Krusial Pembuatan UI Kustom (The "Purge" Problem)

### Latar Belakang Masalah
Sistem *Filament* mengkompilasi file CSS-nya melalui *Tailwind JIT Compiler*. Kelas Tailwind yang dimasukkan dalam string PHP (misal di metode `->getHeading()`) **tidak dibaca oleh Purger**, sehingga tampilannya hancur saat di-deploy (Production).

### Solusi Permanen: Aturan *Strict Inline Styling*
Ketika menyuntikkan HTML kustom dari *backend* PHP (`HtmlString`), Anda **WAJIB menggunakan Inline Style murni**.
**✅ Contoh Wajib (Aman dari Purge):**
```html
<button style="display: flex; align-items: center; width: 44px; height: 44px; border-radius: 9999px; border: 1px solid #e5e7eb; color: #6b7280;">
    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">...</svg>
</button>
```

## 5. Aksesibilitas (Accessibility) & Ikonografi
- **Ikon Utama:** Gunakan SVG **Heroicons**.
- **Indikator Interaksi:** Selalu berikan `cursor: pointer;` pada elemen yang dapat diklik.
- **Batas Sentuh (*Touch Target*):** Setiap tombol aksi primer/sekunder **minimal 44x44 pixel** untuk UX ponsel (*mobile friendly*).

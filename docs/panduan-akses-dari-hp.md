# 🌐 Panduan Akses Aplikasi dari HP (Cloudflare Tunnel)

Panduan ini menjelaskan cara mengakses aplikasi G-English yang berjalan di **laptop** melalui **HP** atau perangkat lain menggunakan **Cloudflare Tunnel (cloudflared)** — gratis, tanpa domain, otomatis HTTPS.

---

## Prasyarat

- `cloudflared` sudah terinstal *(sudah diinstal di laptop ini)*
- `php`, `composer`, dan `MySQL` sudah berjalan

---

## Cara Menjalankan

### Langkah 1: Buka Terminal / PowerShell

Buka **2 terminal** terpisah (atau 2 tab di Windows Terminal).

---

### Langkah 2: Jalankan Server Laravel (Terminal 1)

```powershell
cd C:\Users\Ahmad Bakeri\.gemini\antigravity-ide\scratch\g-english
php artisan serve
```

Pastikan muncul:
```
INFO  Server running on [http://127.0.0.1:8000].
```

---

### Langkah 3: Jalankan Cloudflare Tunnel (Terminal 2)

```powershell
cloudflared tunnel --url http://127.0.0.1:8000
```

Tunggu beberapa detik, akan muncul output seperti:
```
INF |  https://xxxxx-xxxxx-xxxxx-xxxxx.trycloudflare.com  |
```

**Salin URL tersebut** — itulah alamat publik Anda!

---

### Langkah 4: Buka di HP 📱

Buka browser di HP, ketik URL yang muncul di atas, contoh:
```
https://state-logos-issues-auckland.trycloudflare.com
```

Selesai! Anda sudah bisa mengakses aplikasi dari HP! 🎉

---

## Catatan Penting

| Item | Keterangan |
|---|---|
| **Biaya** | 100% Gratis, tanpa akun Cloudflare |
| **HTTPS** | Otomatis aktif (aman) |
| **URL berubah** | Setiap kali menjalankan ulang `cloudflared`, URL-nya akan berubah (random) |
| **Durasi** | Tunnel aktif selama kedua terminal masih berjalan |
| **Jangkauan** | Bisa diakses dari mana saja (beda WiFi pun bisa) |

---

## Menghentikan

- **Hentikan Tunnel:** Tekan `Ctrl + C` di Terminal 2 (cloudflared)
- **Hentikan Server:** Tekan `Ctrl + C` di Terminal 1 (artisan serve)

---

## Troubleshooting

### `cloudflared` tidak ditemukan
Jika muncul error *"cloudflared is not recognized"*, jalankan:
```powershell
winget install Cloudflare.cloudflared
```
Lalu **tutup dan buka ulang** terminal agar PATH ter-refresh.

### Tunnel mati sendiri
Pastikan laptop **tidak sleep/hibernate**. Jika tertutup, jalankan ulang kedua perintah di atas.

### Halaman tidak muncul di HP
1. Pastikan `php artisan serve` masih berjalan di Terminal 1
2. Pastikan `cloudflared tunnel` masih berjalan di Terminal 2
3. Coba refresh halaman di HP

---

## Perintah Singkat (Copy-Paste)

```powershell
# Terminal 1 — Server Laravel
cd C:\Users\Ahmad Bakeri\.gemini\antigravity-ide\scratch\g-english
php artisan serve

# Terminal 2 — Cloudflare Tunnel (buka terminal baru)
cloudflared tunnel --url http://127.0.0.1:8000
```

> 💡 **Tips:** Anda juga bisa mengirim link tunnel ke teman atau klien agar mereka bisa melihat demo aplikasi dari perangkat mereka sendiri!

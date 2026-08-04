# NGuliner Magelang

> Referensi Kuliner No.1 di Magelang  
> Support Resto & UMKM Lokal

---

## Tentang

**NGuliner Magelang** adalah platform digital yang menyajikan rekomendasi kuliner terbaik di Magelang dan sekitarnya.  
Berfokus pada makanan legendaris, street food, hingga resto modern yang layak dicoba.

Kami terbuka untuk kolaborasi bisnis, iklan, dan endorse.

---

## Visi

Menjadi sumber terpercaya bagi siapa saja yang ingin menjelajahi kekayaan rasa Magelang — dari yang legendaris hingga yang baru muncul.

---

## Fitur Utama

- **Rekomendasi Harian (Rotasi)**  
  Rekomendasi terpilih berganti otomatis setiap hari (rotasi deterministik berdasarkan tanggal), stabil sepanjang hari.

- **Kategori Kuliner**  
  Bakso, Es Dawet, Martabak, Nasi Goreng Magelangan, Street Food, dll.

- **Info Praktis**  
  Alamat, jam buka, kisaran harga, tips, status **Buka/Tutup** real-time, dan lokasi peta.

- **Support UMKM (Microsite)**  
  Setiap resto/UMKM dapat memiliki landing page sendiri (hero, menu, galeri, media sosial, peta, tema warna) yang dikelola dari admin.

- **Peta Lokasi**  
  Halaman peta semua kuliner (Leaflet + OpenStreetMap) + embed Google Maps di halaman detail.

- **Pencarian & Filter Lanjutan**  
  Cari berdasarkan nama/kategori, filter **buka sekarang**, **legendaris**, urutkan berdasarkan terbaru/rating/harga/view.

- **Rating & Review**  
  Pengunjung memberi bintang + ulasan; dimoderasi admin sebelum tayang.

- **View Counter**  
  Jumlah kunjungan per tempat kuliner + statistik di dashboard admin.

- **Instagram Feed**  
  Tampilan post terbaru @ngulinermagelang di halaman utama (best-effort, diambil via scraper Python).

- **Share & WhatsApp**  
  Tombol share (WhatsApp, Facebook, X, salin link) dan tombol chat WhatsApp per kuliner.

---

## Kolaborasi

Terbuka untuk:
- Iklan & Endorse
- Review resto / UMKM
- Partnership konten
- Saran tempat baru dari pengunjung

**Form kolaborasi** tersedia di halaman `/kolaborasi`; pengajuan masuk ke inbox admin dan dikonfirmasi via email/WhatsApp.  
Hubungi via Instagram: [@ngulinermagelang](https://www.instagram.com/ngulinermagelang/)

---

## Contoh Rekomendasi

### Es Dawet Pak Min
Legend sejak 2001.  
Santan gurih + gula Jawa asli.  
📍 Jl. Kalingga, Magelang (dekat Pasar Taruma Negara)  
⏰ 10.00 – habis  
💰 Mulai Rp5.000

### Bakso Pak Iwan
Hampir 20 tahun, porsi melimpah, kuah konsisten.  
📍 Jl. Kapten Yahya (depan TKIT Asy Syaffa’)  
⏰ 10.30 – 17.30 WIB

### Bakso Krikil Mas Kentung
Satu mangkok bisa sampai 60 butir.  
Harga mulai Rp8.000.  
Sejak 2016 dan selalu rame.

### Martabak Sahabat
Legend sejak tahun 2000.  
📍 Jl. Ikhlas, Magersari, Magelang Selatan  
⏰ 16.00 – 21.30 WIB  
💰 Mulai Rp18.000

---

## Desain Minimalis

**Prinsip desain:**
- Warna utama: hitam, putih, abu-abu
- Tipografi bersih (sans-serif)
- Banyak ruang kosong (whitespace)
- Fokus pada foto makanan berkualitas tinggi
- Navigasi sederhana
- Tanpa elemen yang berlebihan
- Mobile-first & loading cepat

**Struktur halaman utama:**
1. Hero (logo + tagline singkat)
2. Tentang
3. Rekomendasi Hari Ini
4. Rekomendasi Terbaru
5. Kategori
6. Instagram Feed
7. Kolaborasi (form)
8. Saran Tempat (form)
9. Footer sederhana

---

## Halaman Publik

| Halaman | URL | Keterangan |
|---|---|---|
| Beranda | `/` | Hero, tentang, rekomendasi, kategori, IG feed, kolaborasi, saran |
| Kategori | `/kategori/{slug}` | Daftar kuliner per kategori |
| Detail Kuliner | `/kuliner/{slug}` | Info praktis, buka/tutup, rating, review, maps, share, WA |
| Peta Lokasi | `/peta` | Semua lokasi kuliner di peta |
| Microsite UMKM | `/{slug}` | Landing page per resto/UMKM, tema warna kustom |
| Halaman CMS | `/halaman/{slug}` | Konten dinamis dari admin (Tentang, Kerja Sama, dll) |
| Pencarian | `/cari` | Cari + filter lanjutan + pagination |
| Kolaborasi | `/kolaborasi` | Form kolaborasi bisnis |
| Saran Tempat | `/saran` | Form saran tambah tempat dari pengunjung |

---

## Admin Panel (`/admin`)

Hanya diakses dengan login (role: superadmin / editor).

| Modul | Fitur |
|---|---|
| Dashboard | Statistik kategori, total views, rating rata-rata, kuliner terpopuler, antrian pending |
| Kelola Kuliner | CRUD, upload foto, toggle featured/legendary/publish, edit jam buka & koordinat |
| Kelola Kategori | CRUD kategori |
| Halaman CMS | CRUD + editor blok section (heading, text, image, list, cta, quote, embed) |
| Editor Microsite | Hero, about, menu, galeri, sosial, maps, warna tema, toggle aktif |
| Inbox Kolaborasi | Lihat, ubah status (new/contacted/done), hapus |
| Moderasi Review | Approve / reject / hapus ulasan |
| Inbox Saran Tempat | Review, konversi menjadi data kuliner |
| Feed Instagram | Import & kelola post feed |
| Manajemen User | Kelola admin (superadmin only) |
| Audit Log | Riwayat aktivitas admin |
| Backup | Jalankan backup database, download, hapus |

---

## Python Web Scraper

`python/scraper/` — skrip Python untuk pengumpulan data kuliner:

| Skrip | Fungsi |
|---|---|
| `scraper.py` | Scrape data kuliner dari web publik (nama, kategori, alamat, jam, harga, deskripsi, koordinat, WhatsApp) → `data/kuliner.json` |
| `instagram_feed.py` | Ambil post publik @ngulinermagelang → `data/instagram.json` (best-effort, sering dibatasi Instagram) |

Impor ke database via artisan:
```
php artisan places:import data/kuliner.json
php artisan feed:import data/instagram.json
```

---

## Notifikasi Email

Admin menerima email otomatis saat:
- Kolaborasi baru masuk
- Review baru masuk
- Saran tempat baru masuk

Development memakai `MAIL_MAILER=log`; production via SMTP (panduan Mailpit ada di README).

---

## SEO

- `sitemap.xml` + `robots.txt`
- JSON-LD schema.org: `ItemList` (beranda/kategori), `LocalBusiness` (detail/microsite)
- Meta description, Open Graph, canonical
- Slug URL bersih
- Lazy loading gambar
- Kata kunci fokus: kuliner Magelang, rekomendasi makan Magelang

---

## Catatan Teknis

- **Framework**: Laravel 13 (PHP 8.4)
- **Database**: SQLite (tanpa setup server)
- **Frontend**: Blade + Tailwind CSS (Vite), mobile-first
- **Auth admin**: Laravel Breeze (session), role superadmin/editor
- **Maps**: Leaflet + OpenStreetMap (publik), Google Maps embed (detail)
- **Backup**: `php artisan app:backup` (rotasi 14 hari) + jadwal harian otomatis
- **Deploy**: `deploy.sh` + contoh config Nginx + `.env.production.example` (dokumentasi di README)
- **Integrasi Instagram feed**: opsional, best-effort

---

© NGuliner Magelang  
Support Resto & UMKM Lokal

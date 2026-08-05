# NGuliner Magelang

> Referensi Kuliner No.1 di Magelang — Support Resto & UMKM Lokal

Platform web (Laravel 13 + SQLite) sesuai PRD untuk rekomendasi kuliner Magelang:
rekomendasi harian, kategori, microsite UMKM, CMS halaman, kolaborasi, review,
peta lokasi, feed Instagram, dan panel admin lengkap. Lengkapnya lihat [PRD.md](PRD.md).

## Fitur

- **Publik**: beranda (rekomendasi harian berotasi), kategori, detail kuliner (buka/tutup real-time, rating, review, peta, share WA), peta semua lokasi (Leaflet/OSM), pencarian + filter lanjutan, form kolaborasi & saran tempat, microsite UMKM bertema warna, halaman CMS.
- **Admin** (`/admin`): dashboard statistik, CRUD kuliner/kategori/halaman/microsite, inbox kolaborasi, moderasi review, saran tempat, feed Instagram, manajemen pengguna (superadmin/editor), audit log, backup database.
- **Python scraper**: `python/scraper/` untuk ambil data kuliner dari web & feed Instagram → JSON → import via artisan.
- **SEO**: sitemap.xml, robots.txt, JSON-LD, Open Graph, slug bersih.
- **Notifikasi email** ke admin saat kolaborasi/review/saran masuk.

## Persyaratan

- PHP 8.3+ (disarankan 8.4), Composer, Node 20+ (untuk asset)
- Tidak butuh server database (SQLite)

## Instalasi (lokal)

```bash
composer install
copy .env.example .env            # Windows  /  cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Buka http://127.0.0.1:8000

### Akun default (dari seeder)

| Email | Password | Role |
|---|---|---|
| admin@nguliner.test | `password` | superadmin |
| editor@nguliner.test | `password` | editor |

> Ubah password segera setelah produksi.

## Halaman Utama

| URL | Keterangan |
|---|---|
| `/` | Beranda |
| `/kategori/{slug}` | Daftar per kategori |
| `/kuliner/{slug}` | Detail kuliner |
| `/peta` | Peta semua lokasi |
| `/cari` | Pencarian + filter (buka sekarang, legendaris, sort) |
| `/kolaborasi` | Form kolaborasi bisnis |
| `/saran` | Saran tempat baru |
| `/halaman/{slug}` | Halaman CMS |
| `/{slug}` | Microsite UMKM (harus diaktifkan admin) |
| `/sitemap.xml` `/robots.txt` | SEO |

## Panel Admin (`/admin`)

Dashboard · Kuliner · Kategori · Halaman CMS · Microsite · Kolaborasi ·
Review · Saran Tempat · Feed Instagram · Backup · Audit Log · Pengguna (superadmin).

## Email Development

Default `MAIL_MAILER=log` — email tersimpan di `storage/logs/laravel.log`.
Untuk preview: jalankan [Mailpit](https://mailpit.axllent.org/) dan ubah `.env`:

```
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

## Python Scraper

```bash
cd python/scraper
python -m pip install -r requirements.txt

python scraper.py scrape --query "bakso magelang" --max 20 --output data/kuliner.json
php artisan places:import data/kuliner.json

python instagram_feed.py --username ngulinermagelang --max 12 --output data/instagram.json
php artisan feed:import data/instagram.json
```

Detail: [python/scraper/README.md](python/scraper/README.md)

## Backup

```bash
php artisan app:backup          # manual — simpan di storage/app/backups (rotasi 14)
```

Otomatis tiap pukul 03.00 — daftarkan scheduler di cron/Windows Task Scheduler:

```bash
* * * * * php /path/ke/nguliner/artisan schedule:run >> /dev/null 2>&1
```

## Deployment (production)

1. Salin `.env.production.example` → `.env`, isi `APP_KEY` (via `php artisan key:generate`) dan `APP_URL`.
2. Jalankan `bash deploy.sh` (install composer, build asset, migrate, cache).
3. Config Nginx contoh: `deploy/nginx.conf` (root ke `public/`).
4. Daftarkan scheduler & queue worker (`QUEUE_CONNECTION=database`, jalankan `php artisan queue:work` untuk email).
5. Ganti password admin default.

## Keamanan Native (C/C++ + FFI)

Layer keamanan berbasis library native `native/ngsecurity.dll` (C++17) yang
dipanggil PHP via FFI — lihat `native/README.md`:

- **Anti session hijacking** (`ng-hardening` middleware di grup `/admin`):
  sesi diikat ke sidik jari perangkat (UA + Accept-Language, opsional IP)
  via HMAC-SHA256 native, dicek dengan perbandingan **constant-time**.
  Perangkat berbeda → sesi di-invalidate + redirect ke login.
- **CSPRNG** terpisah (BCryptGenRandom) dan primitif SHA-256/HMAC mandiri.
- Cek status: `php artisan security:ffi-status [--bench]`.
- Jika FFI/DLL tidak ada, aplikasi tetap jalan (fallback otomatis ke
  `hash_hmac`/`hash_equals`/`random_bytes`), hanya log warning.

Aktifkan di `php.ini`: `extension=ffi` dan `ffi.enable=true`.

## Struktur Penting

```
app/
├── Console/Commands/      # places:import, feed:import, app:backup
├── Http/Controllers/      # publik + Admin/
├── Http/Middleware/       # EnsureUserIsAdmin, EnsureUserIsSuperAdmin
├── Mail/                  # NewSubmissionMail
├── Models/                # Place, Category, Microsite, Page, ...
└── Services/              # DailyPickService, AuditService, NotificationService
database/seeders/          # user, kategori, tempat, halaman, feed
python/scraper/            # scraper.py + instagram_feed.py
data/                      # contoh JSON hasil scraper
deploy/                    # nginx.conf
```

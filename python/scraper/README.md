# NGuliner Scraper (Python)

Skrip pengumpulan data kuliner untuk platform NGuliner Magelang.

## Setup

```bash
cd python/scraper
python -m pip install -r requirements.txt
```

## Scrape data kuliner dari web publik

```bash
python scraper.py scrape --query "bakso magelang" --source pergi-kuliner --max 20 --output data/kuliner.json
python scraper.py scrape --query "es dawet magelang" --max 10
```

Hasil tersimpan sebagai JSON sesuai skema tabel `places`, siap diimpor:

```bash
php artisan places:import data/kuliner.json
```

Impor bersifat upsert (update jika slug sudah ada, tambah jika belum).

## Normalisasi file JSON mentah

```bash
python scraper.py normalize --file hasil-raw.json --output data/kuliner.json
```

## Feed Instagram (best-effort)

```bash
python instagram_feed.py --username ngulinermagelang --max 12 --output data/instagram.json
php artisan feed:import data/instagram.json
```

> **Catatan:** Instagram sering memblokir akses publik dan mengubah struktur
> halaman. Jika skrip gagal, gunakan Meta Graph API resmi (izin
> `instagram_basic`) atau impor JSON secara manual lewat menu Admin → Feed.
> Contoh data `data/instagram.json` bisa diimpor kapan saja untuk demo.

> **Catatan (pergikuliner):** situs ini memakai anti-bot (mengalihkan bot ke
> halaman `lander` tanpa data). `scraper scrape` akan melaporkan "Tidak ada
> data" bila diblokir — itu perilaku normal. Solusi: ganti `--source` ke situs
> tanpa proteksi, gunakan data manual di `data/kuliner.json`, atau sumber API
> resmi (mis. Places API Google).

## Struktur output `kuliner.json`

| Field | Keterangan |
|---|---|
| `name` | Nama tempat (wajib) |
| `category` | Kategori (otomatis ditebak dari nama bila kosong) |
| `address` | Alamat |
| `latitude` / `longitude` | Koordinat (untuk peta) |
| `whatsapp` | Nomor WA `62...` |
| `open_days` | Hari buka `Mon,Tue,...` |
| `open_time` / `close_time` | Jam buka/tutup `HH:MM` (kosong = habis) |
| `price_range` | Kisaran harga |
| `since_year` | Tahun berdiri |
| `is_legendary` / `is_featured` | Penanda |
| `image` | URL gambar (relatif `/img/...` atau absolut) |

# ngsecurity — Native Security Layer (C/C++)

Library native (C++17) untuk lapisan keamanan NGuliner, dipanggil dari PHP
melalui FFI. Tidak bergantung pada OpenSSL — SHA-256/HMAC diimplementasikan
mandiri, CSPRNG memakai BCryptGenRandom (Windows) / getrandom (Linux).

## Fitur

| Fungsi | Kegunaan |
|---|---|
| `ng_hmac_sha256_hex` | Signing & token binding (sidik jari sesi) |
| `ng_sha256_hex` | Hash cepat tanpa depdendensi eksternal |
| `ng_constant_time_eq` | Perbandingan constant-time — anti **timing attack** |
| `ng_secure_bytes` | Random kriptografis dari OS (BCryptGenRandom) |
| `ng_version` | Cek status library |

## Kompilasi

Windows (MSVC / Visual Studio 2022):

```
native\compile.bat
```

Linux/macOS (g++):

```
chmod +x native/compile.sh && ./native/compile.sh
```

## Aktifkan FFI di PHP (php.ini)

```ini
extension=ffi
ffi.enable=true
```

Cek status:

```
php artisan security:ffi-status
php artisan security:ffi-status --bench
```

## Integrasi Laravel

1. **`App\Security\NativeGuard`** — wrapper FFI. Semua method otomatis
   *fallback* ke PHP (`hash_hmac`, `random_bytes`, `hash_equals`) jika FFI
   atau DLL tidak tersedia, jadi aplikasi tidak pernah rusak.
2. **`ng-hardening` middleware** (di grup route `admin`) — anti
   **session hijacking**: sesi admin diikat ke sidik jari perangkat
   (User-Agent + Accept-Language, opsional IP) via HMAC-SHA256 native,
   diverifikasi dengan perbandingan constant-time. Perangkat berbeda →
   sesi di-invalidate dan diredirect ke login (dilog di
   `storage/logs/laravel.log` dengan prefix `ng-hardening:`).
3. **Config** (`config/security.php`):
   - `NGSECURITY_DLL` — path ke library
   - `NGSECURITY_SESSION_BINDING` — aktifkan binding (default `true`)
   - `NGSECURITY_BIND_IP` — ikutkan IP dalam sidik jari (default `false`;
     nyalakan hanya jika client punya IP statis)

## Catatan kinerja

Pada benchmark lokal, HMAC-SHA256 native (C++) ~2,4 µs/op vs `hash_hmac`
PHP ~1 µs/op — PHP intrinsik lebih cepat untuk hash murni karena tanpa
overhead FFI. Nilai native terletak pada primitif yang tidak dimiliki PHP:
perbandingan constant-time bawaan, CSPRNG terpisah dari PHP, dan layer
independen yang bisa diverifikasi/tested sendiri.

<?php

namespace App\Security;

use FFI;
use RuntimeException;

/**
 * NativeGuard — wrapper FFI ke native/ngsecurity.dll (C++17).
 *
 * Menyediakan primitif keamanan native: HMAC-SHA256, SHA-256, random
 * kriptografis (BCryptGenRandom) dan perbandingan constant-time.
 *
 * Jika FFI tidak tersedia / DLL tidak ada, semua method otomatis fallback
 * ke implementasi PHP (hash_hmac, random_bytes, hash_equals) sehingga
 * aplikasi tetap berjalan; isNative() menunjukkan mode aktif.
 */
class NativeGuard
{
    private ?FFI $ffi = null;

    private ?string $error = null;

    private bool $tried = false;

    private ?string $libraryPath;

    public function __construct(?string $libraryPath = null)
    {
        $this->libraryPath = $libraryPath;
    }

    private function load(): void
    {
        $this->tried = true;

        if (! class_exists(FFI::class)) {
            $this->error = 'Ekstensi FFI tidak tersedia (aktifkan extension=ffi di php.ini)';

            return;
        }

        $path = $this->libraryPath ?: (string) config('security.dll');

        if (! is_file($path)) {
            $this->error = "Library native tidak ditemukan: {$path}";

            return;
        }

        try {
            $this->ffi = FFI::cdef(<<<'CDEF'
typedef unsigned char uint8_t;
typedef unsigned long long size_t;

const char *ng_version(void);

int ng_sha256_hex(const char *msg, size_t msg_len, char *out, size_t cap);

int ng_hmac_sha256_hex(const char *key, size_t key_len,
                       const char *msg, size_t msg_len, char *out, size_t cap);

int ng_constant_time_eq(const char *a, size_t a_len,
                        const char *b, size_t b_len);

int ng_secure_bytes(uint8_t *out, size_t n);
CDEF, $path);
        } catch (\Throwable $e) {
            $this->error = 'Gagal memuat FFI: '.$e->getMessage();
        }
    }

    /** True bila library native berhasil dimuat dan dipakai. */
    public function isNative(): bool
    {
        if (! $this->tried) {
            $this->load();
        }

        return $this->ffi !== null;
    }

    /** Pesan error (atau null) bila mode native tidak aktif. */
    public function error(): ?string
    {
        if (! $this->tried) {
            $this->load();
        }

        return $this->error;
    }

    public function version(): string
    {
        if ($this->isNative()) {
            return (string) $this->ffi->ng_version();
        }

        return 'fallback: PHP '.PHP_VERSION;
    }

    /** SHA-256 hex. */
    public function sha256Hex(string $data): string
    {
        if ($this->isNative()) {
            $out = $this->ffi->new('char[65]');

            return $this->ffi->ng_sha256_hex($data, strlen($data), $out, 65) === 0
                ? FFI::string($out)
                : hash('sha256', $data);
        }

        return hash('sha256', $data);
    }

    /** HMAC-SHA256 hex. */
    public function hmacHex(string $key, string $data): string
    {
        if ($this->isNative()) {
            $out = $this->ffi->new('char[65]');

            return $this->ffi->ng_hmac_sha256_hex($key, strlen($key), $data, strlen($data), $out, 65) === 0
                ? FFI::string($out)
                : hash_hmac('sha256', $data, $key);
        }

        return hash_hmac('sha256', $data, $key);
    }

    /**
     * Perbandingan constant-time (anti timing attack).
     * Fallback ke hash_equals bila native tidak tersedia.
     */
    public function equals(string $a, string $b): bool
    {
        if ($this->isNative()) {
            return (bool) $this->ffi->ng_constant_time_eq($a, strlen($a), $b, strlen($b));
        }

        return hash_equals($a, $b);
    }

    /** Byte acak kriptografis. */
    public function randomBytes(int $length): string
    {
        if ($length < 1) {
            throw new RuntimeException('Panjang minimal 1 byte.');
        }

        if ($this->isNative()) {
            $buf = $this->ffi->new("uint8_t[{$length}]");
            if ($this->ffi->ng_secure_bytes($buf, $length) === 0) {
                return FFI::string($buf, $length);
            }

            $this->error = 'BCryptGenRandom gagal; fallback ke random_bytes()';
        }

        return random_bytes($length);
    }

    /**
     * Sidik jari perangkat untuk session binding.
     * HMAC(APP_KEY, SHA256(ua|accept-language|ip)) — bagian IP opsional.
     */
    public function fingerprint(string $userAgent, string $acceptLanguage, ?string $ip = null): string
    {
        $parts = [$userAgent, $acceptLanguage];

        if ($ip) {
            $parts[] = $ip;
        }

        return $this->hmacHex((string) config('app.key'), $this->sha256Hex(implode('|', $parts)));
    }
}

<?php

namespace App\Console\Commands;

use App\Security\NativeGuard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SecurityFfiStatus extends Command
{
    protected $signature = 'security:ffi-status {--bench : Jalankan benchmark HMAC native vs PHP}';

    protected $description = 'Cek status layer keamanan native (C++ FFI)';

    public function handle(NativeGuard $guard): int
    {
        $this->components->info('NGuliner native security (C++/FFI)');

        $this->table(
            ['Check', 'Status', 'Detail'],
            [
                ['FFI extension', $guard->isNative() ? 'OK' : 'FAIL', $guard->error() ?? '-'],
                ['Library', $guard->isNative() ? 'loaded' : 'missing', config('security.dll')],
                ['Version', $guard->isNative() ? 'OK' : 'fallback', $guard->version()],
                ['Session binding', config('security.session_binding') ? 'enabled' : 'disabled', config('security.bind_ip') ? 'bind IP: on' : 'bind IP: off'],
            ],
        );

        // Vektor uji SHA-256 / HMAC
        $sha = $guard->sha256Hex('abc');
        $hmac = $guard->hmacHex('key', 'The quick brown fox jumps over the lazy dog');
        $expectedSha = 'ba7816bf8f01cfea414140de5dae2223b00361a396177a9cb410ff61f20015ad';
        $expectedHmac = 'f7bc83f430538424b13298e6aa6fb143ef4d59a14946175997479dbc2d1a3cd8';

        $this->newLine();
        $this->components->info('Vektor uji RFC 4231');

        $this->table(
            ['Uji', 'Hasil', 'Harapan', 'Cocok'],
            [
                ['sha256("abc")', $sha, $expectedSha, $sha === $expectedSha ? 'YES' : 'NO'],
                ['hmac(key, fox)', $hmac, $expectedHmac, $hmac === $expectedHmac ? 'YES' : 'NO'],
                ['constant-time eq', $guard->equals('abcde', 'abcde') ? '1' : '0', '1', $guard->equals('abcde', 'abcde') ? 'YES' : 'NO'],
                ['constant-time neq', $guard->equals('abcde', 'abcdf') ? '1' : '0', '0', ! $guard->equals('abcde', 'abcdf') ? 'YES' : 'NO'],
                ['random bytes', strlen($guard->randomBytes(32)).' byte', '32 byte', strlen($guard->randomBytes(32)) === 32 ? 'YES' : 'NO'],
            ],
        );

        if ($this->option('bench')) {
            $this->newLine();
            $this->components->info('Benchmark 10.000x HMAC-SHA256 (key 32B, data 64B)');

            $start = hrtime(true);
            for ($i = 0; $i < 10000; $i++) {
                $guard->hmacHex('k12345678901234567890123456789012', 'payload-'.$i);
            }
            $nativeMs = (hrtime(true) - $start) / 1e6;

            $start = hrtime(true);
            for ($i = 0; $i < 10000; $i++) {
                hash_hmac('sha256', 'payload-'.$i, 'k12345678901234567890123456789012');
            }
            $phpMs = (hrtime(true) - $start) / 1e6;

            $this->table(
                ['Mode', '10.000x HMAC', 'per-op'],
                [
                    ['native C++ ('.($guard->isNative() ? 'aktif' : 'fallback').')', number_format($nativeMs, 1).' ms', number_format($nativeMs / 10000 * 1000, 2).' µs'],
                    ['PHP hash_hmac', number_format($phpMs, 1).' ms', number_format($phpMs / 10000 * 1000, 2).' µs'],
                ],
            );
        }

        if (! $guard->isNative()) {
            $this->components->warn($guard->error());
            $this->components->warn('Periksa: (1) extension=ffi + ffi.enable=true di php.ini, (2) kompilasi via native/compile.bat, (3) '.(File::exists(config('security.dll')) ? 'DLL ada' : 'DLL tidak ada').' di '.config('security.dll'));
        }

        return $guard->isNative() ? self::SUCCESS : self::FAILURE;
    }
}

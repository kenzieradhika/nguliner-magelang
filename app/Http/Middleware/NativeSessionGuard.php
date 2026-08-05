<?php

namespace App\Http\Middleware;

use App\Security\NativeGuard;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Anti session hijacking berbasis native (C++ FFI).
 *
 * Mengikat sesi ke sidik jari perangkat (User-Agent + Accept-Language,
 * opsional IP via config security.bind_ip). Perbandingan memakai
 * ng_constant_time_eq native untuk mencegah timing attack.
 *
 * Fail-open: jika native tidak aktif, request tetap diteruskan
 * (fingerprint pakai hash_hmac PHP) dan dicatat sekali.
 */
class NativeSessionGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = app(NativeGuard::class);

        $fingerprint = $guard->fingerprint(
            (string) $request->userAgent(),
            (string) $request->header('Accept-Language'),
            config('security.bind_ip') ? $request->ip() : null,
        );

        $session = $request->session();

        if (! $session->has('ng_fp')) {
            $session->put('ng_fp', $fingerprint);
        } elseif (! $guard->equals($session->get('ng_fp'), $fingerprint)) {
            Log::warning('ng-hardening: session binding mismatch', [
                'ip' => $request->ip(),
                'ua' => $request->userAgent(),
            ]);

            $session->invalidate();
            $request->session()->regenerateToken();

            return redirect()->guest(route('login'))
                ->withErrors(['email' => 'Sesi diakhiri karena terdeteksi perangkat berbeda. Silakan masuk ulang.']);
        }

        if (! $guard->isNative()) {
            Log::warning('ng-hardening: mode fallback (FFI tidak aktif) — '.$guard->error());
        }

        return $next($request);
    }
}

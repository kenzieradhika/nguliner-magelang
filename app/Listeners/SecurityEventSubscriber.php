<?php

namespace App\Listeners;

use App\Services\SecurityEventService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;

/**
 * Merekam semua sinyal serangan ke tabel security_events:
 * login gagal (brute force), login terkunci (rate limit), CSRF mismatch,
 * dan login berhasil (pemantauan akses akun).
 */
class SecurityEventSubscriber
{
    public function __construct(private SecurityEventService $service)
    {
    }

    public function onLoginFailed(Failed $event): void
    {
        $this->service->record(
            'login_failed',
            'Percobaan login gagal untuk akun: '.($event->credentials['email'] ?? '?'),
            ['email' => $event->credentials['email'] ?? null],
            request(),
            'medium',
        );
    }

    public function onLoginLocked(Lockout $event): void
    {
        $this->service->record(
            'login_locked',
            'Akun dikunci sementara akibat terlalu banyak percobaan login (rate limit).',
            ['email' => $event->request?->input('email') ?? null],
            $event->request,
            'high',
        );
    }

    public function onLogin(Login $event): void
    {
        $this->service->record(
            'login_success',
            'Login berhasil: '.$event->user->email,
            ['user_id' => $event->user->id, 'email' => $event->user->email],
            request(),
            'info',
        );
    }

    public function subscribe(): array
    {
        return [
            Failed::class => 'onLoginFailed',
            Lockout::class => 'onLoginLocked',
            Login::class => 'onLogin',
        ];
    }
}

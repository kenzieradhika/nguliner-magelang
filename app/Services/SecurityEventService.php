<?php

namespace App\Services;

use App\Models\SecurityEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityEventService
{
    private const DEDUPE_WINDOW_MINUTES = 5;

    /**
     * Catat insiden keamanan. Insiden sejenis dari IP sama dalam 5 menit
     * digabung (count++), agar brute force tidak membanjiri database.
     */
    public function record(string $type, string $message, array $details = [], ?Request $request = null, string $severity = 'medium'): SecurityEvent
    {
        $ip = $request?->ip();
        $since = now()->subMinutes(self::DEDUPE_WINDOW_MINUTES);

        $existing = SecurityEvent::where('type', $type)
            ->whereNull('read_at')
            ->when($ip, fn ($q) => $q->where('ip', $ip))
            ->where('created_at', '>=', $since)
            ->latest()
            ->first();

        if ($existing) {
            $existing->increment('count');

            if ($request) {
                $existing->details = $details;
                $existing->save();
            }

            return $existing->refresh();
        }

        $event = SecurityEvent::create([
            'type' => $type,
            'severity' => $severity,
            'message' => $message,
            'details' => $details ?: null,
            'ip' => $ip,
            'user_agent' => substr((string) ($request?->userAgent() ?? ''), 0, 500) ?: null,
            'url' => substr((string) ($request?->fullUrl() ?? ''), 0, 500) ?: null,
            'count' => 1,
            'last_seen_at' => now(),
        ]);

        Log::warning("security-event:{$type}", [
            'severity' => $severity,
            'ip' => $ip,
            'details' => $details,
        ]);

        return $event;
    }

    public function unreadCount(): int
    {
        return (int) SecurityEvent::unread()->count();
    }

    public function unreadHighSeverityCount(): int
    {
        return (int) SecurityEvent::unread()
            ->whereIn('severity', ['high', 'critical'])
            ->count();
    }
}
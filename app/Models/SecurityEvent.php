<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SecurityEvent extends Model
{
    public const TYPES = [
        'login_success' => 'Login Berhasil',
        'login_failed' => 'Login Gagal',
        'login_locked' => 'Login Terkunci',
        'session_hijack' => 'Percobaan Bajak Sesi',
        'csrf_mismatch' => 'CSRF Tidak Cocok',
    ];

    public const SEVERITIES = ['info', 'low', 'medium', 'high', 'critical'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'read_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucwords(str_replace('_', ' ', $this->type));
    }

    public function severityColor(): string
    {
        return match ($this->severity) {
            'critical' => 'ng-badge-red',
            'high' => 'ng-badge-red',
            'medium' => 'ng-badge-amber',
            'low' => 'ng-badge-blue',
            default => 'ng-badge-neutral',
        };
    }

    public function markRead(): void
    {
        $this->update(['read_at' => now()]);
    }
}

<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public function log(string $action, ?object $model = null, array $details = []): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? $model::class : null,
            'model_id' => $model?->id,
            'details' => $details ?: null,
        ]);
    }
}

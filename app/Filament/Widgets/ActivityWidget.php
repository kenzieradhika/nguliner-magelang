<?php

namespace App\Filament\Widgets;

use App\Models\AuditLog;
use App\Models\User;
use Filament\Widgets\Widget;

class ActivityWidget extends Widget
{
    protected static ?int $sort = 8;

    protected static string $view = 'filament.widgets.activity';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role === 'superadmin';
    }

    public function getViewData(): array
    {
        return [
            'activities' => AuditLog::query()
                ->with('user')
                ->latest()
                ->limit(8)
                ->get(),
        ];
    }

    public static function iconFor(string $action): string
    {
        if (str_contains($action, 'created') || str_contains($action, 'imported')) {
            return 'heroicon-m-plus-circle';
        }

        if (str_contains($action, 'deleted')) {
            return 'heroicon-m-trash';
        }

        if (str_contains($action, 'backup')) {
            return 'heroicon-m-archive-box';
        }

        return 'heroicon-m-pencil-square';
    }
}
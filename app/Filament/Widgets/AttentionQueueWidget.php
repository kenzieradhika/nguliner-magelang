<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CollaborationResource;
use App\Filament\Resources\PlaceSuggestionResource;
use App\Filament\Resources\ReviewResource;
use App\Filament\Resources\SecurityEventResource;
use App\Models\Collaboration;
use App\Models\PlaceSuggestion;
use App\Models\Review;
use App\Services\SecurityEventService;
use Filament\Widgets\Widget;

class AttentionQueueWidget extends Widget
{
    protected static ?int $sort = 4;

    protected static string $view = 'filament.widgets.attention-queue';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return true;
    }

    public function getViewData(): array
    {
        $unreadSecurity = app(SecurityEventService::class)->unreadCount();

        return [
            'counts' => [
                [
                    'label' => 'Kolaborasi baru',
                    'count' => Collaboration::where('status', 'new')->count(),
                    'url' => CollaborationResource::getUrl('index'),
                    'color' => 'danger',
                ],
                [
                    'label' => 'Review menunggu',
                    'count' => Review::where('is_approved', false)->count(),
                    'url' => ReviewResource::getUrl('index'),
                    'color' => 'warning',
                ],
                [
                    'label' => 'Saran baru',
                    'count' => PlaceSuggestion::where('status', 'new')->count(),
                    'url' => PlaceSuggestionResource::getUrl('index'),
                    'color' => 'info',
                ],
                [
                    'label' => 'Insiden keamanan',
                    'count' => $unreadSecurity,
                    'url' => SecurityEventResource::getUrl('index'),
                    'color' => 'gray',
                ],
            ],
            'lastBackup' => $this->lastBackup(),
        ];
    }

    private function lastBackup(): ?string
    {
        $files = \Illuminate\Support\Facades\Storage::disk('local')->files('backups');

        if (empty($files)) {
            return null;
        }

        $latest = max(array_map(
            fn ($file) => \Illuminate\Support\Facades\Storage::disk('local')->lastModified($file),
            $files,
        ));

        return \Illuminate\Support\Carbon::createFromTimestamp($latest)->diffForHumans();
    }
}
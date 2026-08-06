<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PlaceResource;
use App\Models\Place;
use App\Services\DailyPickService;
use Filament\Widgets\Widget;

class DailyPickWidget extends Widget
{
    protected static ?int $sort = 6;

    protected static string $view = 'filament.widgets.daily-pick';

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $pick = (new DailyPickService())->pick(1)->first();
        $featured = Place::query()
            ->where('is_featured', true)
            ->where('is_published', true)
            ->with('category')
            ->orderByDesc('views')
            ->limit(4)
            ->get();

        return [
            'pick' => $pick,
            'featured' => $featured,
        ];
    }

    public static function getUrl(Place $place): string
    {
        return PlaceResource::getUrl('edit', ['record' => $place]);
    }
}
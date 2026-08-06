<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CollaborationResource;
use App\Filament\Resources\PlaceResource;
use App\Filament\Resources\PlaceSuggestionResource;
use App\Filament\Resources\ReviewResource;
use App\Models\Collaboration;
use App\Models\IgPost;
use App\Models\Place;
use App\Models\PlaceSuggestion;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $reviewTrend = Review::query()
            ->where('created_at', '>=', now()->subMonths(6))
            ->get()
            ->groupBy(fn (Review $r): string => $r->created_at->format('Y-m'))
            ->map(fn ($group): int => $group->count())
            ->sortKeys()
            ->values()
            ->all();

        $collabTrend = Collaboration::query()
            ->where('created_at', '>=', now()->subMonths(6))
            ->get()
            ->groupBy(fn (Collaboration $c): string => $c->created_at->format('Y-m'))
            ->map(fn ($group): int => $group->count())
            ->sortKeys()
            ->values()
            ->all();

        $avgRating = round(Review::where('is_approved', true)->avg('rating') ?? 0, 1);

        return [
            Stat::make('Kuliner', Place::count())
                ->description('Total tempat terdaftar')
                ->descriptionIcon('heroicon-o-map-pin')
                ->color('primary')
                ->url(PlaceResource::getUrl('index')),
            Stat::make('Rating Rata-rata', number_format($avgRating, 1))
                ->description('Dari review yang disetujui')
                ->descriptionIcon('heroicon-o-star')
                ->color('warning')
                ->url(ReviewResource::getUrl('index')),
            Stat::make('Total Views', number_format((int) Place::sum('views')))
                ->description('Kunjungan halaman kuliner')
                ->descriptionIcon('heroicon-o-eye')
                ->color('info'),
            Stat::make('Review', Review::count())
                ->description(Review::where('is_approved', false)->count().' menunggu persetujuan')
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color(Review::where('is_approved', false)->count() > 0 ? 'warning' : 'success')
                ->chart($reviewTrend)
                ->url(ReviewResource::getUrl('index')),
            Stat::make('Kolaborasi Baru', Collaboration::where('status', 'new')->count())
                ->description('Perlu direspons')
                ->descriptionIcon('heroicon-o-hand-raised')
                ->color('danger')
                ->chart($collabTrend)
                ->url(CollaborationResource::getUrl('index')),
            Stat::make('Saran Tempat', PlaceSuggestion::where('status', 'new')->count())
                ->description('Belum direview')
                ->descriptionIcon('heroicon-o-light-bulb')
                ->color('warning')
                ->url(PlaceSuggestionResource::getUrl('index')),
            Stat::make('Feed Instagram', IgPost::count())
                ->description('Postingan tersimpan')
                ->descriptionIcon('heroicon-o-camera')
                ->color('gray'),
        ];
    }
}

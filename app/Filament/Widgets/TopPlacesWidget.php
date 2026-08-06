<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PlaceResource;
use App\Models\Place;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TopPlacesWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        return 'Kuliner Terpopuler';
    }

    public function getStats(): array
    {
        $top = Place::query()
            ->with('category')
            ->orderByDesc('views')
            ->limit(5)
            ->get();

        if ($top->isEmpty()) {
            return [Stat::make('Belum ada data views', '0')->description('Views akan terisi saat pengunjung membuka halaman kuliner.')];
        }

        return $top->map(fn (Place $place): Stat => Stat::make(
            $place->name,
            number_format((int) $place->views)
        )
            ->description($place->category?->name.' · '.(($place->tagline) ?: 'Tanpa tagline'))
            ->descriptionIcon('heroicon-o-fire')
            ->color($place->views > 0 ? 'danger' : 'gray')
            ->extraAttributes([
                'class' => 'cursor-pointer',
            ])
            ->url(PlaceResource::getUrl('edit', ['record' => $place])))->all();
    }
}

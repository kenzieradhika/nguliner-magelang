<?php

namespace App\Filament\Widgets;

use App\Models\Review;
use Filament\Widgets\LineChartWidget;

class ReviewTrend extends LineChartWidget
{
    protected static ?string $heading = 'Tren Review (12 Bulan)';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $months = collect(range(11, 0))
            ->map(fn (int $i): string => now()->subMonths($i)->format('Y-m'))
            ->filter(fn (string $ym) => substr($ym, 0, 4) >= (int) now()->subYears(1)->format('Y'));

        $counts = $months->mapWithKeys(function (string $ym): array {
            return [$ym => Review::whereYear('created_at', substr($ym, 0, 4))
                ->whereMonth('created_at', (int) substr($ym, 5, 2))
                ->count()];
        });

        $monthLabels = $months->map(fn (string $ym): string => \Illuminate\Support\Carbon::createFromFormat('Y-m', $ym)->translatedFormat('M'));

        return [
            'datasets' => [
                [
                    'label' => 'Review',
                    'data' => $counts->values()->all(),
                    'borderColor' => '#C2410C',
                    'backgroundColor' => 'rgba(194, 65, 12, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $monthLabels->all(),
        ];
    }
}
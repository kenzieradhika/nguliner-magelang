<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\BarChartWidget;

class CategoryChart extends BarChartWidget
{
    protected static ?string $heading = 'Kuliner per Kategori';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $categories = Category::query()
            ->whereHas('places')
            ->withCount('places')
            ->orderByDesc('places_count')
            ->limit(10)
            ->get();

        $palette = ['#C2410C', '#EA580C', '#F97316', '#FB923C', '#FDBA74', '#65A30D', '#4D7C0F', '#CA8A04', '#B45309', '#92400E'];

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kuliner',
                    'data' => $categories->pluck('places_count')->all(),
                    'backgroundColor' => $palette,
                    'borderRadius' => 6,
                    'maxBarThickness' => 36,
                ],
            ],
            'labels' => $categories->pluck('name')->all(),
        ];
    }
}
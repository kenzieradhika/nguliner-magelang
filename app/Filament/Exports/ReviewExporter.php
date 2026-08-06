<?php

namespace App\Filament\Exports;

use App\Models\Review;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;

class ReviewExporter extends Exporter
{
    protected static ?string $model = Review::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('place.name')->label('Kuliner'),
            ExportColumn::make('name')->label('Pengulas'),
            ExportColumn::make('rating')->label('Rating'),
            ExportColumn::make('comment')->label('Komentar'),
            ExportColumn::make('is_approved')->label('Disetujui')->formatStateUsing(fn ($state) => $state ? 'Ya' : 'Tidak'),
            ExportColumn::make('created_at')->label('Masuk'),
        ];
    }
}
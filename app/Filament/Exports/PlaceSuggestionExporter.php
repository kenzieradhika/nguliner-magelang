<?php

namespace App\Filament\Exports;

use App\Models\PlaceSuggestion;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;

class PlaceSuggestionExporter extends Exporter
{
    protected static ?string $model = PlaceSuggestion::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Nama'),
            ExportColumn::make('category')->label('Kategori'),
            ExportColumn::make('address')->label('Alamat'),
            ExportColumn::make('contact')->label('Kontak'),
            ExportColumn::make('description')->label('Deskripsi'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('created_at')->label('Masuk'),
        ];
    }
}
<?php

namespace App\Filament\Exports;

use App\Models\Place;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;

class PlaceExporter extends Exporter
{
    protected static ?string $model = Place::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Nama'),
            ExportColumn::make('category.name')->label('Kategori'),
            ExportColumn::make('tagline')->label('Tagline'),
            ExportColumn::make('address')->label('Alamat'),
            ExportColumn::make('whatsapp')->label('WhatsApp'),
            ExportColumn::make('price_range')->label('Kisaran Harga'),
            ExportColumn::make('open_days')->label('Hari Buka'),
            ExportColumn::make('open_time')->label('Jam Buka'),
            ExportColumn::make('close_time')->label('Jam Tutup'),
            ExportColumn::make('since_year')->label('Sejak Tahun'),
            ExportColumn::make('views')->label('Views'),
            ExportColumn::make('is_legendary')->label('Legendaris')->formatStateUsing(fn ($state) => $state ? 'Ya' : 'Tidak'),
            ExportColumn::make('is_featured')->label('Unggulan')->formatStateUsing(fn ($state) => $state ? 'Ya' : 'Tidak'),
            ExportColumn::make('is_published')->label('Tayang')->formatStateUsing(fn ($state) => $state ? 'Ya' : 'Tidak'),
            ExportColumn::make('created_at')->label('Dibuat'),
        ];
    }
}
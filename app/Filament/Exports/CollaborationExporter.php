<?php

namespace App\Filament\Exports;

use App\Models\Collaboration;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;

class CollaborationExporter extends Exporter
{
    protected static ?string $model = Collaboration::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Nama'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('whatsapp')->label('WhatsApp'),
            ExportColumn::make('business_name')->label('Usaha'),
            ExportColumn::make('type')->label('Jenis'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('message')->label('Pesan'),
            ExportColumn::make('created_at')->label('Masuk'),
        ];
    }
}
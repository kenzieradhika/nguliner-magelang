<?php

namespace App\Filament\Resources\IgPostResource\Pages;

use App\Filament\Resources\IgPostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIgPost extends EditRecord
{
    protected static string $resource = IgPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

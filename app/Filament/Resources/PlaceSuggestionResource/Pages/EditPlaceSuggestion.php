<?php

namespace App\Filament\Resources\PlaceSuggestionResource\Pages;

use App\Filament\Resources\PlaceSuggestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlaceSuggestion extends EditRecord
{
    protected static string $resource = PlaceSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

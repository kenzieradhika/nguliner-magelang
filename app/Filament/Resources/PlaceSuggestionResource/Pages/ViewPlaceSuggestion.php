<?php

namespace App\Filament\Resources\PlaceSuggestionResource\Pages;

use App\Filament\Resources\PlaceSuggestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPlaceSuggestion extends ViewRecord
{
    protected static string $resource = PlaceSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
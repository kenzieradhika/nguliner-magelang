<?php

namespace App\Filament\Resources\PlaceSuggestionResource\Pages;

use App\Filament\Resources\PlaceSuggestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlaceSuggestions extends ListRecords
{
    protected static string $resource = PlaceSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PlaceResource;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    protected static string $view = 'filament.pages.dashboard';

    public function getHeaderActions(): array
    {
        return [
            Action::make('view_site')
                ->label('Lihat Situs')
                ->icon('heroicon-o-globe-alt')
                ->color('gray')
                ->url(route('home'))
                ->openUrlInNewTab(),
            Action::make('create_place')
                ->label('Tambah Kuliner')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(PlaceResource::getUrl('create')),
        ];
    }

    public function getViewData(): array
    {
        return [
            'greeting' => $this->greeting(),
        ];
    }

    private function greeting(): string
    {
        $hour = (int) now()->format('G');

        return match (true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 19 => 'Selamat sore',
            default => 'Selamat malam',
        };
    }
}
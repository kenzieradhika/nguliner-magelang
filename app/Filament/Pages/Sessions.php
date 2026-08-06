<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Sessions extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?string $navigationLabel = 'Sesi Aktif';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?string $title = 'Sesi Aktif';

    protected static ?int $navigationSort = 16;

    protected static string $view = 'filament.pages.sessions';

    public function getSessionsProperty(): array
    {
        return DB::table('sessions')
            ->where('user_id', auth()->id())
            ->orderByDesc('last_activity')
            ->get()
            ->map(fn ($session) => [
                'id' => $session->id,
                'ip' => $session->ip_address,
                'agent' => $session->user_agent,
                'last_activity' => \Illuminate\Support\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                'is_current' => $session->id === session()->getId(),
            ])
            ->all();
    }

    public function revoke(string $id): void
    {
        $id = preg_replace('/[^a-zA-Z0-9]/', '', $id);

        if ($id === session()->getId()) {
            Notification::make()->warning()->title('Tidak bisa mencabut sesi yang sedang dipakai')->send();

            return;
        }

        DB::table('sessions')->where('id', $id)->where('user_id', auth()->id())->delete();
        Notification::make()->success()->title('Sesi dicabut')->send();
    }

    public function revokeAll(): void
    {
        DB::table('sessions')->where('user_id', auth()->id())->where('id', '!=', session()->getId())->delete();
        Notification::make()->success()->title('Semua sesi lain dicabut')->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('revokeAll')
                ->label('Cabut Semua Sesi Lain')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->action(fn () => $this->revokeAll()),
        ];
    }
}
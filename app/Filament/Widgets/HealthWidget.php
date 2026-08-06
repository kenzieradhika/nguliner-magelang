<?php

namespace App\Filament\Widgets;

use App\Security\NativeGuard;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 9;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role === 'superadmin';
    }

    protected function getHeading(): ?string
    {
        return 'Kesehatan Sistem';
    }

    public function getStats(): array
    {
        $dbSize = file_exists(database_path('database.sqlite'))
            ? filesize(database_path('database.sqlite'))
            : 0;

        $backups = Storage::disk('local')->files('backups');
        $backupSize = collect($backups)
            ->map(fn ($file): int => Storage::disk('local')->size($file))
            ->sum();
        $queueCount = DB::table('jobs')->count();

        $guard = app(NativeGuard::class);

        $stats = [
            Stat::make('Ukuran Database', number_format($dbSize / 1024 / 1024, 2).' MB')
                ->descriptionIcon('heroicon-o-database')
                ->color('primary'),
            Stat::make('Backup Tersimpan', count($backups))
                ->description('Total '.number_format($backupSize / 1024 / 1024, 2).' MB')
                ->descriptionIcon('heroicon-o-archive-box')
                ->color('success'),
            Stat::make('Antrean Pekerjaan', $queueCount)
                ->description($queueCount > 0 ? 'Job sedang menunggu' : 'Tidak ada antrean')
                ->descriptionIcon('heroicon-o-cpu-chip')
                ->color($queueCount > 0 ? 'warning' : 'success'),
            Stat::make('Lingkungan', app()->environment())
                ->descriptionIcon('heroicon-o-server')
                ->color(app()->environment('production') ? 'warning' : 'info'),
        ];

        if ($guard->isNative()) {
            $stats[] = Stat::make('Keamanan Native', 'Aktif (FFI)')
                ->description($guard->version())
                ->descriptionIcon('heroicon-o-shield-check')
                ->color('success');
        } else {
            $stats[] = Stat::make('Keamanan Native', 'Fallback')
                ->description('FFI tidak tersedia — pakai PHP')
                ->descriptionIcon('heroicon-o-shield-exclamation')
                ->color('warning');
        }

        return $stats;
    }
}
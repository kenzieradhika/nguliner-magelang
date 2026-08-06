<?php

namespace App\Filament\Pages;

use App\Services\AuditService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Backup extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Backup Database';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?string $title = 'Backup Database';

    protected static ?int $navigationSort = 13;

    protected static string $view = 'filament.pages.backup';

    public function getFilesProperty(): array
    {
        return collect(Storage::disk('local')->files('backups'))
            ->sortDesc()
            ->map(fn ($file) => [
                'path' => $file,
                'name' => basename($file),
                'size' => Storage::disk('local')->size($file),
                'modified' => Storage::disk('local')->lastModified($file),
            ])
            ->values()
            ->all();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggle_maintenance')
                ->label(fn (): string => app()->isDownForMaintenance() ? 'Aktifkan Situs' : 'Tidak Aktifkan Situs (Maintenance)')
                ->icon(fn (): string => app()->isDownForMaintenance() ? 'heroicon-o-play' : 'heroicon-o-pause')
                ->color(fn (): string => app()->isDownForMaintenance() ? 'success' : 'warning')
                ->action(function (AuditService $audit): void {
                    if (app()->isDownForMaintenance()) {
                        Artisan::call('up');
                        $audit->log('maintenance.off', null, []);
                        Notification::make()->success()->title('Situs aktif kembali')->send();
                    } else {
                        Artisan::call('down');
                        $audit->log('maintenance.on', null, []);
                        Notification::make()->warning()->title('Situs masuk mode maintenance')->body('Pengunjung akan melihat halaman pemeliharaan.')->send();
                    }
                }),
            Action::make('restore')
                ->label('Restore Backup')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('File backup (.sqlite)')
                        ->acceptedFileTypes(['application/octet-stream', 'application/x-sqlite3'])
                        ->storeFiles(false)
                        ->helperText('Backup keselamatan akan dibuat otomatis sebelum database diganti.')
                        ->required(),
                ])
                ->action(function (array $data, AuditService $audit): void {
                    $file = $data['file'] ?? null;

                    if (! $file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                        Notification::make()->danger()->title('File tidak valid')->send();

                        return;
                    }

                    $dbPath = database_path('database.sqlite');

                    if (is_file($dbPath)) {
                        Artisan::call('app:backup');
                    }

                    copy($file->getRealPath(), $dbPath);
                    Artisan::call('optimize:clear');
                    $audit->log('backup.restored', null, ['file' => $file->getClientOriginalName()]);

                    Notification::make()
                        ->success()
                        ->title('Restore selesai')
                        ->body('Database dipulihkan. Backup keselamatan tersimpan di folder backups.')
                        ->send();
                }),
            Action::make('createBackup')
                ->label('Backup Sekarang')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->color('primary')
                ->action(function (AuditService $audit): void {
                    Artisan::call('app:backup');
                    $output = trim(Artisan::output());

                    $audit->log('backup.created', null, ['output' => $output]);

                    Notification::make()->success()->title('Backup berhasil')->body($output)->send();
                }),
        ];
    }

    public function download(string $name): BinaryFileResponse
    {
        $name = basename($name);
        $path = 'backups/' . $name;

        abort_if(! Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, $name);
    }

    public function delete(string $name, AuditService $audit): void
    {
        $name = basename($name);
        $path = 'backups/' . $name;

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            $audit->log('backup.deleted', null, ['file' => $name]);
            Notification::make()->success()->title('Backup dihapus')->send();
        }
    }
}
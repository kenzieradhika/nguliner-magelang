<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'app:backup';

    protected $description = 'Backup database SQLite ke storage (rotasi 14 file)';

    public function handle(): int
    {
        $db = database_path('database.sqlite');

        if (! is_file($db)) {
            $this->error('File database tidak ditemukan.');

            return self::FAILURE;
        }

        $disk = Storage::disk('local');
        $folder = 'backups';
        $name = 'nguliner-' . now()->format('Y-m-d-His') . '.sqlite';

        if (! $disk->exists($folder)) {
            $disk->makeDirectory($folder);
        }

        $disk->put($folder . '/' . $name, file_get_contents($db));

        $files = collect($disk->files($folder))->sortDesc();
        $toDelete = $files->slice(14);

        foreach ($toDelete as $file) {
            $disk->delete($file);
            $this->line("Rotasi: hapus {$file}");
        }

        $this->info("Backup berhasil: {$folder}/{$name}");

        return self::SUCCESS;
    }
}

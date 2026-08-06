<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Place;
use Illuminate\Console\Command;

class PublishScheduledContent extends Command
{
    protected $signature = 'content:publish';

    protected $description = 'Tayangkan konten yang sudah mencapai waktu publish_at';

    public function handle(): int
    {
        $places = Place::query()
            ->where('is_published', false)
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', now())
            ->update(['is_published' => true, 'publish_at' => null]);

        $pages = Page::query()
            ->where('is_published', false)
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', now())
            ->update(['is_published' => true, 'publish_at' => null]);

        $this->info("Selesai. {$places} kuliner dan {$pages} halaman ditayangkan.");

        return self::SUCCESS;
    }
}
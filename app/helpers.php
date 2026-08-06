<?php

use App\Services\SiteSettingsService;

if (! function_exists('site_setting')) {
    function site_setting(string $key, ?string $default = null): ?string
    {
        return app(SiteSettingsService::class)->get($key, $default);
    }
}
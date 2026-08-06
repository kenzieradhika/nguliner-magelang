<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SiteSettingsService
{
    private const CACHE_KEY = 'site_settings';

    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addHours(6), function (): array {
            $settings = SiteSetting::current();

            return $settings
                ? array_merge(SiteSetting::defaults(), $settings->only(array_keys(SiteSetting::defaults())))
                : SiteSetting::defaults();
        });
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $value = $this->all()[$key] ?? $default;

        return $value !== null && $value !== '' ? (string) $value : $default;
    }

    public function update(array $values): SiteSetting
    {
        $settings = SiteSetting::current() ?? new SiteSetting;

        foreach (array_keys(SiteSetting::defaults()) as $key) {
            $settings->{$key} = $values[$key] ?? null;
        }

        $settings->save();
        Cache::forget(self::CACHE_KEY);

        return $settings;
    }
}

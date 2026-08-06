<?php

namespace App\Listeners;

use App\Models\AuditLog;
use App\Models\Microsite;
use App\Models\Page;
use App\Models\Place;
use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\Model;

class AdminActivitySubscriber
{
    private const TRACKED_MODELS = [
        Place::class,
        Page::class,
        Microsite::class,
    ];

    public function handleLogin(Login $event): void
    {
        if ($event->user instanceof \App\Models\User) {
            $event->user->forceFill(['last_login_at' => now()])->save();
        }
    }

    public function handleUpdated(Model $model): void
    {
        if (! auth()->check()) {
            return;
        }

        if (! in_array(get_class($model), self::TRACKED_MODELS, true)) {
            return;
        }

        if (! $model->isDirty() || ! $model->exists) {
            return;
        }

        $before = [];
        $after = [];

        foreach ($model->getDirty() as $key => $newValue) {
            if (in_array($key, ['updated_at', 'created_at', 'views'], true)) {
                continue;
            }

            $before[$key] = $model->getOriginal($key);
            $after[$key] = $newValue;
        }

        if (empty($after)) {
            return;
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'model_type' => $model::class,
            'model_id' => $model->getKey(),
            'details' => ['before' => $before, 'after' => $after],
        ]);
    }

    public function subscribe($events): void
    {
        $events->listen(Login::class, self::class.'@handleLogin');

        foreach (self::TRACKED_MODELS as $model) {
            $events->listen('eloquent.updated: '.$model, self::class.'@handleUpdated');
        }
    }
}
<?php

namespace App\Services;

use App\Mail\NewSubmissionMail;
use App\Models\Collaboration;
use App\Models\Place;
use App\Models\PlaceSuggestion;
use App\Models\User;
use App\Models\Review;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public static function collaborationSubmitted(Collaboration $collaboration): void
    {
        static::notifyAdmins('Kolaborasi', $collaboration->name, "Tipe: {$collaboration->type} — {$collaboration->message}");
    }

    public static function reviewSubmitted(Place $place): void
    {
        static::notifyAdmins('Review', $place->name, "Review baru untuk {$place->name} menunggu moderasi.");
    }

    public static function suggestionSubmitted(PlaceSuggestion $suggestion): void
    {
        static::notifyAdmins('Saran Tempat', $suggestion->name, "Saran tempat: {$suggestion->name} ({$suggestion->category})");
    }

    private static function notifyAdmins(string $type, string $name, string $details): void
    {
        $admins = User::whereIn('role', ['superadmin', 'editor'])->get();

        foreach ($admins as $admin) {
            Mail::to($admin)->queue(new NewSubmissionMail($type, $name, $details));

            FilamentNotification::make()
                ->title("{$type} baru: {$name}")
                ->body($details)
                ->icon(match ($type) {
                    'Kolaborasi' => 'heroicon-o-hand-raised',
                    'Review' => 'heroicon-o-star',
                    default => 'heroicon-o-light-bulb',
                })
                ->sendToDatabase($admin);
        }
    }
}

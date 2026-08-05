<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CollaborationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FeedController;
use App\Http\Controllers\Admin\MicrositeController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PlaceController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SecurityEventController;
use App\Http\Controllers\Admin\SuggestionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth', 'admin', 'ng-hardening'])->group(function () {
    Route::get('/', DashboardController::class)->name('admin.dashboard');

    Route::resource('places', PlaceController::class)
        ->except('show')
        ->names('admin.places');

    Route::get('categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

    Route::resource('pages', PageController::class)
        ->except('show')
        ->names('admin.pages');

    Route::resource('microsites', MicrositeController::class)
        ->except('show')
        ->names('admin.microsites');

    Route::get('collaborations', [CollaborationController::class, 'index'])->name('admin.collaborations.index');
    Route::put('collaborations/{collaboration}/status', [CollaborationController::class, 'updateStatus'])->name('admin.collaborations.status');
    Route::delete('collaborations/{collaboration}', [CollaborationController::class, 'destroy'])->name('admin.collaborations.destroy');

    Route::get('reviews', [ReviewController::class, 'index'])->name('admin.reviews.index');
    Route::put('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('admin.reviews.approve');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('admin.reviews.destroy');

    Route::get('suggestions', [SuggestionController::class, 'index'])->name('admin.suggestions.index');
    Route::put('suggestions/{suggestion}/status', [SuggestionController::class, 'updateStatus'])->name('admin.suggestions.status');
    Route::delete('suggestions/{suggestion}', [SuggestionController::class, 'destroy'])->name('admin.suggestions.destroy');

    Route::get('feed', [FeedController::class, 'index'])->name('admin.feed.index');
    Route::post('feed/import', [FeedController::class, 'import'])->name('admin.feed.import');
    Route::delete('feed/{post}', [FeedController::class, 'destroy'])->name('admin.feed.destroy');

    Route::get('backup', [BackupController::class, 'index'])->name('admin.backup.index');
    Route::post('backup', [BackupController::class, 'run'])->name('admin.backup.run');
    Route::get('backup/download', [BackupController::class, 'download'])->name('admin.backup.download');
    Route::delete('backup', [BackupController::class, 'destroy'])->name('admin.backup.destroy');

    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('admin.audit.index');

    Route::get('keamanan', [SecurityEventController::class, 'index'])->name('admin.security.index');
    Route::put('keamanan/{event}/read', [SecurityEventController::class, 'markRead'])->name('admin.security.read');
    Route::put('keamanan/read-all', [SecurityEventController::class, 'markAllRead'])->name('admin.security.read-all');
    Route::delete('keamanan/{event}', [SecurityEventController::class, 'destroy'])->name('admin.security.destroy');
    Route::delete('keamanan', [SecurityEventController::class, 'destroyAll'])->name('admin.security.destroy-all');

    Route::middleware('superadmin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('users', [UserController::class, 'store'])->name('admin.users.store');
        Route::put('users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.role');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });
});

<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CollaborationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MicrositeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SuggestionController;
use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/', HomeController::class)->name('home');
Route::get('/kategori/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/kuliner/{slug}', [PlaceController::class, 'show'])->name('place.show');
Route::post('/kuliner/{slug}/review', [PlaceController::class, 'storeReview'])->name('place.review');
Route::get('/peta', MapController::class)->name('map');
Route::get('/cari', SearchController::class)->name('search');
Route::get('/kolaborasi', [CollaborationController::class, 'create'])->name('collaboration.create');
Route::post('/kolaborasi', [CollaborationController::class, 'store'])->name('collaboration.store');
Route::get('/saran', [SuggestionController::class, 'create'])->name('suggestion.create');
Route::post('/saran', [SuggestionController::class, 'store'])->name('suggestion.store');
Route::get('/halaman/{slug}', [PageController::class, 'show'])->name('page.show');

Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsAdmin::class])->group(function () {
    Route::get('/admin/preview/page/{page}', [PreviewController::class, 'page'])->name('admin.preview.page');
    Route::get('/admin/preview/place/{place}', [PreviewController::class, 'place'])->name('admin.preview.place');
    Route::get('/admin/preview/microsite/{microsite}', [PreviewController::class, 'microsite'])->name('admin.preview.microsite');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// require __DIR__.'/admin.php'; // Digantikan panel Filament (app/Providers/Filament/AdminPanelProvider.php)
require __DIR__.'/auth.php';

Route::get('/{slug}', [MicrositeController::class, 'show'])->name('microsite.show');

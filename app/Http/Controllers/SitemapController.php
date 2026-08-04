<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Microsite;
use App\Models\Page;
use App\Models\Place;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $places = Place::where('is_published', true)->get();
        $categories = Category::whereHas('places', fn ($q) => $q->where('is_published', true))->get();
        $pages = Page::where('is_published', true)->get();
        $microsites = Microsite::where('is_active', true)->with('place')->get();

        $content = view('seo.sitemap', compact('places', 'categories', 'pages', 'microsites'))->render();

        return response($content, 200)->header('Content-Type', 'application/xml');
    }
}

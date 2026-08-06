<?php

namespace App\Http\Controllers;

use App\Models\Microsite;
use App\Models\Page;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PreviewController extends Controller
{
    public function page(Request $request, Page $page): View
    {
        abort_unless($request->hasValidSignature(), 403);

        return view('page.show', ['page' => $page]);
    }

    public function place(Request $request, Place $place): View
    {
        abort_unless($request->hasValidSignature(), 403);

        return view('place.show', ['place' => $place]);
    }

    public function microsite(Request $request, Microsite $microsite): View
    {
        abort_unless($request->hasValidSignature(), 403);

        return view('microsite.show', ['microsite' => $microsite]);
    }
}
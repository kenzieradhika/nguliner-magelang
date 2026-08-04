<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IgPost;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FeedController extends Controller
{
    public function index(): View
    {
        $posts = IgPost::latest('posted_at')->paginate(15);

        return view('admin.feed.index', compact('posts'));
    }

    public function import(Request $request, AuditService $audit): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:json,txt', 'max:2048'],
        ]);

        $path = $request->file('file')->getRealPath();
        $json = json_decode(file_get_contents($path), true);

        if (! is_array($json)) {
            return back()->withErrors(['file' => 'File JSON tidak valid.']);
        }

        $imported = 0;

        foreach ($json as $post) {
            if (! isset($post['ig_id'])) {
                continue;
            }

            $exists = IgPost::where('ig_id', $post['ig_id'])->exists();

            if ($exists) {
                continue;
            }

            IgPost::create([
                'ig_id' => $post['ig_id'],
                'image_url' => $post['image_url'] ?? null,
                'permalink' => $post['permalink'] ?? null,
                'caption' => $post['caption'] ?? null,
                'posted_at' => $post['posted_at'] ?? null,
            ]);

            $imported++;
        }

        $audit->log('feed.imported', null, ['imported' => $imported]);

        return back()->with('success', "{$imported} post feed berhasil diimpor.");
    }

    public function destroy(IgPost $post, AuditService $audit): RedirectResponse
    {
        $post->delete();
        $audit->log('feed.deleted', null, ['ig_id' => $post->ig_id]);

        return back()->with('success', 'Post feed dihapus.');
    }
}

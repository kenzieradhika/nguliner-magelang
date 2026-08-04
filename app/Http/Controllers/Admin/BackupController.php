<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;

class BackupController extends Controller
{
    private const DISK = 'local';
    private const FOLDER = 'backups';

    public function index(): View
    {
        $files = collect(Storage::disk(self::DISK)->files(self::FOLDER))
            ->sortDesc()
            ->map(fn ($file) => [
                'path' => $file,
                'name' => basename($file),
                'size' => Storage::disk(self::DISK)->size($file),
                'modified' => Storage::disk(self::DISK)->lastModified($file),
            ])
            ->values();

        return view('admin.backup.index', compact('files'));
    }

    public function run(AuditService $audit): RedirectResponse
    {
        Artisan::call('app:backup');
        $output = trim(Artisan::output());
        $audit->log('backup.created', null, ['output' => $output]);

        return back()->with('success', $output);
    }

    public function download(Request $request): BinaryFileResponse
    {
        $name = basename($request->input('file'));

        $path = self::FOLDER . '/' . $name;

        abort_if(! Storage::disk(self::DISK)->exists($path), 404);

        return response()->download(Storage::disk(self::DISK)->path($path), $name);
    }

    public function destroy(Request $request, AuditService $audit): RedirectResponse
    {
        $name = basename($request->input('file'));
        $path = self::FOLDER . '/' . $name;

        if (Storage::disk(self::DISK)->exists($path)) {
            Storage::disk(self::DISK)->delete($path);
            $audit->log('backup.deleted', null, ['file' => $name]);
        }

        return back()->with('success', 'Backup dihapus.');
    }
}

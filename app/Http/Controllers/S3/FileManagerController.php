<?php

namespace App\Http\Controllers\S3;

use App\Http\Controllers\Controller;
use App\Http\Requests\S3\UploadFileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileManagerController extends Controller
{
    public function index(Request $request): View
    {
        $currentPath = $request->query('path', '');
        $disk = Storage::disk('s3');

        $files = collect($disk->files($currentPath))->map(function (string $filePath) use ($disk) {
            return [
                'path' => $filePath,
                'name' => basename($filePath),
                'size' => $disk->size($filePath),
                'lastModified' => $disk->lastModified($filePath),
            ];
        });

        $directories = collect($disk->directories($currentPath))->map(function (string $dirPath) {
            return [
                'path' => $dirPath,
                'name' => basename($dirPath),
            ];
        });

        return view('s3.file-manager', [
            'files' => $files,
            'directories' => $directories,
            'currentPath' => $currentPath,
        ]);
    }

    public function upload(UploadFileRequest $request): RedirectResponse
    {
        $file = $request->file('file');
        $path = $request->input('path', '');

        Storage::disk('s3')->putFileAs($path, $file, $file->getClientOriginalName());

        return redirect()->route('s3.index', ['path' => $path])
            ->with('success', "File \"{$file->getClientOriginalName()}\" uploaded successfully.");
    }

    public function download(Request $request): StreamedResponse
    {
        $request->validate([
            'path' => ['required', 'string'],
        ]);

        $path = $request->query('path');

        abort_unless(Storage::disk('s3')->exists($path), 404, 'File not found.');

        return Storage::disk('s3')->download($path);
    }

    public function signedUrl(Request $request): JsonResponse
    {
        $request->validate([
            'path' => ['required', 'string'],
            'expiry' => ['nullable', 'integer', 'min:1', 'max:10080'],
        ]);

        $path = $request->input('path');
        $expiry = $request->input('expiry', 60);

        abort_unless(Storage::disk('s3')->exists($path), 404, 'File not found.');

        $url = Storage::disk('s3')->temporaryUrl($path, now()->addMinutes($expiry));

        return response()->json(['url' => $url]);
    }

    public function destroy(Request $request, string $path): RedirectResponse
    {
        abort_unless(Storage::disk('s3')->exists($path), 404, 'File not found.');

        Storage::disk('s3')->delete($path);

        $parentPath = dirname($path);
        $parentPath = $parentPath === '.' ? '' : $parentPath;

        return redirect()->route('s3.index', ['path' => $parentPath])
            ->with('success', 'File deleted successfully.');
    }
}

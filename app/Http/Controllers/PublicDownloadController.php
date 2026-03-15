<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicDownloadController extends Controller
{
    /**
     * Securely download public documents via signed URLs.
     */
    public function download(Request $request): StreamedResponse
    {
        $file = $request->query('file');
        
        // Security check: Prevent directory traversal
        if (!$file || str_contains($file, '..') || str_contains($file, '/') || str_contains($file, '\\')) {
            abort(403, 'Invalid file path.');
        }

        // Define base path for public downloads
        $path = "downloads/{$file}";

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found or has been moved.');
        }

        return Storage::disk('public')->download($path);
    }
}

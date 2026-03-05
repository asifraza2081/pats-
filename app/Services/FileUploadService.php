<?php

declare(strict_types=1);

namespace App\Services;

class FileUploadService
{
    private string $uploadPath;

    public function __construct(string $subDirectory = '')
    {
        $this->uploadPath = BASE_PATH . '/storage/uploads/' . trim($subDirectory, '/');
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    /**
     * Handle array from $_FILES and move to storage.
     * Returns the relative path (e.g. 'photos/abc.jpg') on success, or null.
     */
    public function upload(array $file, array $allowedMimes = ['image/jpeg', 'image/png'], int $maxSize = 2097152): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > $maxSize) {
            throw new \RuntimeException('File size exceeds ' . ($maxSize / 1024 / 1024) . 'MB limit.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedMimes, true)) {
            throw new \RuntimeException('Invalid file type. Allowed: ' . implode(', ', $allowedMimes));
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('upload_', true) . '.' . $ext;

        $dest = $this->uploadPath . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return basename($this->uploadPath) . '/' . $filename; // Returns e.g. "photos/upload_123.jpg"
        }

        return null;
    }
}

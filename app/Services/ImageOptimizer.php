<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    private int $maxWidth = 1600;

    private int $jpegQuality = 82;

    public function optimize(UploadedFile $file, string $directory, ?string $name = null): string
    {
        $info = @getimagesize($file->getRealPath());

        if (! $info) {
            return $file->store($directory, 'public');
        }

        $mime = $info['mime'];
        $src = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($file->getRealPath()),
            'image/png' => imagecreatefrompng($file->getRealPath()),
            'image/webp' => imagecreatefromwebp($file->getRealPath()),
            'image/gif' => imagecreatefromgif($file->getRealPath()),
            default => null,
        };

        if (! $src) {
            return $file->store($directory, 'public');
        }

        $width = imagesx($src);
        $height = imagesy($src);

        if ($width <= $this->maxWidth) {
            imagedestroy($src);

            return $file->store($directory, 'public');
        }

        $ratio = $this->maxWidth / $width;
        $newWidth = (int) round($this->maxWidth);
        $newHeight = (int) round($height * $ratio);

        $dst = imagecreatetruecolor($newWidth, $newHeight);

        if (in_array($mime, ['image/png', 'image/webp', 'image/gif'], true)) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $extension = $mime === 'image/png' ? 'png' : 'jpg';
        $storedName = $name ?: sprintf('%s.%s', Str::lower(Str::random(24)), $extension);
        $path = trim($directory, '/').'/'.$storedName;

        $stream = fopen('php://temp', 'r+');
        if ($mime === 'image/png') {
            imagepng($dst, $stream);
        } else {
            imagejpeg($dst, $stream, $this->jpegQuality);
        }
        rewind($stream);

        Storage::disk('public')->put($path, $stream, 'public');

        fclose($stream);
        imagedestroy($src);
        imagedestroy($dst);

        return $path;
    }
}
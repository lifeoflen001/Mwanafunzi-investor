<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    public function store(UploadedFile $file, string $directory = 'media', ?string $altText = null, ?string $title = null): Media
    {
        $disk = 'public';
        $path = $file->store($directory, $disk);
        $dimensions = @getimagesize($file->getRealPath()) ?: [null, null];

        $media = Media::create([
            'disk' => $disk,
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size' => $file->getSize(),
            'width' => $dimensions[0],
            'height' => $dimensions[1],
            'alt_text' => $altText,
            'title' => $title,
            'variants' => $this->variants($file, $path, $dimensions),
        ]);

        return $media;
    }

    /**
     * Remove a media record and, optionally, the stored file and responsive variants.
     *
     * Keeping the database record and stored contents as one operation is the safe
     * default. Admins can explicitly keep the files when removing an asset from the
     * media catalogue, for example while legacy content still points at its path.
     */
    public function delete(Media $media, bool $deleteFiles = true): void
    {
        if ($deleteFiles) {
            Storage::disk($media->disk ?: 'public')->delete($media->path);
            foreach ($media->variants ?? [] as $variant) {
                if (! empty($variant['path'])) Storage::disk($media->disk ?: 'public')->delete($variant['path']);
            }
        }
        $media->delete();
    }

    private function variants(UploadedFile $file, string $path, array $dimensions): array
    {
        // XAMPP's PHP build may not have GD/Imagick enabled. Keep the original
        // usable and generate WebP variants only when a safe local driver exists.
        if (! function_exists('imagewebp') || ! $dimensions[0] || ! $dimensions[1]) return [];

        $mime = $file->getMimeType();
        $loader = match ($mime) {
            'image/jpeg' => 'imagecreatefromjpeg',
            'image/png' => 'imagecreatefrompng',
            'image/webp' => 'imagecreatefromwebp',
            default => null,
        };
        if (! $loader || ! function_exists($loader)) return [];

        $source = @$loader($file->getRealPath());
        if (! $source) return [];

        $variants = [];
        $base = pathinfo($path, PATHINFO_FILENAME);
        foreach ([480, 768, 1200, 1920] as $width) {
            if ($width >= $dimensions[0]) continue;
            $height = (int) round($dimensions[1] * ($width / $dimensions[0]));
            $canvas = imagecreatetruecolor($width, $height);
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $width, $height, $dimensions[0], $dimensions[1]);
            $variantPath = "media/variants/{$base}-{$width}.webp";
            ob_start();
            imagewebp($canvas, null, 82);
            Storage::disk('public')->put($variantPath, ob_get_clean());
            imagedestroy($canvas);
            $variants[] = ['width' => $width, 'height' => $height, 'path' => $variantPath, 'mime_type' => 'image/webp'];
        }
        imagedestroy($source);
        return $variants;
    }
}

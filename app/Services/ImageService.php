<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImageService
{
    public static function generateCleanFilename(string $originalName): string
    {
        $name = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $slug = Str::slug($name);
        $random = substr(md5(time()), 0, 6);

        return $slug . '-' . $random . '.' . strtolower($extension);
    }

    public static function ensureDirectoryExists(string $path): void
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    public static function deleteIfExists(string $url): void
    {
        $path = public_path(parse_url($url, PHP_URL_PATH));
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    public static function extractImagePaths(?string $html): array
    {
        $paths = [];

        if (!$html) return $paths;

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            if (strpos($src, '/uploads/') !== false) {
                $paths[] = $src;
            }
        }

        return $paths;
    }
}

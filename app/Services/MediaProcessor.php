<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Illuminate\Http\UploadedFile;

class MediaProcessor
{
    protected string $baseFolder = 'media';

    protected array $variants = [
        'original'  => null,
        'large'     => 1200,
        'medium'    => 850,
        'thumbnail' => [400, 250],
        'og'        => [1200, 630],
    ];

    protected ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new GdDriver());
    }

    public function process(UploadedFile $file, string $slug): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $timestamp = now()->format('YmdHis');
        $meta = [];

        foreach ($this->variants as $key => $size) {
            $filename = "{$slug}-{$timestamp}.{$extension}";

            if ($key === 'original') {
                // Save to "media" folder
                $folder = $this->baseFolder;
                $path   = "{$folder}/{$filename}";

                Storage::disk('public')->putFileAs($folder, $file, $filename);

                $image = $this->imageManager->read(Storage::disk('public')->path($path));
            } else {
                $folder = "{$this->baseFolder}/{$key}";
                $path   = "{$folder}/{$filename}";

                $image = $this->imageManager->read($file->getPathname());

                if (is_array($size)) {
                    $image = $image->cover($size[0], $size[1]);
                } else {
                    $maxHeight = 500;
                    $image = $this->smartResize($image, $size, $maxHeight);
                }

                $encoder = match ($extension) {
                    'jpg', 'jpeg' => new JpegEncoder(),
                    'png'         => new PngEncoder(),
                    'webp'        => new WebpEncoder(),
                    default       => new JpegEncoder(),
                };

                Storage::disk('public')->put($path, (string) $image->encode($encoder));
            }

            $meta[$key] = [
                'path'   => $path,
                'width'  => $image->width(),
                'height' => $image->height(),
                'size'   => Storage::disk('public')->size($path),
            ];
        }

        return $meta;
    }

    public function customResize(string $filePath, string $slug, ?int $width = null, ?int $height = null): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $timestamp = now()->format('YmdHis');

        // Create new name file
        $filename = "{$slug}-custom-{$timestamp}.{$extension}";
        $folder = "{$this->baseFolder}/custom";
        $path = "{$folder}/{$filename}";

        $image = $this->imageManager->read($filePath);

        // Resize logic
        if ($width && !$height) {
            $image = $this->smartResize($image, $width, 500); // giới hạn chiều cao
        } elseif ($height && !$width) {
            $image = $this->smartResize($image, 1200, $height); // giới hạn chiều rộng
        } elseif ($width && $height) {
            $image = $image->cover($width, $height); // crop cứng
        }


        // Encoder
        $encoder = match ($extension) {
            'jpg', 'jpeg' => new JpegEncoder(),
            'png'         => new PngEncoder(),
            'webp'        => new WebpEncoder(),
            default       => new JpegEncoder(),
        };

        Storage::disk('public')->put($path, (string) $image->encode($encoder));

        return [
            'path'   => $path,
            'url'    => Storage::url($path),
            'width'  => $image->width(),
            'height' => $image->height(),
            'size'   => Storage::disk('public')->size($path),
        ];
    }

    public function smartResize($image, int $maxWidth, int $maxHeight): \Intervention\Image\Image
    {
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        // scale
        $scale = min($maxWidth / $originalWidth, $maxHeight / $originalHeight, 1);

        $newWidth = intval($originalWidth * $scale);
        $newHeight = intval($originalHeight * $scale);

        return $image->resize($newWidth, $newHeight);
    }
}

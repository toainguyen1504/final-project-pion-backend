<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
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
        $this->imageManager = new ImageManager('imagick'); // or 'imagick'
    }

    public function process(UploadedFile $file, string $slug): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $timestamp = now()->format('YmdHis');
        $meta = [];

        foreach ($this->variants as $key => $size) {
            $filename = "{$slug}-{$timestamp}.{$extension}";
            $folder = "{$this->baseFolder}/{$key}";
            $path = "{$folder}/{$filename}";

            $image = $this->imageManager->read($file->getPathname());

            if ($key === 'original') {
                Storage::disk('public')->putFileAs($folder, $file, $filename);
            } else {
                if (is_array($size)) {
                    // Crop đúng tỷ lệ (cover)
                    $image = $image->cover($size[0], $size[1]);
                } else {
                    // Resize theo chiều rộng, giữ tỷ lệ
                    $image = $image->resize($size, null);
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
}

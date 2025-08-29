<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\MediaProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class MediaApiController extends Controller
{
    // List media (GET /api/media)
    public function index()
    {
        $mediaList = Media::latest()->paginate(20);

        $mediaList->getCollection()->transform(function ($media) {
            $media->url = Storage::url($media->path);
            return $media;
        });

        return $mediaList;
    }

    // GET /api/media/{id}
    public function show(Media $media)
    {

        if (!$media->exists) {
            return response()->json(['error' => 'Media not found'], 404);
        }
        $media->url = Storage::url($media->path);

        return response()->json([
            'id'          => $media->id,
            'type'        => $media->type,
            'mime_type'   => $media->mime_type,
            'title'       => $media->title,
            'alt'         => $media->alt,
            'caption'     => $media->caption,
            'description' => $media->description,
            'url'         => $media->url,
            'meta'        => $media->meta ?? new \stdClass(), // FE chỉ cần đọc trong meta
            'created_at'  => $media->created_at?->toDateTimeString(),
        ]);
    }

    //Upload file (POST /api/media)
    public function store(Request $request, MediaProcessor $processor)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,webp',
            'slugs.*' => 'nullable|string',
        ]);

        if (!$request->hasFile('files')) {
            return response()->json(['message' => 'Không có file upload'], 400);
        }

        $uploaded = [];

        try {
            foreach ($request->file('files') as $index => $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension    = strtolower($file->getClientOriginalExtension());

                $slug = $request->input("slugs.$index") ?? Str::slug($originalName);

                // Call MediaProcessor to create variants
                $variantsMeta = $processor->process($file, $slug);

                // get "original" -> main path
                $originalPath = $variantsMeta['original']['path'];

                $media = Media::create([
                    'path'       => $originalPath,
                    'mime_type'  => $file->getMimeType(),
                    'type'       => str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image',
                    'title'      => $slug,
                    'meta'       => [
                        'variants'  => $variantsMeta,
                        'extension' => $extension,
                        'filename'  => basename($originalPath),
                    ],
                ]);

                $media->url = Storage::url($originalPath);
                $uploaded[] = $media;
            }

            return response()->json($uploaded, 201);
        } catch (\Throwable $e) {
            Log::error('Upload media failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);

            return response()->json([
                'error' => 'Lỗi server khi xử lý ảnh',
            ], 500);
        }
    }

    // Update metadata (PUT /api/media/{id})
    public function update(Request $request, Media $media)
    {
        $meta = $request->input('meta');
        if (is_string($meta)) {
            $meta = json_decode($meta, true) ?? [];
        }

        $media->update([
            'title'       => $request->input('title'),
            'alt'         => $request->input('alt'),
            'caption'     => $request->input('caption'),
            'description' => $request->input('description'),
            'meta'        => $meta,
        ]);

        return response()->json($media);
    }

    // Delete media (DELETE /api/media/{id})
    public function destroy(Media $media)
    {
        $variants = $media->meta['variants'] ?? [];

        foreach ($variants as $variant) {
            if (!empty($variant['path']) && Storage::disk('public')->exists($variant['path'])) {
                Storage::disk('public')->delete($variant['path']);
            }
        }

        $media->delete();

        return response()->json(['message' => 'Deleted']);
    }

    // Resize (custom)
    public function resize(Request $request, Media $media, MediaProcessor $processor)
    {
        $request->validate([
            'width'  => 'nullable|integer|min:50',
            'height' => 'nullable|integer|min:50',
        ]);

        if (!Storage::disk('public')->exists($media->path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $width  = $request->input('width');
        $height = $request->input('height');

        $filePath = Storage::disk('public')->path($media->path);

        $slug = $media->title ? Str::slug($media->title) : "media-{$media->id}";
        $meta = $processor->customResize($filePath, $slug, $width, $height);

        $oldMeta = $media->meta ?? [];
        $media->meta = array_merge($oldMeta, [
            'custom_resize' => $meta,
        ]);
        $media->save();

        return response()->json([
            'message' => 'Image resized successfully',
            'url'     => $meta['url'] ?? null,
            'media'   => $media,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostContent;
use App\Models\Media;
use App\Services\MediaProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class MediaController extends Controller
{
    /**
     * List media files with pagination
     * GET /api/media
     */
    public function index()
    {
        $mediaList = Media::latest()->paginate(20);

        $mediaList->getCollection()->transform(function ($media) {
            $media->url = Storage::url($media->path);
            return $media;
        });

        return response()->json([
            'success' => true,
            'data' => $mediaList->items(),
            'meta' => [
                'current_page' => $mediaList->currentPage(),
                'last_page' => $mediaList->lastPage(),
                'per_page' => $mediaList->perPage(),
                'total' => $mediaList->total(),
                'next_page_url' => $mediaList->nextPageUrl(),
                'prev_page_url' => $mediaList->previousPageUrl()
            ]
        ]);
    }

    /**
     * Show media detail
     * GET /api/media/{id}
     */
    public function show(Media $media)
    {
        $media->url = Storage::url($media->path);

        return response()->json([
            'success'      => true,
            'data' => [
                'id'          => $media->id,
                'type'        => $media->type,
                'mime_type'   => $media->mime_type,
                'title'       => $media->title,
                'alt'         => $media->alt,
                'caption'     => $media->caption,
                'description' => $media->description,
                'url'         => $media->url,
                'meta'        => $media->meta ?? new \stdClass(),
                'created_at'  => $media->created_at?->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Upload media files
     * POST /api/media
     */
    public function store(Request $request, MediaProcessor $processor)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,webp',
            'slugs.*' => 'nullable|string',
        ]);

        if (!$request->hasFile('files')) {
            return response()->json([
                'success' => false,
                'message' => 'No files uploaded.'
            ], 400);
        }

        $uploaded = [];

        try {
            foreach ($request->file('files') as $index => $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension    = strtolower($file->getClientOriginalExtension());
                $slug         = $request->input("slugs.$index") ?? Str::slug($originalName);

                // Generate variants using MediaProcessor
                $variantsMeta = $processor->process($file, $slug);
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

            return response()->json([
                'success' => true,
                'message' => 'Media uploaded successfully.',
                'data' => $uploaded
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Media upload failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error while processing media.'
            ], 500);
        }
    }

    /**
     * Update media metadata
     * PUT /api/media/{id}
     */
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

        return response()->json([
            'success' => true,
            'message' => 'Media metadata updated.',
            'data' => $media
        ]);
    }

    /**
     * Delete media file
     * DELETE /api/media/{id}
     */
    public function destroy(Media $media)
    {
        try {
            $usedPaths = collect($media->meta['variants'] ?? [])
                ->pluck('path')
                ->filter()
                ->map(fn($path) => Storage::url($path));

            $isUsed = PostContent::where(function ($query) use ($usedPaths) {
                foreach ($usedPaths as $path) {
                    $query->orWhere('content_html', 'LIKE', '%' . $path . '%');
                }
            })->exists();

            if ($isUsed) {
                return response()->json([
                    'success' => false,
                    'message' => 'This media is currently used in a post and cannot be deleted.'
                ], 400);
            }

            foreach ($media->meta['variants'] ?? [] as $variant) {
                if (!empty($variant['path'])) {
                    Storage::disk('public')->delete($variant['path']);
                }
            }

            $media->delete();

            return response()->json([
                'success' => true,
                'message' => '🗑️ Media deleted successfully.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Media deletion failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error while deleting media.'
            ], 500);
        }
    }

    /**
     * Resize media manually
     * POST /api/media/{id}/resize
     */
    public function resize(Request $request, Media $media, MediaProcessor $processor)
    {
        $request->validate([
            'width'  => 'nullable|integer|min:50',
            'height' => 'nullable|integer|min:50',
        ]);

        if (!Storage::disk('public')->exists($media->path)) {
            return response()->json([
                'success' => false,
                'message' => 'Original file not found.'
            ], 404);
        }

        $width  = $request->input('width');
        $height = $request->input('height');
        $filePath = Storage::disk('public')->path($media->path);
        $slug = $media->title ? Str::slug($media->title) : "media-{$media->id}";

        $meta = $processor->customResize($filePath, $slug, $width, $height);

        if (empty($meta['url'])) {
            return response()->json([
                'success' => false,
                'message' => 'Resize failed or no URL returned.'
            ], 500);
        }

        $media->meta = array_merge($media->meta ?? [], [
            'custom_resize' => $meta,
        ]);
        $media->save();

        return response()->json([
            'success' => true,
            'message' => 'Image resized successfully.',
            'url'     => $meta['url'],
            'data'    => $media
        ]);
    }
}

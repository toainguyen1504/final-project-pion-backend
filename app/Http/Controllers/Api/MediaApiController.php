<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\MediaProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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


    //Upload file (POST /api/media)
    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:1024000|mimes:jpg,jpeg,png,gif,webp',
            'slugs.*' => 'nullable|string', // slug from frontend
        ]);

        $uploaded = [];

        foreach ($request->file('files') as $index => $file) {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $slug = $request->input("slugs.$index") ?? Str::slug($originalName);

            $timestamp = now()->format('YmdHis');
            $filename = "{$slug}-{$timestamp}.{$extension}";

            $path = $file->storeAs('media', $filename, 'public');

            $media = Media::create([
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'type' => str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image',
                'title' => $slug,
            ]);

            $media->url = Storage::url($path);
            $uploaded[] = $media;
        }

        return response()->json($uploaded, 201);
    }


    // Update metadata (PUT /api/media/{id})
    public function update(Request $request, Media $media)
    {
        $media->update([
            'title' => $request->input('title'),
            'alt' => $request->input('alt'),
            'caption' => $request->input('caption'),
            'description' => $request->input('description'),
            'meta' => $request->input('meta'),
        ]);


        return response()->json($media);
    }

    // Delete media (DELETE /api/media/{id})
    public function destroy(Media $media)
    {
        if (Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }

        $media->delete();

        return response()->json(['message' => 'Deleted']);
    }
}

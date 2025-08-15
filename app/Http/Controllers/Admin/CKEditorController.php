<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ImageService;

class CKEditorController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');

            $allowedMime = ['image/jpeg', 'image/png'];
            if (!in_array($file->getMimeType(), $allowedMime)) {
                return response()->json([
                    'uploaded' => 0,
                    'error' => ['message' => 'Chỉ chấp nhận ảnh JPEG hoặc PNG.']
                ], 422);
            }

            $cleanName = ImageService::generateCleanFilename($file->getClientOriginalName());

            $uploadPath = public_path('uploads/posts');
            ImageService::ensureDirectoryExists($uploadPath);

            $file->move($uploadPath, $cleanName);

            return response()->json([
                'uploaded' => 1,
                'fileName' => $cleanName,
                'url' => asset('uploads/posts/' . $cleanName)
            ]);
        }

        return response()->json([
            'uploaded' => 0,
            'error' => ['message' => 'Không có file được gửi lên.']
        ], 400);
    }
}

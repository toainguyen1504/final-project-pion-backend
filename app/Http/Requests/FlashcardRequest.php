<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlashcardRequest extends FormRequest
{
    /**
     * Xác định quyền gọi request này
     */
    public function authorize(): bool
    {
        // Cho phép tất cả user có quyền gọi, 
        // có thể thêm logic check role ở đây
        return true;
    }

    /**
     * Các rules validate cho flashcard
     */
    public function rules(): array
    {
        return [
            'vocabulary' => 'required|string|max:255',
            'phonetic' => 'nullable|string|max:255',
            'translation' => 'nullable|string|max:255',
            'example_sentence' => 'nullable|string',
            'example_translation' => 'nullable|string',
            'image_url' => 'nullable|string',
            'image_prompt' => 'nullable|string',
            'audio' => 'nullable|string',
            'level' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'tags' => 'nullable|json',
            'lesson_id' => 'required|exists:lessons,id',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Có thể thêm logic phân quyền ở đây, mặc định cho phép
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('program'); // lấy id từ route khi update

        return [
            'title' => 'required|string|max:255',
            'slug'  => 'nullable|string|unique:programs,slug,' . $id,
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique'  => 'Tiêu đề này đã tạo slug trùng, hãy đổi tiêu đề để tiếp tục!',
            'status.in'    => 'Trạng thái không hợp lệ, chỉ chấp nhận active hoặc inactive.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('course'); // lấy id khi update

        return [
            'title'          => 'required|string|max:255',
            'slug'           => 'nullable|string|unique:courses,slug,' . $id,
            'language'       => 'nullable|string|max:50',
            'thumbnail'      => 'nullable|string',
            'thumbnail_media_id' => 'nullable|exists:medias,id',
            'description'    => 'nullable|string',
            'price'          => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'level'          => 'required|integer|min:0|max:10',
            'status'         => 'required|string|in:draft,pending,published,inactive,archived',
            'duration'       => 'nullable|integer|min:0',
            'participants'   => 'nullable|integer|min:0',
            'total_lessons'  => 'nullable|integer|min:0',
            'benefits'       => 'nullable|string',
            'is_free'        => 'boolean',
            'program_id'     => 'required|exists:programs,id', // required
            'category_id'    => 'required|exists:categories,id', //required
            'user_id'        => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique'    => 'Tiêu đề này đã tạo slug trùng, hãy đổi tiêu đề để tiếp tục!',
            'thumbnail_media_id.exists' => 'Ảnh thumbnail không tồn tại trong thư viện media.',
            'level.min'      => 'Level không hợp lệ, phải từ 0 đến 10.',
            'level.max'      => 'Level không hợp lệ, phải từ 0 đến 10.',
            'status.in'      => 'Trạng thái không hợp lệ, chỉ chấp nhận draft, pending, published, inactive hoặc archived.',
            'program_id.exists'  => 'Program không tồn tại.',
            'category_id.exists' => 'Category không tồn tại.',
        ];
    }
}

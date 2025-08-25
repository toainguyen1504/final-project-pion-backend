<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'title'       => 'required|string|max:255',
            'category_ids'  => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'content'     => 'required|string|min:50',
        ];
    }

    public function messages()
    {
        return [
            'title.required'       => 'Vui lòng nhập tiêu đề bài viết.',
            'title.max'            => 'Tiêu đề quá dài, tối đa 255 ký tự.',
            'category_ids.required'  => 'Vui lòng chọn ít nhất một danh mục.',
            'category_ids.array'     => 'Dữ liệu danh mục không hợp lệ.',
            'category_ids.*.exists'  => 'Danh mục đã chọn không tồn tại.',
            'content.required'     => 'Bạn chưa nhập nội dung bài viết.',
            'content.min'          => 'Nội dung bài viết cần dài hơn để đảm bảo đầy đủ thông tin.',
        ];
    }
}

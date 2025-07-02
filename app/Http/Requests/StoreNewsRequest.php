<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'content'     => 'required|string|min:10',
        ];
    }

    public function messages()
    {
        return [
            'title.required'       => 'Vui lòng nhập tiêu đề bài viết.',
            'title.max'            => 'Tiêu đề quá dài, tối đa 255 ký tự.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists'   => 'Danh mục không hợp lệ.',
            'content.required'     => 'Bạn chưa nhập nội dung bài viết.',
            'content.min'          => 'Nội dung cần tối thiểu 10 ký tự.',
        ];
    }
}

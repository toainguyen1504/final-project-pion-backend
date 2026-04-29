<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Models\Category;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');  // Lấy ID từ route (cần cho việc ignore trong validation)

        // Lấy category từ database
        $category = Category::find($id);

        // Nếu không tìm thấy category, trả lỗi
        if (!$category) {
            return [];
        }

        $rules = [
            'name' => [
                'required',
                'string',
                'max:50',
                // Chỉ kiểm tra trùng tên nếu name có thay đổi
                Rule::unique('categories', 'name')->ignore($id),
            ],
            'type' => ['required', 'string', 'max:50'],  // Loại của category (post, course, ...)
            'is_featured' => ['boolean'],  // Nếu có
        ];

        // Nếu tên không thay đổi, bỏ qua kiểm tra trùng tên
        if ($this->input('name') === $category->name) {
            unset($rules['name']);  // Bỏ qua validation unique nếu tên không thay đổi
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Category name is required.',
            'name.string'   => 'Category name must be text.',
            'name.max'      => 'Category name must be under 50 characters.',
            'name.unique'   => 'Category name is already taken.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ], 422));
    }
}

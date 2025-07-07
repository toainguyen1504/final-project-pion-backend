<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
   use Illuminate\Contracts\Validation\Validator;
    use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cho phép sử dụng request này
    }

    public function rules()
    {
        $id = $this->route('id'); // Lấy ID từ route để xử lý update

        return [
            'name' => 'required|string|max:50|unique:categories,name' . ($id ? ',' . $id : ''),
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.string'   => 'Tên danh mục phải là chuỗi ký tự.',
            'name.max'      => 'Tên danh mục không được vượt quá 50 ký tự.',
            'name.unique'   => 'Tên danh mục này đã tồn tại.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $modal = $this->routeIs('categories.update') ? 'modalEditCategory' : 'modalAddCategory';
        $redirect = redirect()
            ->route('admin.categories.index')
            ->withErrors($validator)
            ->withInput()
            ->with('openModal', $modal);

        if ($modal === 'modalEditCategory') {
            $redirect->with('editingId', $this->route('id'));
        }

        throw new HttpResponseException($redirect);
    }
}

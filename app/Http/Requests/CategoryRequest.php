<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');

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

        // Check if the form is submitted from the create post page
        if ($this->has('from_post_create')) {
            $redirect = redirect()
                ->route('admin.posts.create')
                ->withErrors($validator, 'category')
                ->withInput()
                ->with('openModal', $modal);

            throw new HttpResponseException($redirect);
        }

        // Default: redirect to the category management page
        $redirect = redirect()
            ->route('admin.categories.index')
            ->withErrors($validator, 'category')
            ->withInput()
            ->with('openModal', $modal);

        if ($modal === 'modalEditCategory') {
            $redirect->with('editingId', $this->route('id'));
        }

        throw new HttpResponseException($redirect);
    }
}

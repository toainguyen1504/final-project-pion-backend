<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'title'           => 'required|string|max:255',
            'category_ids'    => 'required|array|min:1',
            'category_ids.*'  => 'exists:categories,id',
            'content'         => 'required|string|min:50',
        ];
    }

    public function messages()
    {
        return [
            'title.required'          => 'Please enter a post title.',
            'title.max'               => 'The title may not be greater than 255 characters.',
            'category_ids.required'   => 'Please select at least one category.',
            'category_ids.array'      => 'Invalid category data format.',
            'category_ids.*.exists'   => 'One or more selected categories do not exist.',
            'content.required'        => 'Post content is required.',
            'content.min'             => 'Content must be at least 50 characters long.',
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

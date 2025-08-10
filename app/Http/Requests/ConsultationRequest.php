<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoBadWords;
// use App\Rules\ValidRecaptcha;

class ConsultationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guest_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZÀ-Ỹà-ỹ\s]+$/',
                new NoBadWords()
            ],
            'guest_email' => 'required|email|max:254',
            'guest_phone' => 'required|regex:/^0\d{9}$/',
            'request_content' => [
                'required',
                'string',
                'min:10',
                'max:500',
                new NoBadWords()
            ],
            // 'recaptcha_token' => ['required', new ValidRecaptcha()],
        ];
    }

    public function messages()
    {
        return [
            'guest_name.required' => 'Vui lòng nhập tên.',
            'guest_name.regex' => 'Tên không được chứa số hoặc ký tự đặc biệt!',
            'guest_email.required' => 'Vui lòng nhập email!',
            'guest_email.email' => 'Email không hợp lệ! VD: abc@gmail.com',
            'guest_email.max' => 'Email không được vượt quá 254 ký tự!',
            'guest_phone.required' => 'Vui lòng nhập số điện thoại!',
            'guest_phone.regex' => 'Số điện thoại phải có đúng 10 chữ số và bắt đầu bằng số 0! (VD: 0912345678)',
            'request_content.required' => 'Vui lòng nhập nội dung yêu cầu!',
            'request_content.min' => 'Nội dung tư vấn phải từ 10-500 ký tự!',
            'request_content.max' => 'Nội dung tư vấn phải từ 10-500 ký tự!',
            // 'recaptcha_token.required' => 'Vui lòng xác nhận reCAPTCHA!',
        ];
    }
}

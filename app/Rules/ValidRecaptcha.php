<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ValidRecaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret'),
                'response' => $value,
            ]);

            $result = $response->json();

            Log::info('🔐 reCAPTCHA response', $result);

            if (!($result['success'] ?? false)) {
                $fail('Xác thực reCAPTCHA không thành công. Vui lòng thử lại.');
            }
        } catch (\Exception $e) {
            Log::error('❌ Lỗi xác thực reCAPTCHA:', ['message' => $e->getMessage()]);
            $fail('Không thể xác thực reCAPTCHA. Vui lòng thử lại sau.');
        }
    }
}

<?php
namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class PasswordService
{
    public static function generate(int $length = 6): array
    {
        // Sinh password ngẫu nhiên (chỉ chữ và số)
        $plain = Str::random($length);

        // Hash để lưu DB
        $hashed = Hash::make($plain);

        return [
            'plain' => $plain,
            'hashed' => $hashed,
        ];
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Str;

class UsernameService
{
    public static function generate($user): string
    {
        $yy = now()->format('y');   // 2 số cuối của năm

        // Lấy tên từ learner nếu có, nếu không thì dùng display_name
        $name = '';
        if ($user->learner) {
            $name = trim(($user->learner->first_name ?? '') . ' ' . ($user->learner->last_name ?? ''));
        } else {
            $name = $user->display_name ?? 'user';
        }

        // Chuyển thành không dấu
        $ascii = Str::ascii($name);

        // Loại bỏ khoảng trắng và ký tự đặc biệt, chỉ giữ chữ và số
        $slug = preg_replace('/[^A-Za-z0-9]/', '', strtolower($ascii));

        // Nếu tên quá ngắn (<=2 ký tự) thì thêm 2 ký tự random
        if (strlen($slug) <= 2) {
            $slug .= Str::random(2);
        }

        // Nếu tên quá dài (>= 8 ký tự) thì cắt gọn: lấy họ + tên
        if (strlen($slug) >= 8) {
            // Tách từ gốc trước khi loại bỏ khoảng trắng
            $parts = preg_split('/\s+/', strtolower($ascii));
            if (count($parts) >= 2) {
                $first = preg_replace('/[^a-z0-9]/', '', $parts[0]); // họ
                $last  = preg_replace('/[^a-z0-9]/', '', end($parts)); // tên
                $slug  = $last . $first; // tên + họ
            }
        }

        // Nếu là learner thì bỏ chữ pion_
        if ($user->role && $user->role->name === 'learner') {
            return "{$slug}{$user->id}{$yy}";
        }
        
        return "{$slug}{$user->id}{$yy}";
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Str;

class UsernameService
{
    public static function generate($user): string
    {
        $yy = now()->format('y');
        $role = $user->role->name ?? 'user';

        switch ($role) {
            case 'learner':
                $learner = $user->learner; // có thể null nếu chưa tạo profile
                if ($learner) {
                    $slug = Str::slug($learner->first_name . '' . $learner->last_name);
                    return "{$slug}{$user->id}{$yy}";
                }
                // Fallback an toàn khi chưa có learner profile
                return "learner-{$user->id}{$yy}";

            case 'teacher':
            case 'staff':
            case 'admin':
            case 'super_admin':
                return "{$role}{$user->id}{$yy}";

            default:
                return "{$role}{$user->id}{$yy}";
        }
    }
}

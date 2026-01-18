<?php

namespace App\Services;

use Illuminate\Support\Str;

class UsernameService
{
    public static function generate($user): string
    {
        $yy = now()->format('y');   // 2 số cuối của năm
        $mm = now()->format('m');   // 2 số của tháng
        $role = $user->role ? $user->role->name : 'user';

        switch ($role) {
            case 'learner':
                $learner = $user->learner;
                if ($learner) {
                    $slug = Str::slug(($learner->first_name ?? '') . ($learner->last_name ?? ''));
                    return "{$slug}{$user->id}{$mm}{$yy}";
                }
                return "learner{$user->id}{$mm}{$yy}";

            case 'teacher':
            case 'staff':
            case 'admin':
            case 'super_admin':
                return "{$role}{$user->id}{$mm}{$yy}";

            default:
                return "{$role}{$user->id}{$mm}{$yy}";
        }
    }
}

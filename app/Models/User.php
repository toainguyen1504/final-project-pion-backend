<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use App\Services\UsernameService;
use App\Models\Learner;
use App\Models\Teacher;
use App\Models\Staff;

class User extends Authenticatable
{
    // use Notifiable;
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'display_name',
        'username',       // thêm vào để có thể gán
        'email',
        'password',
        'role_id',
        'profile_image',
    ];
    protected $hidden = ['password', 'remember_token'];

    // ===== Quan hệ =====
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function learner()
    {
        return $this->hasOne(Learner::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    // ===== Helpers =====
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->role && in_array($this->role->name, $roles);
    }

    // ===== Hook để sinh username =====
    protected static function booted()
    {
        static::created(function ($user) {
            if (!$user->username) {
                $user->username = UsernameService::generate($user);
                $user->save();
            }
        });
    }
}

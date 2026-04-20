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

    // ===== Constants cho status ===== 
    const STATUS_UNVERIFIED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCKED = 2;

    protected $fillable = [
        'display_name',
        'username',       // thêm vào để có thể gán
        'email',
        'password',
        'role_id',
        'status',
        'profile_image',
    ];
    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['status' => 'integer', 'email_verified_at' => 'datetime'];

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

    // Kiểm tra trạng thái 
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }
    public function isUnverified(): bool
    {
        return $this->status === self::STATUS_UNVERIFIED;
    }

    // ===== Hook để sinh username =====
    protected static function booted()
    {
        static::created(function ($user) {
            if (!$user->username) {
                // Load quan hệ trước khi generate
                $user->loadMissing(['role', 'learner', 'teacher', 'staff']);

                try {
                    $username = UsernameService::generate($user);
                    $user->update(['username' => $username]);
                } catch (\Throwable $e) {
                    // Nếu không dùng log, thì ít nhất không để lỗi làm hỏng response
                }
            }
        });
    }
}

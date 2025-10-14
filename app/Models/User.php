<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // use Notifiable;
    use HasApiTokens, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role_id', 'profile_image'];

    protected $hidden = ['password', 'remember_token'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->role && in_array($this->role->name, $roles);
    }
}

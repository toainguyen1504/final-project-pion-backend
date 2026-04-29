<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $superAdminRole = Role::where('name', 'super_admin')->first();

        if (!$adminRole || !$superAdminRole) {
            throw new \Exception('Roles not found. Please run RoleSeeder first.');
        }

        User::firstOrCreate([
            'email' => 'admin@pion.vn'
        ], [
            'display_name' => 'Admin',
            'username'     => 'admin' . now()->format('y'), // thêm username
            'password' => Hash::make('Admin@6868'),
            'profile_image' => 'default_avatar.jpg',
            'role_id' => $adminRole->id
        ]);

        User::firstOrCreate([
            'email' => 'superadmin@pion.vn'
        ], [
            'display_name' => 'Super Admin',
            'username'     => 'superadmin_' . now()->format('y'),
            'password' => Hash::make('Admin@6868'),
            'profile_image' => 'default_avatar.jpg',
            'role_id' => $superAdminRole->id
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{ 
    public function run(): void
    {
        $roles = Role::pluck('id', 'name'); // ['admin' => 1, 'staff' => 2, ...]

        // 2 staffs
        User::firstOrCreate(['email' => 'staff1@pion.vn'], [
            'name' => 'Admin Pion',
            'password' => Hash::make('Staff@1123'),
            'role_id' => $roles['staff']
        ]);

        User::firstOrCreate(['email' => 'staff2@pion.vn'], [
            'name' => 'Staff Two',
            'password' => Hash::make('Staff@1123'),
            'role_id' => $roles['staff']
        ]);

        // Staff ADS
        // User::firstOrCreate(['email' => 'adminads@pion.vn'], [
        //     'name' => 'Staff ADS',
        //     'password' => Hash::make('Adminads110825'),
        //     'role_id' => $roles['staffads']
        // ]);

        // 2 teachers
        User::firstOrCreate(['email' => 'teacher1@pion.vn'], [
            'name' => 'Cô Rita',
            'password' => Hash::make('Teacher@1123'),
            'role_id' => $roles['teacher']
        ]);

        User::firstOrCreate(['email' => 'teacher2@pion.vn'], [
            'name' => 'Thầy Minh',
            'password' => Hash::make('Teacher@1123'),
            'role_id' => $roles['teacher']
        ]);

        // 2 parents
        User::firstOrCreate(['email' => 'parent1@pion.vn'], [
            'name' => 'Phụ huynh An',
            'password' => Hash::make('Parent@1123'),
            'role_id' => $roles['parent']
        ]);

        User::firstOrCreate(['email' => 'parent2@pion.vn'], [
            'name' => 'Phụ huynh Bình',
            'password' => Hash::make('Parent@1123'),
            'role_id' => $roles['parent']
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'super_admin']); //1
        Role::firstOrCreate(['name' => 'admin']); //2
        Role::firstOrCreate(['name' => 'staff']); //3
        Role::firstOrCreate(['name' => 'staffads']); //4
        Role::firstOrCreate(['name' => 'teacher']); //5
        
        Role::firstOrCreate(['name' => 'learner']); //6
        Role::firstOrCreate(['name' => 'guest']); //7
    }
}

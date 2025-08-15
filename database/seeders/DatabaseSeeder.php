<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            TemplateSeeder::class,
            PostSeeder::class,
        ]);
    }
}

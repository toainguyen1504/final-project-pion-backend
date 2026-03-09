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
            // ElearningSeeder::class, // Seeder cho Program, Course, Lesson, Flashcard - chưa cần test vì đang cần tạo data thật
        ]);
    }
}

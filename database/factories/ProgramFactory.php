<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3), // tên chương trình
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'user_id' => 1, // hoặc gán trong seeder, FK tới bảng users
        ];
    }
}

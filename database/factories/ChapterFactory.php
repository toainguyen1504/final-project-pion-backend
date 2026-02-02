<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChapterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(2),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'order' => $this->faker->numberBetween(1, 10),
            'is_preview' => $this->faker->boolean(10),
            'total_duration' => $this->faker->numberBetween(30, 300),
            'total_lessons' => $this->faker->numberBetween(1, 10),
            'course_id' => null, // sẽ gán trong seeder
        ];
    }
}

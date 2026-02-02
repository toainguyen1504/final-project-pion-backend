<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(),
            'intro' => $this->faker->sentence(10),
            'content' => $this->faker->paragraph(5),
            'duration' => $this->faker->numberBetween(5, 60),
            'video_url' => $this->faker->url(),
            'order' => $this->faker->numberBetween(1, 20),
            'is_preview' => $this->faker->boolean(10),
            'is_quiz' => $this->faker->boolean(20),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FlashcardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'front_text' => $this->faker->sentence(5),
            'back_text' => $this->faker->sentence(10),
            'image_url' => $this->faker->imageUrl(200, 200, 'education'),
            'image_prompt' => $this->faker->sentence(6),
            'audio' => $this->faker->url(),
            'lesson_id' => null, // sẽ gán trong seeder
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(),
            'language' => $this->faker->randomElement(['en', 'vi']),
            'thumbnail' => $this->faker->imageUrl(640, 480, 'education'),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'discount_price' => $this->faker->randomFloat(2, 50, 500),
            'level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'status' => $this->faker->randomElement(['draft', 'published']),
            'duration' => $this->faker->numberBetween(10, 100),
            'participants' => $this->faker->numberBetween(0, 1000),
            'total_lessons' => $this->faker->numberBetween(5, 50),
            'benefits' => $this->faker->sentence(6),
            'is_free' => $this->faker->boolean(20),
            'category_id' => 1, // giả định có category
            'user_id' => 1,     // giả định có user tạo
        ];
    }
}

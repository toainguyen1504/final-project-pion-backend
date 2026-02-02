<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Flashcard;

class ElearningSeeder extends Seeder
{
    public function run(): void
    {
        Course::factory(5)->create()->each(function ($course) {
            Chapter::factory(rand(3, 5))->create([
                'course_id' => $course->id,
            ])->each(function ($chapter) {
                Lesson::factory(rand(4, 6))->create([
                    'chapter_id' => $chapter->id,
                ])->each(function ($lesson) {
                    Flashcard::factory(rand(5, 10))->create([
                        'lesson_id' => $lesson->id,
                    ]);
                });
            });
        });
    }
}

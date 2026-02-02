<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Flashcard;

class ElearningSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo vài chương trình học
        Program::factory(2)->create()->each(function ($program) {
            // Mỗi chương trình có vài khóa học
            Course::factory(rand(2, 4))->create([
                'program_id' => $program->id,
            ])->each(function ($course) {
                // Mỗi khóa học có vài bài học
                Lesson::factory(rand(4, 6))->create([
                    'course_id' => $course->id,
                ])->each(function ($lesson) {
                    // Mỗi bài học có vài flashcard
                    Flashcard::factory(rand(5, 10))->create([
                        'lesson_id' => $lesson->id,
                    ]);
                });
            });
        });
    }
}

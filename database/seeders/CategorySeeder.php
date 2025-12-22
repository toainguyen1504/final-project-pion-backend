<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'id' => 1,
                'name' => 'Du học',
                'slug' => 'du-hoc',
                'created_at' => '2025-07-09 03:39:10',
                'updated_at' => '2025-07-09 03:39:10',
            ],
            [
                'id' => 2,
                'name' => 'Du học Trung Quốc',
                'slug' => 'du-hoc-trung-quoc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Học phí',
                'slug' => 'hoc-phi',
                'created_at' => '2025-07-09 03:39:29',
                'updated_at' => '2025-07-09 03:39:29',
            ],
            [
                'id' => 4,
                'name' => 'Lịch khai giảng',
                'slug' => 'lich-khai-giang',
                'created_at' => '2025-07-09 03:39:39',
                'updated_at' => '2025-07-09 03:39:39',
            ],
            [
                'id' => 5,
                'name' => 'Tuyển dụng',
                'slug' => 'tuyen-dung',
                'created_at' => '2025-07-09 03:39:47',
                'updated_at' => '2025-07-09 03:39:47',
            ],
            [
                'id' => 6,
                'name' => 'Khóa học',
                'slug' => 'khoa-hoc',
                'created_at' => '2025-07-09 03:39:56',
                'updated_at' => '2025-07-09 03:39:56',
            ],
            [
                'id' => 7,
                'name' => 'Tin tức',
                'slug' => 'tin-tuc',
                'created_at' => '2025-07-10 09:14:46',
                'updated_at' => '2025-07-10 09:14:46',
            ],
            [
                'id' => 8,
                'name' => 'Hữu ích',
                'slug' => 'huu-ich',
                'created_at' => '2025-07-10 09:14:54',
                'updated_at' => '2025-07-10 09:14:54',
            ],
            [
                'id' => 9,
                'name' => 'Chia sẻ',
                'slug' => 'chia-se',
                'created_at' => '2025-07-10 09:15:03',
                'updated_at' => '2025-07-10 09:15:03',
            ],
            [
                'id' => 10,
                'name' => 'Kỹ năng sống',
                'slug' => 'ky-nang-song',
                'created_at' => '2025-07-10 09:15:09',
                'updated_at' => '2025-07-11 07:46:49',
            ],
        ]);
    }
}

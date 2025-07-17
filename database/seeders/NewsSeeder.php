<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('news')->insert([
            [
                'id' => 1,
                'title' => 'Du học Trung Quốc – Lựa chọn thông minh cho tương lai rộng mở',
                'user_id' => 1,
                'category_id' => 1,
                'created_at' => '2025-07-09 03:41:19',
                'updated_at' => '2025-07-11 04:06:42',
            ],
            [
                'id' => 2,
                'title' => 'Giáo viên Nước Ngoài',
                'user_id' => 1,
                'category_id' => 5,
                'created_at' => '2025-07-09 03:43:35',
                'updated_at' => '2025-07-11 07:50:07',
            ],
            [
                'id' => 3,
                'title' => '5 Mẹo Giữ Cho Bàn Làm Việc Gọn Gàng',
                'user_id' => 1,
                'category_id' => 10,
                'created_at' => '2025-07-11 01:06:49',
                'updated_at' => '2025-07-11 07:48:14',
            ],
            [
                'id' => 4,
                'title' => 'Vì Sao Tập Thể Dục Buổi Sáng Tốt Hơn?',
                'user_id' => 1,
                'category_id' => 11,
                'created_at' => '2025-07-11 07:51:05',
                'updated_at' => '2025-07-11 07:51:05',
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\PostContent;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run()
    {
        $posts = [
            [
                'title' => 'Du học Trung Quốc – Lựa chọn thông minh cho tương lai rộng mở',
                'user_id' => 1,
                'category_id' => 1,
                'seo_title' => 'Du học Trung Quốc - Tương lai rộng mở',
                'seo_description' => 'Khám phá cơ hội học tập tại Trung Quốc với chi phí hợp lý và chất lượng đào tạo cao.',
                'seo_keywords' => 'du học, trung quốc, tương lai',
                'seo_meta' => json_encode([
                    'twitter:card' => 'summary_large_image'
                ]),
                'status' => 'published',
                'publish_at' => now(),
            ],
            [
                'title' => 'Giáo viên Nước Ngoài',
                'user_id' => 1,
                'category_id' => 5,
                'seo_title' => 'Giáo viên bản ngữ chất lượng cao',
                'seo_description' => 'Cơ hội học với giáo viên nước ngoài giàu kinh nghiệm.',
                'seo_keywords' => 'giáo viên, bản ngữ, tiếng anh',
                'seo_meta' => json_encode([
                    'twitter:card' => 'summary_large_image'
                ]),
                'status' => 'published',
                'publish_at' => now(),
            ]
        ];

        foreach ($posts as $data) {
            $post = Post::create($data);

            PostContent::create([
                'post_id' => $post->id,
                'content_html' => '<p>Đây là nội dung bài viết mẫu với <strong>CKEditor</strong>.</p>',
                'content_json' => null
            ]);
        }
    }
}

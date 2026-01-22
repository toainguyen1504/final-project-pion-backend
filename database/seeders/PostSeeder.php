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
                'title' => 'Du học Trung Quốc – Lựa chọn thông minh cho tương lai rộng mở false',
                'user_id' => 1,
                'category_id' => 1,
                'seo_title' => 'Du học Trung Quốc - Tương lai rộng mở Du học Trung Quốc - Tương lai rộng mở Du học Trung Quốc - Tương lai rộng mở',
                'seo_description' => 'Khám phá cơ hội học tập tại Trung Quốc với chi phí hợp lý và chất lượng đào tạo cao.',
                'seo_keywords' => 'du học, trung quốc, tương lai, trung quốc, tương lai',
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
            ],
            [
                'title' => 'Học bổng du học Anh quốc năm 2025',
                'user_id' => 1,
                'category_id' => 1,
                'seo_title' => 'Cập nhật học bổng du học Anh 2025',
                'seo_description' => 'Danh sách học bổng hấp dẫn cho sinh viên quốc tế tại Anh.',
                'seo_keywords' => 'học bổng, du học, anh quốc',
                'seo_meta' => json_encode(['twitter:card' => 'summary_large_image']),
                'status' => 'published',
                'publish_at' => now(),
            ],
            [
                'title' => 'Bí quyết luyện thi IELTS đạt điểm cao',
                'user_id' => 1,
                'category_id' => 3,
                'seo_title' => 'Hướng dẫn luyện thi IELTS hiệu quả',
                'seo_description' => 'Chiến lược luyện thi IELTS giúp bạn đạt band 7.0 trở lên.',
                'seo_keywords' => 'ielts, luyện thi, tiếng anh',
                'seo_meta' => json_encode(['twitter:card' => 'summary_large_image']),
                'status' => 'published',
                'publish_at' => now(),
            ],
            [
                'title' => 'Du học Nhật Bản – Cơ hội học tập và làm việc song song',
                'user_id' => 1,
                'category_id' => 2,
                'seo_title' => 'Chương trình du học Nhật Bản vừa học vừa làm',
                'seo_description' => 'Tìm hiểu chương trình du học Nhật giúp sinh viên tiết kiệm chi phí.',
                'seo_keywords' => 'du học, nhật bản, việc làm',
                'seo_meta' => json_encode(['twitter:card' => 'summary_large_image']),
                'status' => 'published',
                'visibility' => 'public',
                'publish_at' => now(),
            ],
            [
                'title' => 'Tại sao nên chọn ngành Công nghệ thông tin?',
                'user_id' => 1,
                'category_id' => 4,
                'seo_title' => 'Ngành CNTT – Lựa chọn của tương lai',
                'seo_description' => 'Công nghệ thông tin đang là ngành học có nhu cầu nhân lực cao nhất.',
                'seo_keywords' => 'công nghệ, việc làm, it',
                'seo_meta' => json_encode(['twitter:card' => 'summary_large_image']),
                'status' => 'published',
                'visibility' => 'public',
                'publish_at' => now(),
            ],
            [
                'title' => 'Cách chọn trường phù hợp khi du học',
                'user_id' => 1,
                'category_id' => 1,
                'seo_title' => 'Hướng dẫn chọn trường du học hợp lý',
                'seo_description' => 'Các tiêu chí quan trọng giúp bạn chọn trường phù hợp khi du học.',
                'seo_keywords' => 'chọn trường, du học, tư vấn',
                'seo_meta' => json_encode(['twitter:card' => 'summary_large_image']),
                'status' => 'published',
                'visibility' => 'public',
                'publish_at' => now(),
            ],
            [
                'title' => 'Hướng dẫn xin visa du học Mỹ chi tiết',
                'user_id' => 1,
                'category_id' => 2,
                'seo_title' => 'Quy trình xin visa du học Mỹ',
                'seo_description' => 'Từng bước chuẩn bị hồ sơ và phỏng vấn xin visa du học Mỹ thành công.',
                'seo_keywords' => 'visa, du học, mỹ',
                'seo_meta' => json_encode(['twitter:card' => 'summary_large_image']),
                'status' => 'published',
                'visibility' => 'public',
                'publish_at' => now(),
            ],
            [
                'title' => '5 kỹ năng mềm giúp bạn thành công khi du học',
                'user_id' => 1,
                'category_id' => 3,
                'seo_title' => 'Kỹ năng cần thiết cho du học sinh',
                'seo_description' => 'Các kỹ năng giúp bạn thích nghi và học tập hiệu quả ở môi trường mới.',
                'seo_keywords' => 'kỹ năng, du học, sinh viên',
                'seo_meta' => json_encode(['twitter:card' => 'summary_large_image']),
                'status' => 'published',
                'visibility' => 'public',
                'publish_at' => now(),
            ],
            [
                'title' => 'Top quốc gia du học phổ biến năm 2025',
                'user_id' => 1,
                'category_id' => 1,
                'seo_title' => 'Những điểm đến du học được yêu thích nhất',
                'seo_description' => 'Danh sách các quốc gia hàng đầu cho sinh viên quốc tế trong năm 2025.',
                'seo_keywords' => 'du học, quốc gia, xu hướng',
                'seo_meta' => json_encode(['twitter:card' => 'summary_large_image']),
                'status' => 'published',
                'visibility' => 'public',
                'publish_at' => now(),
            ],
            [
                'title' => 'Cách viết bài luận du học ấn tượng',
                'user_id' => 1,
                'category_id' => 2,
                'seo_title' => 'Bí quyết viết bài luận du học đạt điểm cao',
                'seo_description' => 'Chia sẻ các bước giúp bạn viết bài luận hấp dẫn và thuyết phục.',
                'seo_keywords' => 'bài luận, du học, viết essay',
                'seo_meta' => json_encode(['twitter:card' => 'summary_large_image']),
                'status' => 'published',
                'visibility' => 'public',
                'publish_at' => now(),
            ],
            [
                'title' => 'Du học Hàn Quốc – Cơ hội học tập và việc làm',
                'user_id' => 1,
                'category_id' => 1,
                'seo_title' => 'Du học Hàn Quốc năm 2025',
                'seo_description' => 'Tìm hiểu cơ hội học tập và làm việc lâu dài tại Hàn Quốc.',
                'seo_keywords' => 'du học, hàn quốc, việc làm',
                'seo_meta' => json_encode(['twitter:card' => 'summary_large_image']),
                'status' => 'published',
                'visibility' => 'public',
                'publish_at' => now(),
            ],
            [
                'title' => 'Top ngành học có cơ hội việc làm cao nhất',
                'user_id' => 1,
                'category_id' => 4,
                'seo_title' => 'Ngành học xu hướng 2025',
                'seo_description' => 'Danh sách những ngành học có nhu cầu nhân lực lớn nhất hiện nay.',
                'seo_keywords' => 'ngành học, việc làm, hướng nghiệp',
                'seo_meta' => json_encode(['twitter:card' => 'summary_large_image']),
                'status' => 'published',
                'visibility' => 'public',
                'publish_at' => now(),
            ],

            [
                'title' => 'Du học Canada – Cánh cửa đến tương lai toàn cầu',
                'user_id' => 1,
                'category_id' => 1,
                'seo_title' => 'Cơ hội du học Canada 2025',
                'seo_description' => 'Khám phá chương trình học và cơ hội định cư tại Canada.',
                'seo_keywords' => 'du học, canada, định cư',
                'seo_meta' => json_encode(['twitter:card' => 'summary_large_image']),
                'status' => 'published',
                'visibility' => 'public',
                'publish_at' => now(),
            ],
            [
                'title' => 'Học tiếng Anh giao tiếp cho người mới bắt đầu',
                'user_id' => 1,
                'category_id' => 3,
                'seo_title' => 'Khóa học tiếng Anh cơ bản',
                'seo_description' => 'Lộ trình học tiếng Anh giao tiếp dễ hiểu và hiệu quả cho người mới.',
                'seo_keywords' => 'tiếng anh, giao tiếp, học ngoại ngữ',
                'seo_meta' => json_encode(['twitter:card' => 'summary_large_image']),
                'status' => 'published',
                'visibility' => 'public',
                'publish_at' => now(),
            ],
            [
                'title' => 'Phương pháp học tập hiệu quả cho sinh viên đại học',
                'user_id' => 1,
                'category_id' => 4,
                'seo_title' => 'Cách học hiệu quả cho sinh viên',
                'seo_description' => 'Những mẹo giúp sinh viên tối ưu thời gian và kết quả học tập.',
                'seo_keywords' => 'sinh viên, học tập, kỹ năng',
                'seo_meta' => json_encode(['twitter:card' => 'summary_large_image']),
                'status' => 'published',
                'visibility' => 'public',
                'publish_at' => now(),
            ],
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

## API Laravel - NOTE
Những lỗi cần sửa (QUAN TRỌNG) DATABASE
- posts -> vừa có category_id vừa có category_post  (chọn 1 post nhiều category -> lấy category_post, XÓA category_id trong posts), làm sau khi xong learning
- lessons.video_url -> thêm video_provider và video_id, vì nghiệp vụ là Video từ Youtube
- flashcards.order -> order là keyword SQL vì vậy nên đổi thành  sort_order
- Với lesson sau này sẽ tối ưu là tạo thêm table "videos"


Những bảng còn thiếu cho PION, Database
- comments (Blog SEO thường có comment), tags (SEO blog thường cần: tags post_tag)

-  flashcard_reviews -> dùng Spaced Repetition Algorithm (SM-2), có 3 field chính "ease_factor, interval, next_review_at"

## API Laravel - NOTE
Những lỗi cần sửa (QUAN TRỌNG) DATABASE
- posts -> vừa có category_id vừa có category_post  (chọn 1 post nhiều category -> lấy category_post, XÓA category_id trong posts), làm sau khi xong learning
- lessons.video_url -> thêm video_provider và video_id, vì nghiệp vụ là Video từ Youtube
- flashcards.order -> order là keyword SQL vì vậy nên đổi thành  sort_order
- Với lesson sau này sẽ tối ưu là tạo thêm table "videos"

Những bảng còn thiếu cho PION, Database
- comments (Blog SEO thường có comment), tags (SEO blog thường cần: tags post_tag)
-  flashcard_reviews -> dùng Spaced Repetition Algorithm (SM-2), có 3 field chính "ease_factor, interval, next_review_at"

Với API response, nên đồng bộ dùng format: status, message, data, meta

Điểm tốt: Tổng thể project đang ở mức rất tốt cho một hệ thống Laravel API thực tế (không chỉ là đồ án). Đã xử lý nhiều thứ nâng cao như:
- transaction -> không bị data corruption
- reorder logic -> xử lý reorder chuẩn LMS logic., có delete reorder: Đây là cách rất tốt để giữ thứ tự.
- media processing
- SEO post scheduling
- RBAC user control
- bulk operations
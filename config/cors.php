<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://pion.edu.vn'], // Hoặc ['https://tenmienfe.com'] nếu cần bảo mật

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];

// ✅ Lựa chọn 2: Sử dụng mặc định, không cần chỉnh
// Nếu bạn không cần cấu hình đặc biệt:
// Laravel vẫn xử lý CORS mặc định với header cơ bản khi FE gọi API.
// Nếu FE fetch bị lỗi CORS, hãy tiếp tục sử dụng lựa chọn 1 để bật toàn bộ.
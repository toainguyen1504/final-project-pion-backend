<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang người dùng - Blog PION</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">

        <!-- Phần tiêu đề -->
        <h2 class="mb-4">📚 All Blogs - Pion Academy</h2>

        <!-- Nút xem tất cả bài viết -->
        <div class="text-end mb-4">
            <a href="{{ route('client.post.list') }}" class="btn btn-primary">
                Xem tất cả bài viết
            </a>
        </div>

        <!-- Danh mục -->
        <div class="mb-4">
            <h5>Danh mục</h5>
            <ul class="list-group list-group-horizontal flex-wrap">
                <li class="list-group-item">Tất cả</li>
                @foreach ($categories as $category)
                    <li class="list-group-item">{{ $category->name }}</li>
                @endforeach
            </ul>

        </div>

        <!-- Danh sách bài viết -->
        <div>
            <h5>Tin mới</h5>
            <div class="row">
                @foreach ($posts as $post)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title">{{ $post->title }}</h6>
                                <p class="card-text text-muted small">Danh mục:
                                    {{ optional($post->category)->name ?? 'Không rõ' }}</p>
                                <p class="card-text">
                                    {{-- {{ Str::limit(strip_tags($post->content->content_html ?? ''), 100) }}</p> --}}
                                    <a href="#!" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</body>

</html>

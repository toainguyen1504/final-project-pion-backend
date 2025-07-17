<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>PION - Danh sách bài viết</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Source Sans Pro', sans-serif;
        }

        h2 {
            color: #ce232d;
        }

        .card-title {
            color: #212529;
        }

        .btn-outline-danger:hover {
            background-color: #ce232d;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <h2 class="mb-4 text-center">📰 PION - Danh sách bài viết</h2>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            @forelse ($posts as $post)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">{{ $post->title }}</h5>
                            <p class="card-text small text-muted">
                                Danh mục: {{ optional($post->category)->name ?? 'Không có' }}<br>
                                Ngày đăng: {{ $post->created_at->format('d/m/Y') }}
                            </p>
                            <a href="{{ route('preview.post.detail', $post->id) }}"
                                class="btn btn-outline-danger btn-sm">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col">
                    <div class="alert alert-light border text-center py-4">
                        <i class="bi bi-newspaper fa-2x d-block mb-3 text-danger"></i>
                        Không có bài viết nào được đăng.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

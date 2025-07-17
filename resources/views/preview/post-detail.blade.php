@extends('layouts.preview') {{-- layout riêng nếu bạn đang tách --}}
@section('title', 'Preview Tin tức')

@section('styles')
    <style>
        nav a.nav-link:hover {
            color: #ce232d !important;
            text-decoration: underline;
        }

        .btn-outline-danger:hover {
            background-color: #ce232d;
            border-color: #ce232d;
            color: #fff;
        }

        .container {
            min-height: 1123px;
        }

        .content-wrapper {
            height: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        {{-- TOC BÊN TRÁI --}}
        {{-- SIDEBAR bên trái danh sách bài viết --}}
        <div class="col-md-3 border-end pe-3">
            <nav class="sticky-top pt-4" style="top: 80px;">
                <h5 class="text-danger mb-3">📚 Bài viết khác</h5>
                <ul class="nav flex-column small">
                    @foreach ($posts as $item)
                        <li class="mb-1">
                            <a href="{{ route('preview.post.detail', $item->id) }}" class="nav-link text-dark d-block">
                                {{ \Illuminate\Support\Str::limit($item->title, 60) }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <hr>

                {{-- Nút quay lại --}}
                <a href="{{ route('preview.post.list') }}"
                    class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left-circle"></i> Quay về danh sách bài viết
                </a>
            </nav>
        </div>

        {{-- NỘI DUNG BÊN PHẢI --}}
        <div class="container col-md-9 pt-4">
            <div class="content-wrapper bg-white p-5 border rounded shadow-sm">
                <h1 class="mb-3 fw-bold">{{ $post->title }}</h1>
                <div class="text-muted fst-italic mb-4">
                    Danh mục: {{ optional($post->category)->name ?? 'Không có danh mục' }}
                </div>

                <div class="post-content">
                    {!! $post->content?->content_html ?? '<p class="text-muted">[Chưa có nội dung]</p>' !!}
                </div>
            </div>
        </div>
    </div>
@endsection

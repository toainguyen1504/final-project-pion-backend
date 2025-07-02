@extends('layouts.app')

@section('content')
    <h2>{{ $news->title }}</h2>
    <p><strong>Danh mục:</strong> {{ $news->category->name ?? 'Không xác định' }}</p>
    <p><strong>Người tạo:</strong> {{ $news->user->name }}</p>
    <p><strong>Ngày đăng:</strong> {{ $news->created_at->format('d/m/Y H:i') }}</p>

    <hr>
    <div>
        {!! $news->content->content_html ?? '<em>Không có nội dung</em>' !!}
    </div>

    <a href="{{ route('news.index') }}" class="btn btn-secondary mt-3">← Quay về danh sách</a>
@endsection

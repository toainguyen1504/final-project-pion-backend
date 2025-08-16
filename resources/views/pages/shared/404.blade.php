@extends('layouts.shared.404')

@section('title', 'Không tìm thấy trang')

@section('content')
    <div class="d-flex flex-column align-items-center justify-content-center text-center" style="min-height: 70vh;">
        <h1 class="display-1 fw-bold text-danger">Oops!</h1>
        <h2 class="my-3">Trang bạn cần không được tìm thấy!</h2>
        <p class="text-muted mb-4">
            Liên kết có thể đã bị thay đổi, xoá hoặc bạn đang tìm ở nơi sai.
        </p>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-dark px-4 py-2">
            <i class="bi bi-arrow-left me-2"></i> Quay về Trang quản trị
        </a>
    </div>
@endsection

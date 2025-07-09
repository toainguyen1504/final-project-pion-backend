@extends('layouts.error')

@section('title', 'Không tìm thấy trang')

@section('content')
    <div class="d-flex flex-column align-items-center justify-content-center text-center" style="min-height: 70vh;">
        <h1 class="display-1 fw-bold text-danger">404</h1>
        <h2 class="mb-3">Không tìm thấy trang</h2>
        <p class="text-muted mb-4">
            Trang bạn đang cố truy cập không tồn tại hoặc đã bị xóa.<br>
            Vui lòng kiểm tra lại đường dẫn hoặc quay về trang chính.
        </p>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-dark px-4 py-2">
            <i class="bi bi-arrow-left me-2"></i> Quay về Trang quản trị
        </a>
    </div>
@endsection

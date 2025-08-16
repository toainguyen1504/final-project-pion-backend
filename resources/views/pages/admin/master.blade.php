@extends('layouts.app')

@section('title', '- Tổng quan')

@section('content')
    <div class="container">
        <h1 class="mb-4 fs-2">Chào mừng bạn đến trang quản lý <strong class="text-danger">PION ACADEMY</strong></h1>

        {{-- Overview --}}
        <div class="row">
            <!-- Consultations -->
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Yêu cầu tư vấn mới</p>
                                <h3 class="mb-0 font-weight-bold">{{ number_format($totalConsultations) }}</h3>
                            </div>
                            <div class="bg-warning text-white p-3 rounded-circle">
                                <i class="fas fa-headset fa-lg"></i>
                            </div>
                        </div>
                        <p class="text-success mt-3 mb-0">
                            <i class="fas fa-arrow-up"></i> 5% so với tháng trước
                        </p>
                    </div>
                </div>
            </div>

            <!-- Posts -->
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Tổng Tin tức</p>
                                <h3 class="mb-0 font-weight-bold">{{ number_format($totalPosts) }}</h3>
                            </div>
                            <div class="bg-primary text-white p-3 rounded-circle">
                                <i class="fas fa-newspaper fa-lg"></i>
                            </div>
                        </div>
                        <p class="text-success mt-3 mb-0">
                            <i class="fas fa-arrow-up"></i> 12% so với tháng trước
                        </p>
                    </div>
                </div>
            </div>

            <!-- Category -->
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Tổng Danh mục</p>
                                <h3 class="mb-0 font-weight-bold">{{ number_format($totalCategories) }}</h3>
                            </div>
                            <div class="bg-success text-white p-3 rounded-circle">
                                <i class="fas fa-th-large fa-lg"></i>
                            </div>
                        </div>
                        <p class="text-success mt-3 mb-0">
                            <i class="fas fa-arrow-up"></i> 3% so với tháng trước
                        </p>
                    </div>
                </div>
            </div>

            <!--  viewer -->
            {{-- <div class="col-md-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Tổng lượt xem</p>
                                <h3 class="mb-0 font-weight-bold">24.8k</h3>
                            </div>
                            <div class="bg-info text-white p-3 rounded-circle">
                                <i class="fas fa-eye fa-lg"></i>
                            </div>
                        </div>
                        <p class="text-success mt-3 mb-0">
                            <i class="fas fa-arrow-up"></i> 22% so với tháng trước
                        </p>
                    </div>
                </div>
            </div> --}}
        </div>

        {{-- Manage category and posts - Hidden if it is staff ads --}}
        @if (!Auth::user()?->hasRole('staffads'))
            <div class="row mb-4">
                <!-- Top categories -->
                <div class="col-lg-6 mb-4 d-flex align-items-stretch">
                    <div class="card shadow-sm w-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Danh mục nổi bật</h5>
                            <a href="{{ route('admin.categories.index') }}"
                                class="btn-link text-primary small text-nowrap ms-auto">Xem
                                tất cả</a>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse ($topCategories as $category)
                                <a href="{{ route('admin.posts.index') }}?category={{ $category->id }}"
                                    class="list-group-item list-group-item-action d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded me-3">
                                        <i class="fas fa-folder"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ $category->name }}</div>
                                        <small class="text-muted">{{ $category->posts_count }} bài viết</small>
                                    </div>
                                    <i class="fas fa-chevron-right text-primary"></i>
                                </a>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-folder-open fa-2x mb-2"></i>
                                    <p class="mb-0">Chưa có danh mục nào được tạo.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>


                <!-- Top posts -->
                <div class="col-lg-6 mb-4 d-flex align-items-stretch">
                    <div class="card shadow-sm w-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Tin tức nổi bật</h5>
                            <a href="{{ route('admin.posts.index') }}"
                                class="btn-link text-primary small text-nowrap ms-auto">Xem tất
                                cả</a>
                        </div>
                        <div class="card-body">
                            @forelse ($latestPosts as $posts)
                                <div class="mb-4 border-bottom pb-3">
                                    <h6 class="fw-semibold">{{ $posts->title }}</h6>
                                    <small class="text-muted">
                                        {{ $posts->created_at->diffForHumans() }} -
                                        <span
                                            class="text-primary">{{ $posts->category->name ?? 'Không có danh mục' }}</span>
                                    </small>
                                    <p class="mt-2 text-muted">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($posts->content->content_html ?? ''), 120) }}
                                    </p>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-newspaper fa-2x mb-2"></i>
                                    <p class="mb-0">Chưa có bài viết nào được đăng.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        @endif

        {{-- Manage consultations - Show if it is staff ads --}}
        {{-- @if (Auth::user()?->hasRole('staffads'))
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Yêu cầu tư vấn gần đây</h5>
                    <a href="{{ route('admin.consultations.index') }}"
                        class="btn-link text-primary small text-nowrap ms-auto">
                        Xem tất cả
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Họ tên</th>
                                <th>SĐT</th>
                                <th>Email</th>
                                <th>Nội dung</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Lê+Văn+C&background=6f42c1&color=fff"
                                            alt="Lê Văn C" class="rounded-circle me-2" width="40" height="40">
                                        <span class="fw-semibold">Lê Văn C</span>
                                    </div>
                                </td>
                                <td>0905123456</td>
                                <td><small class="text-muted">levanc@yahoo.com</small></td>
                                <td>Cần tư vấn về việc học AI khi không có nền tảng toán</td>
                                <td><small class="text-muted">3 giờ trước</small></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif --}}
    </div>
@endsection

@stack('scripts-admin')

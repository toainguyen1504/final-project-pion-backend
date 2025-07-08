@extends('layouts.app')

@section('content')
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="mb-0 fs-2">Quản lý bài viết</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.news.create') }}" class="btn btn-dark" style="width: 40%;">+ Thêm bài viết mới</a>
        </div>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th class="text-center">STT</th>
                <th class="px-3">Tiêu đề</th>
                <th class="px-3">Danh mục</th>
                <th class="text-center">Bài viết</th>
                <th class="text-center">Người tạo</th>
                <th class="text-center">Ngày tạo</th>
                <th class="text-center">Cập nhật</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($news as $index => $post)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="px-3">{{ $post->title }}</td>
                    <td class="px-3">{{ optional($post->category)->name ?? '-' }}</td>
                    <td class="text-center">
                        {{-- {{ route('admin.news.show', $post->id) }} --}}
                        <button class="btn btn-dark btn-sm w-100" data-bs-toggle="modal" data-bs-target="#modalViewNews"
                            data-content="{{ $post->content?->content_html ?? '[Chưa có nội dung]' }}"
                            data-title="{{ $post->title }}"
                            data-category="{{ optional($post->category)->name ?? 'Không có danh mục' }}">
                            Xem
                        </button>

                    </td>
                    <td class="text-center">{{ $post->user->name }}</td>
                    <td class="text-center">{{ $post->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-center">{{ $post->updated_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="d-flex justify-content-center gap-3">
                            {{-- nút edit --}}
                            <a href="{{ route('admin.news.edit', $post->id) }}" class="btn btn-warning btn-sm px-3">Sửa</a>

                            <!-- Nút Xóa kích hoạt modal -->
                            <button type="button" class="btn btn-danger btn-sm px-3" data-bs-toggle="modal"
                                data-bs-target="#modalDeleteNews" data-id="{{ $post->id }}"
                                data-title="{{ $post->title }}">
                                {{-- <i class="bi bi-trash3 me-1"></i> <strong>XÓA</strong> --}}
                                Xóa
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    {{-- MODAL view content --}}
    <div class="modal fade" id="modalViewNews" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 mx-auto" style="width: 794px;">
                    <h5 class="modal-title">📖 Xem bài viết</h5>
                    <button type="button" class="btn-close red-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="post-content bg-white p-5 mx-auto"
                        style="width: 794px; min-height: 1123px; border: 1px solid #dee2e6; border-radius: 4px; box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);"
                        id="modal-view-content">
                        {{-- Content --}}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal xác nhận xóa bài viết -->
    <div class="modal fade" id="modalDeleteNews" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="margin-top: 80px;">
            <div class="modal-content border-0 shadow">
                <form method="POST" id="delete-news-form">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header bg-danger bg-opacity-10 border-0">
                        <h5 class="modal-title text-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Xác nhận xóa bài viết
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>

                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn <strong class="text-danger mx-2">XÓA</strong> bài viết sau không?</p>
                        <div class="border rounded p-3 small bg-light">
                            <span id="delete-title" class="fw-bold text-dark"></span>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger px-4">Xóa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    {{-- Mở modal xem trước --}}
    <script>
        const modalView = document.getElementById('modalViewNews');

        modalView.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const html = button.getAttribute('data-content');
            const container = document.getElementById('modal-view-content');

            const title = button.getAttribute('data-title') || '[Không có tiêu đề]';
            const category = button.getAttribute('data-category') || '[Không có danh mục]';

            container.innerHTML = `
                <h1 class="mb-3 fw-bold">${title}</h1>
                <div class="text-muted fst-italic mb-4">Danh mục: ${category}</div>
                ${html}
            `;

        });
    </script>

    {{-- Mở modal xóa --}}
    <script>
        const deleteModal = document.getElementById('modalDeleteNews');
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const postId = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');

            const form = document.getElementById('delete-news-form');
            form.action = `/admin/news/${postId}`;

            document.getElementById('delete-title').textContent = title;
        });
    </script>
@endsection

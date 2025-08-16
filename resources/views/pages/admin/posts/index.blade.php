@extends('layouts.app')

@section('content')
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h1 class="mb-0 fs-2">Quản lý bài viết</h1>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <a href="{{ route('admin.posts.create') }}"
                class="btn btn-dark d-flex align-items-center justify-content-center gap-2" style="width: 40%;">
                <i class="fas fa-plus"></i>
                <span>Thêm bài viết mới</span>
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle table-striped table-hover py-2" id="posts-table">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 50px;">STT</th>
                    <th style="min-width: 200px;">Tiêu đề</th>
                    <th style="max-width: 160px;">Danh mục</th>
                    <th class="text-center" style="max-width: 140px;">Người tạo</th>
                    <th class="text-center" style="width: 140px;">Ngày tạo</th>
                    <th class="text-center" style="width: 140px;">Cập nhật</th>
                    <th class="text-center" style="width: 160px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $index => $post)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>

                        <td class="text-truncate" style="max-width: 240px;">
                            <a href="#" class="title-link-custom" data-bs-toggle="modal"
                                data-bs-target="#modalViewPost"
                                data-content="{{ $post->content?->content_html ?? '[Chưa có nội dung]' }}"
                                data-title="{{ $post->title }}"
                                data-category="{{ optional($post->category)->name ?? 'Không có danh mục' }}"
                                title="{{ $post->title }}">
                                {{ \Illuminate\Support\Str::limit($post->title, 80) }}
                            </a>
                        </td>

                        <td class="text-truncate" style="max-width: 180px;" title="{{ optional($post->category)->name }}">
                            {{ optional($post->category)->name ?? '-' }}
                        </td>

                        <td class="text-truncate text-center" style="max-width: 140px;" title="{{ $post->user->name }}">
                            {{ $post->user->name }}
                        </td>

                        <td class="text-center">{{ $post->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">{{ $post->updated_at->format('d/m/Y H:i') }}</td>

                        <td>
                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <a href="{{ route('admin.posts.edit', $post->id) }}"
                                    class="btn btn-warning btn-sm py-1 px-3">Sửa</a>
                                <button type="button" class="btn btn-danger btn-sm py-1 px-3" data-bs-toggle="modal"
                                    data-bs-target="#modalDeletePost" data-id="{{ $post->id }}"
                                    data-title="{{ $post->title }}">
                                    Xóa
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-newspaper fa-2x mb-2 d-block"></i>
                            <span>Chưa có bài viết nào được đăng.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- View content modal--}}
    <div class="modal fade" id="modalViewPost" tabindex="-1" aria-hidden="true">
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


    <!-- Confirm delete post modal -->
    <div class="modal fade" id="modalDeletePost" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="margin-top: 80px;">
            <div class="modal-content border-0 shadow">
                <form method="POST" id="delete-post-form">
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
    {{-- Open preview modal --}}
    <script>
        const modalView = document.getElementById('modalViewPost');

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

    {{-- Open delete modal  --}}
    <script>
        const deleteModal = document.getElementById('modalDeletePost');
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const postId = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');

            const form = document.getElementById('delete-post-form');
            form.action = `/posts/${postId}`;

            document.getElementById('delete-title').textContent = title;
        });
    </script>

    {{-- Script Posts DataTables --}}
    <script>
        $(document).ready(function() {
            $('#posts-table').DataTable({
                lengthMenu: [
                    [6, 12, 18, -1],
                    [6, 12, 18, "Tất cả"]
                ],
                pageLength: 6,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
                },
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true
            });
        });
    </script>
@endsection

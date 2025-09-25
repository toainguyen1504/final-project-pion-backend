@extends('layouts.app')

@section('content')
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h1 class="mb-0 fs-2">Quản lý bài viết</h1>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <a href="{{ route('admin.posts.create') }}"
                class="btn btn-dark d-flex align-items-center justify-content-center gap-2" style="width: 40%;">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Thêm bài viết mới</span>
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle table-striped table-hover" id="posts-table">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 50px;">STT</th>
                    <th style="min-width: 240px;">Tiêu đề</th>
                    <th style="min-width: 240px;">Sapo</th>
                    <th style="max-width: 160px; min-width: 120px">Chuyên mục</th>
                    <th class="text-center" style="min-width: 120px;">Trạng thái</th>
                    <th class="text-center" style="min-width: 120px;">Hiển thị</th>
                    <th style="min-width: 160px;">Tiêu đề (SEO)</th>
                    <th style="min-width: 160px;">Mô tả (SEO)</th>
                    <th style="min-width: 160px;">Keyword (SEO)</th>
                    <th class="text-center" style="min-width: 140px;">Ngày tạo</th>
                    <th class="text-center" style="min-width: 140px;">Cập nhật</th>
                    <th class="text-center" style="min-width: 160px;">Hành động</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($posts as $index => $post)
                    <tr>
                        <td class="text-center" style="width: 50px;">{{ $index + 1 }}</td>

                        <td class="text-truncate" style="min-width: 240px; max-width: 240px;"
                            title="{{ $post->title ?? '------' }}">
                            <a href="#" class="title-link-custom" data-bs-toggle="modal"
                                data-bs-target="#modalViewPost"
                                data-content="{{ $post->content?->content_html ?? '[Không có nội dung]' }}"
                                data-title="{{ $post->title ?? '[Không có tiêu đề]' }}"
                                data-sapo="{{ $post->sapo_text ?? '[Không có Sapo]' }}"
                                data-category="{{ $post->categories->pluck('name')->implode(', ') ?: '[Không có danh mục]' }}">
                                {{ \Illuminate\Support\Str::limit($post->title ?? '------', 80) }}
                            </a>
                        </td>

                        <td class="text-truncate" style="min-width: 240px; max-width: 240px;"
                            title="{{ $post->sapo_text ?? '------' }}">
                            {{ \Illuminate\Support\Str::limit($post->sapo_text ?? '------', 80) }}
                        </td>

                        <td class="text-truncate" style="min-width: 120px; max-width: 160px;"
                            title="{{ $post->categories->pluck('name')->implode(', ') ?: '------' }}">
                            @php
                                $categoryNames = $post->categories->pluck('name')->toArray();
                                $categoryDisplay = implode(', ', $categoryNames);
                            @endphp
                            {{ \Illuminate\Support\Str::limit($categoryDisplay ?: '------', 50, '...') }}
                        </td>

                        <td class="text-center" style="min-width: 120px;">{{ ucfirst($post->status ?? '------') }}
                        </td>
                        <td class="text-center" style="min-width: 120px;">
                            {{ ucfirst($post->visibility ?? '------') }}</td>

                        <td class="text-truncate" style="min-width: 160px; max-width: 240px;"
                            title="{{ $post->seo_title ?? '------' }}">
                            {{ \Illuminate\Support\Str::limit($post->seo_title ?? '------', 80) }}
                        </td>

                        <td class="text-truncate" style="min-width: 160px; max-width: 240px;"
                            title="{{ $post->seo_description ?? '------' }}">
                            {{ \Illuminate\Support\Str::limit($post->seo_description ?? '------', 80) }}
                        </td>

                        <td class="text-truncate" style="min-width: 160px; max-width: 240px;"
                            title="{{ $post->seo_keywords ?? '------' }}">
                            {{ \Illuminate\Support\Str::limit($post->seo_keywords ?? '------', 80) }}
                        </td>

                        <td class="text-center" style="min-width: 140px;">
                            {{ $post->created_at ? $post->created_at->format('d/m/Y H:i') : '------' }}
                        </td>

                        <td class="text-center" style="min-width: 140px;">
                            {{ $post->updated_at ? $post->updated_at->format('d/m/Y H:i') : '------' }}
                        </td>

                        <td class="text-center" style="min-width: 160px;">
                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <a href="{{ route('admin.posts.edit', $post->id) }}"
                                    class="btn btn-warning btn-sm py-1 px-3">Sửa</a>
                                <button type="button" class="btn btn-danger btn-sm py-1 px-3" data-bs-toggle="modal"
                                    data-bs-target="#modalDeletePost" data-id="{{ $post->id }}"
                                    data-title="{{ $post->title ?? '------' }}">
                                    Xóa
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted py-4">
                            <i class="fas fa-newspaper fa-2x mb-2 d-block"></i>
                            <span>Không có bài viết nào được đăng.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    {{-- View content modal --}}
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

@push('scripts')
    {{-- Open preview modal --}}
    <script>
        const modalView = document.getElementById('modalViewPost');

        modalView.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const html = button.getAttribute('data-content');
            const container = document.getElementById('modal-view-content');

            const title = button.getAttribute('data-title') || '[Không có tiêu đề]';
            const category = button.getAttribute('data-category') || '[Không có danh mục]';
            const sapo = button.getAttribute('data-sapo') || '[Không có sapo]';

            container.innerHTML = `
                <h1 class="mb-3">${title}</h1>
                <div class="text-muted fst-italic mb-4">Danh mục: ${category}</div>
                <div class="mb-4">${sapo}</div>
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
        $.fn.dataTable.ext.errMode = 'none'; //off warming when no data

        $(document).ready(function() {
            var table = $('#posts-table').DataTable({
                scrollX: true,

                dom: '<"top"Blfr>t<"bottom"ip>',
                buttons: [{
                        extend: 'colvis',
                        text: '<i class="fas fa-columns me-1"></i>&nbsp;Tùy chỉnh cột'
                    },
                    {
                        text: '<i class="fas fa-list-ol me-1"></i>&nbsp;Số dòng',
                        extend: 'collection',
                        autoClose: true,
                        buttons: [{
                                text: '4 dòng',
                                action: function() {
                                    table.page.len(4).draw();
                                }
                            },
                            {
                                text: '6 dòng',
                                action: function() {
                                    table.page.len(6).draw();
                                }
                            },
                            {
                                text: '12 dòng',
                                action: function() {
                                    table.page.len(12).draw();
                                }
                            },
                            {
                                text: 'Tất cả',
                                action: function() {
                                    table.page.len(-1).draw();
                                }
                            }
                        ]
                    }
                ],
                fixedColumns: {
                    leftColumns: 2,
                    rightColumns: 1
                },
                pageLength: 6,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json',
                    info: "Đang hiển thị _START_ - _END_ trong tổng số _TOTAL_ dữ liệu",
                    infoEmpty: "Không có dữ liệu để hiển thị",
                    infoFiltered: "(lọc từ _MAX_ dữ liệu)"
                },
                paging: true,
                lengthChange: false, // hidden lengthMenu default
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: false // off responsive to avoid conflict scrollX + FixedColumns
            });
        });
    </script>
@endpush

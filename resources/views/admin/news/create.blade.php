@extends('layouts.app')

@section('content')
    <div class="container-fluid bg-light py-4">
        <div class="d-flex justify-content-center">
            <div class="bg-white shadow rounded px-5 py-4" style="width: 794px; min-height: 1123px;">
                <form action="{{ route('news.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Tiêu đề chính -->
                    <div class="mb-4">
                        <input type="text" name="title" class="form-control border-0 fs-4 fw-bold"
                            placeholder="Tiêu đề bài viết..." required value="{{ old('title') }}">
                        @error('title')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                    </div>

                    <div class="mb-4">
                        <label class="form-label">Danh mục</label>
                        <select name="category_id" class="form-select fs-5" required>
                            <option value="" disabled selected>Chọn danh mục...</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                    </div>

                    <!-- Nội dung chi tiết -->
                    <div class="mb-4">
                        <div class="border rounded" style="min-height: calc(1123px - 200px);  padding: 16px;">
                            <textarea name="content" id="editor">{{ old('content') }}</textarea>
                        </div>
                        @error('content')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </form>

                <!-- Nút nổi bên phải -->
                <div class="position-fixed" style="bottom: 120px; right: 124px; z-index: 1050;" id="floating-buttons">
                    <div class="d-flex flex-column gap-5">
                        <div class="d-flex flex-column gap-3">
                            <a href="{{ route('news.index') }}" class="btn btn-outline-dark btn-sm rounded-circle"
                                data-bs-toggle="tooltip" title="Quay lại trang quản lý tin tức">
                                <i class="bi bi-arrow-left"></i>
                            </a>

                            <button type="button" class="btn btn-outline-primary btn-sm rounded-circle"
                                data-bs-toggle="modal" data-bs-target="#modalPreviewNews"
                                title="Xem trước bài viết (chưa lưu)">
                                <i class="bi bi-eye"></i>
                            </button>


                            <button type="submit" class="btn btn-dark btn-sm rounded-circle" data-bs-toggle="tooltip"
                                title="Xuất bản bài viết" onclick="document.querySelector('form').submit()">
                                <i class="bi bi-send-check-fill"></i>
                            </button>
                        </div>

                        <!-- Nút scroll to top -->
                        <button type="button" class="btn btn-secondary btn-sm rounded-circle" data-bs-toggle="tooltip"
                            title="Cuộn lên đầu trang" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
                            <i class="bi bi-chevron-double-up"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal xem trước -->
                <div class="modal fade" id="modalPreviewNews" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">📄 Xem trước bài viết</h5>
                                <button type="button" class="btn-close red-close" data-bs-dismiss="modal"
                                    aria-label="Đóng"></button>
                            </div>
                            <div class="modal-body p-0">
                                <div class="bg-white p-5 mx-auto"
                                    style="width: 794px; min-height: 1123px; border: 1px solid #dee2e6; border-radius: 4px;"
                                    id="preview-content">
                                    <!-- Nội dung bài viết sẽ được render tại đây -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/classic/ckeditor.js"></script>

    <script>
        let editorInstance;

        ClassicEditor
            .create(document.querySelector('#editor'), {
                ckfinder: {
                    uploadUrl: '{{ route('ckeditor.upload') . '?_token=' . csrf_token() }}'
                }
            })
            .then(editor => {
                editorInstance = editor;
                console.log('CKEditor đã khởi tạo thành công');

                // Gắn sự kiện preview sau khi editor đã sẵn sàng
                const previewBtn = document.querySelector('[data-bs-target="#modalPreviewNews"]');
                const previewContent = document.getElementById('preview-content');

                if (previewBtn) {
                    previewBtn.addEventListener('click', () => {
                        const title = document.querySelector('input[name="title"]').value.trim();
                        const category = document.querySelector('select[name="category_id"]');
                        const categoryName = category.options[category.selectedIndex]?.text || '';
                        const content = editor.getData();

                        previewContent.innerHTML = `
                            <h2 class="fw-bold mb-3">${title}</h2>
                            <div class="text-muted mb-4 fst-italic">Danh mục: ${categoryName}</div>
                            <div>${content}</div>
                        `;
                    });
                }
            })
            .catch(error => {
                console.error('Lỗi khi khởi tạo CKEditor:', error);
            });
    </script>

    <script>
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(el) {
            new bootstrap.Tooltip(el);
        });
    </script>
@endsection

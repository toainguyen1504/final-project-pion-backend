@extends('layouts.app')

@section('content')
    <div class="container-fluid bg-light py-4">
        <div class="d-flex justify-content-center">
            <div class="bg-white shadow rounded px-5 py-4" style="width: 794px; min-height: 1123px;">
                <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data"
                    id="edit-form">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>😕 Oops! Có vẻ như bạn đã bỏ sót một vài thông tin quan trọng.</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Tiêu đề -->
                    <div class="mb-4">
                        <input type="text" name="title" id="title-input" class="form-control border-0 fs-4 fw-bold"
                            placeholder="Tiêu đề bài viết..." value="{{ old('title', $news->title) }}" required>
                    </div>

                    <!-- Danh mục -->
                    <div class="mb-4">
                        <label class="form-label">Danh mục</label>
                        <select name="category_id" id="category-select" class="form-select fs-5" required>
                            <option value="" disabled>Chọn danh mục...</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $news->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nội dung -->
                    <div class="mb-4">
                        <div class="border rounded" style="min-height: calc(1123px - 200px); padding: 16px;">
                            <textarea name="content" id="editor" class="w-100" style="min-height: 300px;">{{ old('content', $news->content->content_html ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Floating buttons -->
                    <div class="position-fixed" style="bottom: 120px; right: 124px; z-index: 1050;" id="floating-buttons">
                        <div class="d-flex flex-column gap-5">
                            <div class="d-flex flex-column gap-3">
                                <a href="{{ route('admin.news.index') }}" class="btn btn-outline-dark btn-sm rounded-circle"
                                    data-bs-toggle="tooltip" title="Quay lại trang quản lý tin tức">
                                    <i class="bi bi-arrow-left"></i>
                                </a>

                                <button type="button" class="btn btn-outline-primary btn-sm rounded-circle"
                                    data-bs-toggle="modal" data-bs-target="#modalPreviewNews"
                                    title="Xem trước bài viết (chưa lưu)">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <button type="submit" class="btn btn-dark btn-sm rounded-circle" data-bs-toggle="tooltip"
                                    title="Cập nhật bài viết">
                                    <i class="bi bi-send-check-fill"></i>
                                </button>
                            </div>

                            <button type="button" class="btn btn-secondary btn-sm rounded-circle" data-bs-toggle="tooltip"
                                title="Cuộn lên đầu trang" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
                                <i class="bi bi-chevron-double-up"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPreviewNews" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 mx-auto" style="width: 794px;">
                    <h5 class="modal-title">📄 Xem trước bài viết</h5>
                    <button type="button" class="btn-close red-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="post-content bg-white p-5 mx-auto"
                        style="width: 794px; min-height: 1123px; border: 1px solid #dee2e6; border-radius: 4px;"
                        id="preview-content">
                        <!-- Nội dung bài viết sẽ được render tại đây -->
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
                heading: {
                    options: [{
                            model: 'paragraph',
                            title: 'Đoạn văn',
                            class: 'ck-heading_paragraph'
                        },
                        {
                            model: 'heading2',
                            view: 'h2',
                            title: 'Tiêu đề phụ (H2) - tự động đánh số 1,2,3...',
                            class: 'ck-heading_heading2'
                        },
                        {
                            model: 'heading3',
                            view: 'h3',
                            title: 'Tiêu đề nhỏ (H3) - tự động dạng danh sách đánh dấu (icon)',
                            class: 'ck-heading_heading3'
                        }
                    ]
                },
                toolbar: [
                    'heading',
                    '|',
                    'bold', 'italic', 'link',
                    // '|',
                    // 'bulletedList', 'numberedList',
                    // '|',
                    // 'blockQuote', 'insertTable',
                    '|',
                    'undo', 'redo'
                ],
                removePlugins: ['MediaEmbed'],
                image: {
                    styles: ['alignCenter', 'alignLeft', 'alignRight'],
                    resizeUnit: '%',
                    toolbar: [
                        'imageTextAlternative',
                        '|',
                        'imageStyle:alignLeft',
                        'imageStyle:alignCenter',
                        'imageStyle:alignRight'
                    ]
                },
                ckfinder: {
                    uploadUrl: '{{ route('ckeditor.upload') . '?_token=' . csrf_token() }}'
                }

            })
            .then(editor => {
                editorInstance = editor;
                console.log('CKEditor đã khởi tạo thành công');

                // Đồng bộ nội dung CKEditor về textarea trước khi submit
                const form = document.getElementById('edit-form');
                form.addEventListener('submit', function() {
                    document.querySelector('#editor').value = editorInstance.getData();
                });

                // Gắn sự kiện preview
                const previewBtn = document.querySelector('[data-bs-target="#modalPreviewNews"]');
                const previewContent = document.getElementById('preview-content');

                if (previewBtn) {
                    previewBtn.addEventListener('click', () => {
                        const title = document.querySelector('#title-input').value.trim();
                        const category = document.querySelector('#category-select');
                        const categoryName = category.options[category.selectedIndex]?.text || '';
                        const content = editorInstance.getData();

                        previewContent.innerHTML = `
                            <h1 class="fw-bold mb-3">${title}</h1>
                            <div class="text-muted mb-4 fst-italic">Danh mục: ${categoryName}</div>
                            <div class="post-content">${content}</div>
                        `;
                    });
                }
            })
            .catch(error => {
                console.error('Lỗi khi khởi tạo CKEditor:', error);
            });
    </script>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container-fluid bg-light py-4">
        <div class="d-flex">
            <div class="bg-white shadow rounded px-5 py-4" style="width: 794px;">
                <form action="{{ route('admin.posts.update', $posts->id) }}" method="POST" enctype="multipart/form-data"
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

                    <!-- title -->
                    <div class="mb-4">
                        <input type="text" name="title" id="post-title" class="form-control border-0 fs-4 fw-bold"
                            placeholder="Tiêu đề bài viết..." value="{{ old('title', $posts->title) }}" required>
                    </div>

                    <!-- category -->
                    <div class="mb-4">
                        <label class="form-label">Danh mục</label>
                        <select name="category_id" id="category-select" class="form-select fs-5" required>
                            <option value="" disabled>Chọn danh mục...</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $posts->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- content -->
                    <div class="mb-4">
                        <div class="border rounded"
                            style="padding: 0 12px; min-height: 540px; max-height: 640px; overflow-y: auto;">
                            <textarea name="content" id="editor">{{ old('content', $posts->content->content_html ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Floating buttons -->
                    <div class="position-fixed" style="bottom: 120px; right: 92px; z-index: 1050;" id="floating-buttons">
                        <div class="d-flex flex-column gap-3">
                            <!-- Back btn-->
                            <a href="{{ route('admin.posts.index') }}" class="floating-btn floating-back"
                                data-bs-toggle="tooltip" data-bs-placement="left" title="Quay lại trang quản lý tin tức">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-arrow-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M15 8a.5.5 0 0 1-.5.5H3.707l4.147 4.146a.5.5 0 0 1-.708.708l-5-5a.5.5 0 0 1 0-.708l5-5a.5.5 0 1 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z" />
                                </svg>
                                <span>Back</span>
                            </a>

                            <!-- preview btn -->
                            <button type="button" class="floating-btn floating-preview" data-bs-toggle="modal"
                                data-bs-target="#modalPreviewPost" data-bs-toggle="tooltip" data-bs-placement="left"
                                title="Xem trước bài viết (chưa lưu)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-eye" viewBox="0 0 16 16">
                                    <path
                                        d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4a4 4 0 1 1 0-8 4 4 0 0 1 0 8z" />
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z" />
                                </svg>
                                <span>Preview</span>
                            </button>

                            <!-- Edit btn -->
                            <button type="submit" class="floating-btn floating-submit" data-bs-toggle="tooltip"
                                data-bs-placement="left" title="Cập nhật bài viết (lưu)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-send-check-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M15.964.686a.5.5 0 0 0-.65-.65L.273 7.313a.5.5 0 0 0 .065.93l4.765 1.278 1.278 4.765a.5.5 0 0 0 .93.065l7.277-15.665zm-5.5 6.707l-4 4a.5.5 0 0 1-.708-.708l4-4a.5.5 0 0 1 .708.708z" />
                                </svg>
                                <span>Update</span>
                            </button>

                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPreviewPost" tabindex="-1" aria-hidden="true">
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
                        <!-- here is render content -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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
                            title: 'H2 - Tiêu đề phụ (tự động đánh số 1,2,3...)',
                            class: 'ck-heading_heading2'
                        },
                        {
                            model: 'heading3',
                            view: 'h3',
                            title: 'H3 - Tiêu đề nhỏ (tự động dạng danh sách đánh dấu - icon)',
                            class: 'ck-heading_heading3'
                        }
                    ]
                },
                toolbar: [
                    'heading',
                    '|',
                    'bold', 'italic', 'link',
                    '|',
                    'bulletedList',
                    '|',
                    'uploadImage',
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

                //  Sync CKEditor content to the textarea before submitting
                const form = document.getElementById('edit-form');
                form.addEventListener('submit', function() {
                    document.querySelector('#editor').value = editorInstance.getData();
                });

                const previewBtn = document.querySelector('[data-bs-target="#modalPreviewPost"]');
                const previewContent = document.getElementById('preview-content');

                if (previewBtn) {
                    previewBtn.addEventListener('click', () => {
                        const title = document.querySelector('#post-title').value.trim();
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

        // Check change
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('edit-form');
            const titleInput = document.getElementById('post-title');
            const categorySelect = document.getElementById('category-select');
            const updateButton = document.querySelector('.floating-submit');

            // save original data
            const originalTitle = titleInput.value.trim();
            const originalCategory = categorySelect.value;
            let originalContent = '';

            //get data original CKEditor when editor ready
            if (editorInstance) {
                originalContent = editorInstance.getData().trim();
            } else {
                console.error('CKEditor chưa khởi tạo xong để lấy dữ liệu ban đầu.');
            }

            // function check changes
            function checkChanges() {
                const currentTitle = titleInput.value.trim();
                const currentCategory = categorySelect.value;
                const currentContent = editorInstance.getData().trim();

                const hasChanges =
                    currentTitle !== originalTitle ||
                    currentCategory !== originalCategory ||
                    currentContent !== originalContent;

                updateButton.disabled = !hasChanges;
            }

            titleInput.addEventListener('input', checkChanges);
            categorySelect.addEventListener('change', checkChanges);
            editorInstance.model.document.on('change:data', checkChanges);

            checkChanges();
        });
        // End check change

        // Tooltip
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    
@endpush

@extends('layouts.app')

@section('content')
    <div class="container-fluid bg-light py-4">

        {{-- Guide write posts --}}
        <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-info-circle-fill"></i>
            <strong>Hướng dẫn viết bài:</strong> Tiêu đề ngắn, chọn danh mục đúng, intro 2–3 câu, thêm hình ảnh, dùng H2/H3,
            kết bài gợi hành động.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <div class="d-flex justify-content-center">
            <div class="bg-white shadow rounded px-5 py-4" style="width: 794px; min-height: 1123px;">
                <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

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

                    <!-- title-->
                    <div class="mb-4">
                        <input type="text" name="title" id="title-input" class="form-control border-0 fs-4 fw-bold"
                            placeholder="Tiêu đề bài viết..." required value="{{ old('title') }}">
                    </div>

                    <!-- category -->
                    <div class="mb-4">
                        <label class="form-label">Danh mục</label>
                        <select name="category_id" id="category-select" class="form-select fs-6" required>
                            <option value="" disabled selected>Chọn danh mục...</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- content CKEditor -->
                    <div class="mb-4">
                        <div class="border rounded" style="min-height: calc(1123px - 200px); padding: 16px;">
                            <textarea name="content" id="editor">{{ old('content') }}</textarea>
                        </div>
                    </div>

                    <!-- Floating buttons -->
                    <div class="position-fixed" style="bottom: 120px; right: 92px; z-index: 1050;" id="floating-buttons">
                        <div class="d-flex flex-column gap-3">
                            <!-- Back btn -->
                            <a href="{{ route('admin.posts.index') }}" class="floating-btn floating-back"
                                data-bs-toggle="tooltip" data-bs-placement="left" title="Quay lại trang quản lý tin tức">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-arrow-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M15 8a.5.5 0 0 1-.5.5H3.707l4.147 4.146a.5.5 0 0 1-.708.708l-5-5a.5.5 0 0 1 0-.708l5-5a.5.5 0 1 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z" />
                                </svg>
                                <span>Back</span>
                            </a>

                            <!-- Preview btn -->
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

                            <!-- Publish btn -->
                            <button type="submit" class="floating-btn floating-submit" data-bs-toggle="tooltip"
                                data-bs-placement="left" title="Xuất bản bài viết (lưu)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-send-check-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M15.964.686a.5.5 0 0 0-.65-.65L.273 7.313a.5.5 0 0 0 .065.93l4.765 1.278 1.278 4.765a.5.5 0 0 0 .93.065l7.277-15.665zm-5.5 6.707l-4 4a.5.5 0 0 1-.708-.708l4-4a.5.5 0 0 1 .708.708z" />
                                </svg>
                                <span>Publish</span>
                            </button>

                            <!-- Scroll to top btn -->
                            <button type="button" class="floating-btn floating-top" data-bs-toggle="tooltip"
                                data-bs-placement="left" data-bs-trigger="hover" title="Cuộn lên đầu trang"
                                onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-chevron-double-up" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M7.646 2.146a.5.5 0 0 1 .708 0l5 5a.5.5 0 0 1-.708.708L8 2.707 3.354 7.854a.5.5 0 1 1-.708-.708l5-5zM7.646 7.146a.5.5 0 0 1 .708 0l5 5a.5.5 0 0 1-.708.708L8 7.707l-4.646 4.647a.5.5 0 0 1-.708-.708l5-5z" />
                                </svg>
                                <span>Top</span>
                            </button>
                        </div>
                    </div>

                </form>

                <!-- Modal preview -->
                <div class="modal fade" id="modalPreviewPost" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header border-0 mx-auto" style="width: 794px;">
                                <h5 class="modal-title">📄 Xem trước bài viết</h5>
                                <button type="button" class="btn-close red-close" data-bs-dismiss="modal"
                                    aria-label="Đóng"></button>
                            </div>
                            <div class="modal-body p-0">
                                <div class="bg-white p-5 mx-auto"
                                    style="width: 794px; min-height: 1123px; border: 1px solid #dee2e6; border-radius: 4px;"
                                    id="preview-content">
                                    <!--here is render content -->
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
                    'heading', '|',
                    'bold', 'italic', 'link', '|',
                    'bulletedList', '|',
                    'uploadImage', '|',
                    'undo', 'redo'
                ],
                removePlugins: ['MediaEmbed'],
                image: {
                    styles: ['alignCenter', 'alignLeft', 'alignRight'],
                    resizeUnit: '%',
                    toolbar: [
                        'imageTextAlternative', '|',
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

                const form = document.querySelector('form');
                const titleInput = document.getElementById('title-input');
                const categorySelect = document.getElementById('category-select');
                const publishButton = document.querySelector('.floating-submit');

                // Disable btn publish
                publishButton.disabled = true;

                // save original value
                const originalTitle = titleInput.value.trim();
                const originalCategory = categorySelect.value;
                const originalContent = editorInstance.getData().trim();

                //  check changes
                function checkChanges() {
                    const currentTitle = titleInput.value.trim();
                    const currentCategory = categorySelect.value;
                    const currentContent = editorInstance.getData().trim();

                    const hasChanges =
                        currentTitle !== '' ||
                        currentCategory !== '' ||
                        currentContent !== '';

                    publishButton.disabled = !hasChanges;
                }

                // Sync CKEditor content to the textarea before submitting
                form.addEventListener('submit', function() {
                    document.querySelector('#editor').value = editorInstance.getData();
                });

                // event preview
                const previewBtn = document.querySelector('[data-bs-target="#modalPreviewPost"]');
                const previewContent = document.getElementById('preview-content');

                if (previewBtn) {
                    previewBtn.addEventListener('click', () => {
                        const title = titleInput.value.trim();
                        const categoryName = categorySelect.options[categorySelect.selectedIndex]?.text || '';
                        const content = editorInstance.getData();

                        previewContent.innerHTML = `
                            <h1 class="fw-bold mb-3">${title}</h1>
                            <div class="text-muted mb-4 fst-italic">Danh mục: ${categoryName}</div>
                            <div class="post-content">${content}</div>
                        `;
                    });
                }

                titleInput.addEventListener('input', checkChanges);
                categorySelect.addEventListener('change', checkChanges);
                editorInstance.model.document.on('change:data', checkChanges);

                checkChanges();
            })
            .catch(error => {
                console.error('Lỗi khi khởi tạo CKEditor:', error);
            });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <script>
        function selectTemplate(cssClass) {
            // Add a class to the form or content area
            const editorWrapper = document.querySelector('#editor').closest('.mb-4');
            editorWrapper.className = 'mb-4 ' + cssClass;

            // Assign an ID to the input to include it in the form submission
            const templateId = event.currentTarget.getAttribute('data-id');
            document.getElementById('selected-template-id').value = templateId;

            // Highlight template is selected
            document.querySelectorAll('.template-card').forEach(card => card.classList.remove('border-danger'));
            event.currentTarget.classList.add('border-danger');
        }
    </script>
@endsection

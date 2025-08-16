@extends('layouts.app')

@section('content')
    <div class="container-fluid bg-light py-4">

        {{-- Guide write posts --}}
        {{-- <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-info-circle-fill"></i>
            <strong>Hướng dẫn viết bài:</strong> Tiêu đề ngắn, chọn danh mục đúng, intro 2–3 câu, thêm hình ảnh, dùng H2/H3,
            kết bài gợi hành động.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div> --}}
        <div class="d-flex gap-4 align-items-start">
            <!-- Content Section -->
            <div class="flex-grow-1" style="min-width: 794px;">
                <!-- Form Section -->
                <div class="bg-white shadow rounded px-5 py-4" style="min-width: 794px;">
                    <form id="postForm" action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
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
                        <div class="mb-4 title-section">
                            <input type="text" name="title" id="title-input" class="form-control border-0 fs-4 fw-bold"
                                placeholder="Tiêu đề bài viết..." required value="{{ old('title') }}">

                            <!-- Link tĩnh -->
                            <div class="mt-2 fs-6" id="slug-display">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted">Liên kết tĩnh:</span>
                                    <span class="badge bg-light text-dark border" id="slug-preview">
                                        https://pion.edu.vn/posts/{{ Str::slug(old('title', 'mac-ban-cach-dan-giay-dan-tuong')) }}
                                    </span>
                                    <a href="#" class="text-decoration-none" id="edit-slug-btn">Chỉnh sửa</a>
                                </div>
                            </div>

                            <!-- Slug edit form (ẩn ban đầu) -->
                            <div class="mt-2 d-none fs-6" id="slug-edit">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted" style="min-width: 100px;">Liên kết tĩnh:</span>
                                    <div class="input-group input-group-sm flex-grow-1">
                                        <span class="input-group-text">https://pion.edu.vn/posts/</span>
                                        <input type="text" name="slug" id="inputGroup-sizing-sm" class="form-control"
                                            value="{{ old('slug', Str::slug(old('title', 'mac-ban-cach-dan-giay-dan-tuong'))) }}">
                                        <button type="button" class="btn px-4 fs-6 btn-outline-primary"
                                            id="confirm-slug-btn">OK</button>
                                        <button type="button" class="btn fs-6 btn-link" id="cancel-slug-btn">Hủy</button>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- category -->
                        {{-- <div class="mb-4">
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
                    </div> --}}

                        <!-- Add medias -->
                        <div class="mb-3 d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm">
                                📎 Thêm Media
                            </button>
                            {{-- <button type="button" class="btn btn-outline-secondary btn-sm">
                                🖼️ Add Gallery
                            </button> --}}
                        </div>

                        <!-- content CKEditor -->
                        <div class="mb-4">
                            <div class="border rounded"
                                style="padding: 0 12px; min-height: 540px; max-height: 640px; overflow-y: auto;">
                                <textarea name="content" id="editor">{{ old('content') }}</textarea>
                            </div>
                        </div>

                        <!-- Floating buttons -->
                        <div class="position-fixed" style="bottom: 120px; right: 24px; z-index: 1050;"
                            id="floating-buttons">
                            <div class="d-flex flex-column gap-3">
                                <!-- Back btn -->
                                <a href="{{ route('admin.posts.index') }}" class="floating-btn floating-back"
                                    data-bs-toggle="tooltip" data-bs-placement="left"
                                    title="Quay lại trang quản lý tin tức">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M15 8a.5.5 0 0 1-.5.5H3.707l4.147 4.146a.5.5 0 0 1-.708.708l-5-5a.5.5 0 0 1 0-.708l5-5a.5.5 0 1 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z" />
                                    </svg>
                                    <span>Back</span>
                                </a>

                                <!-- Preview btn -->
                                {{-- <button type="button" class="floating-btn floating-preview" data-bs-toggle="modal"
                                    data-bs-target="#modalPreviewPost" data-bs-toggle="tooltip" data-bs-placement="left"
                                    title="Xem trước bài viết (chưa lưu)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                        <path
                                            d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4a4 4 0 1 1 0-8 4 4 0 0 1 0 8z" />
                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z" />
                                    </svg>
                                    <span>Preview</span>
                                </button> --}}

                                <!-- Publish btn -->
                                {{-- <button type="submit" class="floating-btn floating-submit" data-bs-toggle="tooltip"
                                    data-bs-placement="left" title="Xuất bản bài viết (lưu)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-send-check-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M15.964.686a.5.5 0 0 0-.65-.65L.273 7.313a.5.5 0 0 0 .065.93l4.765 1.278 1.278 4.765a.5.5 0 0 0 .93.065l7.277-15.665zm-5.5 6.707l-4 4a.5.5 0 0 1-.708-.708l4-4a.5.5 0 0 1 .708.708z" />
                                    </svg>
                                    <span>Publish</span>
                                </button> --}}

                                <!-- Scroll to top btn -->
                                {{-- <button type="button" class="floating-btn floating-top" data-bs-toggle="tooltip"
                                data-bs-placement="left" data-bs-trigger="hover" title="Cuộn lên đầu trang"
                                onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-chevron-double-up" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M7.646 2.146a.5.5 0 0 1 .708 0l5 5a.5.5 0 0 1-.708.708L8 2.707 3.354 7.854a.5.5 0 1 1-.708-.708l5-5zM7.646 7.146a.5.5 0 0 1 .708 0l5 5a.5.5 0 0 1-.708.708L8 7.707l-4.646 4.647a.5.5 0 0 1-.708-.708l5-5z" />
                                </svg>
                                <span>Top</span>
                            </button> --}}
                            </div>
                        </div>

                    </form>
                </div>

                <!-- SEO Section -->
                <x-admin.posts.math-rank />

            </div>

            <!-- Sidebar Section -->
            <div style="width: 280px;" class="sidebar-right flex-shrink-0 fs-6">
                <!-- Publish Section -->
                <div class="mb-4">
                    <label class="form-label fw-bold">📝 Đăng</label>
                    <div class="border rounded p-3 bg-light">

                        <!-- Xem trước -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Xem trước</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#modalPreviewPost"
                                title="Xem trước bài viết (chưa lưu)">Xem&nbsp;trước</button>
                        </div>

                        <!-- Trạng thái -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Trạng thái: <strong class="text-success">Đã xuất bản</strong></span>
                                <a href="#" class="text-decoration-none edit-toggle"
                                    data-target="#edit-status">Chỉnh&nbsp;sửa</a>
                            </div>
                            <div class="mt-2 collapse" id="edit-status">
                                <select class="form-select form-select-sm mb-2">
                                    <option>Đã xuất bản</option>
                                    <option>Bản nháp</option>
                                    <option>Đang chờ xét duyệt</option>
                                </select>
                                <button class="btn btn-sm btn-success me-2">OK</button>
                                <button class="btn btn-sm btn-secondary cancel-toggle"
                                    data-target="#edit-status">Hủy</button>
                            </div>
                        </div>

                        <!-- Hiển thị -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Hiển thị: <strong class="text-warning">Công khai</strong></span>
                                <a href="#" class="text-decoration-none edit-toggle"
                                    data-target="#edit-visibility">Chỉnh&nbsp;sửa</a>
                            </div>
                            <div class="mt-2 collapse" id="edit-visibility">
                                <select class="form-select form-select-sm mb-2">
                                    <option>Công khai</option>
                                    <option>Riêng tư</option>
                                    <option>Mật khẩu bảo vệ</option>
                                </select>
                                <button class="btn btn-sm btn-success me-2">OK</button>
                                <button class="btn btn-sm btn-secondary cancel-toggle"
                                    data-target="#edit-visibility">Hủy</button>
                            </div>
                        </div>

                        <!-- Bản thảo -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Bản thảo: <strong>3</strong></span>
                                <a href="#" class="text-decoration-none edit-toggle"
                                    data-target="#revision-list">Xem&nbsp;lại</a>
                            </div>
                            <div class="mt-2 collapse" id="revision-list">
                                <ul class="list-group list-group-flush small">
                                    <li class="list-group-item">🕒 10/08/2022 - Sửa tiêu đề</li>
                                    <li class="list-group-item">🕒 09/08/2022 - Thêm ảnh</li>
                                    <li class="list-group-item">🕒 08/08/2022 - Viết nội dung</li>
                                </ul>
                                <button class="btn btn-sm btn-secondary mt-2 cancel-toggle"
                                    data-target="#revision-list">Đóng</button>
                            </div>
                        </div>

                        <!-- Thời gian đăng -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Đã đăng lúc: <strong>Thứ 7, 2022 lúc 11:52</strong></span>
                                <a href="#" class="text-decoration-none edit-toggle"
                                    data-target="#edit-date">Chỉnh&nbsp;sửa</a>
                            </div>
                            <div class="mt-2 collapse" id="edit-date">
                                <input type="datetime-local" class="form-control form-control-sm mb-2">
                                <button class="btn btn-sm btn-success me-2">OK</button>
                                <button class="btn btn-sm btn-secondary cancel-toggle"
                                    data-target="#edit-date">Hủy</button>
                            </div>
                        </div>

                        <!-- SEO Score -->
                        <div class="mb-2">
                            <span class="badge bg-warning text-dark">SEO: 62 / 100</span>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between mt-3">
                            <a href="#" class="text-danger text-decoration-none">🗑️ Bỏ vào thùng rác</a>
                            <button type="submit" form="postForm" class="btn btn-primary">Cập nhật</button>
                        </div>
                    </div>
                </div>


                <!-- category -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Chuyên mục</label>

                    <!-- Search box -->
                    <input type="text" class="form-control mb-2" id="category-search"
                        placeholder="🔍 Tìm kiếm chuyên mục...">

                    <!-- Category list -->
                    <div class="border rounded p-3" style="max-height: 140px; overflow-y: auto;" id="category-list">
                        @foreach ($categories as $category)
                            <div class="form-check category-item">
                                <input class="form-check-input" type="checkbox" name="category_ids[]"
                                    value="{{ $category->id }}" id="category-{{ $category->id }}"
                                    {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="category-{{ $category->id }}">
                                    {{ $category->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <!-- Add category link -->
                    <div class="mt-2">
                        <a href="{{ route('admin.categories.create') }}"
                            class="text-decoration-none text-primary fw-medium">
                            + Thêm chuyên mục
                        </a>
                    </div>
                </div>

                <!-- Ảnh đại diện -->
                <div class="mb-4">
                    <label class="form-label fw-bold">🖼️ Ảnh đại diện</label>
                    <div class="border rounded p-3 bg-light text-center">
                        @if (old('thumbnail') || (isset($post) && $post->thumbnail))
                            <label for="thumbnail-input" style="cursor: pointer;">
                                <img src="{{ old('thumbnail') ?? $post->thumbnail }}" alt="Ảnh đại diện"
                                    class="img-fluid rounded mb-2" style="max-height: 200px; object-fit: cover;">
                            </label>
                            <p class="text-muted mb-1">Nhấn vào ảnh để sửa hoặc cập nhật</p>
                            <a href="#" class="text-danger text-decoration-none">🗑️ Xóa ảnh đại diện</a>
                        @else
                            <label for="thumbnail-input" style="cursor: pointer;">
                                <div class="bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center"
                                    style="height: 200px;">
                                    <span class="text-muted">Nhấn để chọn ảnh đại diện</span>
                                </div>
                            </label>
                        @endif

                        <input type="file" name="thumbnail" id="thumbnail-input" class="d-none" accept="image/*">
                    </div>
                </div>

            </div>

            <!-- Preview content modal-->
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

            <!-- Edit Snippet Modal -->
            <div class="modal custom-modal fade" id="snippetModal" tabindex="-1" aria-labelledby="snippetModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="snippetModalLabel">🔧 Preview Snippet Editor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Preview URL -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Preview</label>
                                <p class="text-muted mb-1">https://yourdomain.com/tin-tuc/huong-dan-dan-giay</p>
                                <p class="fw-bold text-primary mb-1">Hướng Dẫn Cách Dán Giấy Dán Tường Mới Nhất</p>
                                <p class="text-secondary">Hướng Dẫn Cách Dán Giấy Dán Tường Mới Nhất. Nếu tường nhà bạn bị
                                    ẩm mốc hay không bằng phẳng bạn cần xử lý trước khi dán bảo.</p>
                            </div>

                            <!-- SEO Title -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Title</label>
                                <input type="text" class="form-control" id="seo_title"
                                    value="Hướng Dẫn Cách Dán Giấy Dán Tường Mới Nhất">
                                <div class="d-flex align-items-center justify-content-between mt-1 mb-1">
                                    <small class="text-muted">56 / 60 (484px / 580px)</small>
                                    <div class="progress flex-grow-1 ms-2" style="height: 6px;">
                                        <div class="progress-bar bg-warning" id="titleBar" style="width: 84%;"></div>
                                    </div>
                                </div>
                                <small class="text-muted">This is what will appear in the first line when this post shows
                                    up in the search results.</small>
                            </div>

                            <!-- Permalink -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Permalink</label>
                                <input type="text" class="form-control" id="seo_slug"
                                    value="6-cach-dan-giay-dan-tuong">
                                <div class="d-flex align-items-center justify-content-between mt-1 mb-1">
                                    <small class="text-muted">70 / 75</small>
                                    <div class="progress flex-grow-1 ms-2" style="height: 6px;">
                                        <div class="progress-bar bg-success" id="slugBar" style="width: 93%;"></div>
                                    </div>
                                </div>
                                <small class="text-muted">This is the unique URL of this page, displayed below the post
                                    title in the search results.</small>
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Description</label>
                                <textarea class="form-control" rows="3" id="seo_desc">Hướng Dẫn Cách Dán Giấy Dán Tường Mới Nhất. Nếu tường nhà bạn bị ẩm mốc hay không bằng phẳng bạn cần xử lý trước khi dán bảo.</textarea>
                                <div class="d-flex align-items-center justify-content-between mt-1 mb-1">
                                    <small class="text-muted">128 / 160 (804px / 920px)</small>
                                    <div class="progress flex-grow-1 ms-2" style="height: 6px;">
                                        <div class="progress-bar bg-danger" id="descBar" style="width: 87%;"></div>
                                    </div>
                                </div>
                                <small class="text-muted">This is what will appear as the description when this post shows
                                    up in the search results.</small>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="button" class="btn btn-primary">Lưu thay đổi</button>
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

        // Tooltip
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });


        // select template
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


        // Display when express "chỉnh sửa" in title section
        document.getElementById('edit-slug-btn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('slug-display').classList.add('d-none');
            document.getElementById('slug-edit').classList.remove('d-none');
        });

        document.getElementById('cancel-slug-btn').addEventListener('click', function() {
            document.getElementById('slug-edit').classList.add('d-none');
            document.getElementById('slug-display').classList.remove('d-none');
        });

        document.getElementById('confirm-slug-btn').addEventListener('click', function() {
            const newSlug = document.getElementById('slug-input').value.trim();
            document.getElementById('slug-preview').textContent = `https://pion.edu.vn/posts/${newSlug}`;
            document.getElementById('slug-edit').classList.add('d-none');
            document.getElementById('slug-display').classList.remove('d-none');
        });



        // Open/close when express "chỉnh sửa" of Publish section)
        document.querySelectorAll('.edit-toggle').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.dataset.target);
                target.classList.add('show');
            });
        });

        document.querySelectorAll('.cancel-toggle').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.dataset.target);
                target.classList.remove('show');
            });
        });
    </script>
@endsection

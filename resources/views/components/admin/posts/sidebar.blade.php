@push('styles')
    <style>
        #category-list .form-check {
            transition: background-color 0.2s ease;
            padding: 4px 8px;
            border-radius: 4px;
        }

        #category-list .form-check:hover {
            background-color: #f8f9fa;
        }

        #category-list .form-check-input:checked+.form-check-label {
            font-weight: 600;
            color: #0d6efd;
        }

        /* SEO Score box */
        .seo-score-box {
            background-color: transparent;
            border-radius: 6px;
            padding: 6px 16px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            opacity: 1;
            cursor: pointer;
            transition: opacity 0.4s ease;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .seo-score-box:hover {
            opacity: 0.92;
        }

        /* Trash link */
        .trash-link {
            color: #dc3545;
            text-decoration: underline;
            font-weight: 500;
            transition: 0.25s;
        }

        .trash-link:hover {
            text-shadow: 0 0 1px #dc3545;
        }

        /* Publish button */
        .publish-submit {
            padding: 6px 16px;
            font-weight: 600;
            font-size: 14px;
            width: 100%;
            border-radius: 0;
        }

        /* Thumbnail */
        .end-44 {
            right: 44px !important;
        }

        .thumbnail-og {
            position: relative;
        }

        .thumbnail-preview {
            position: relative;
        }

        .thumbnail-preview:hover img {
            opacity: 0.9;
            transition: opacity 0.3s ease;
        }

        /* Thumbnail wrapper 1200x630 (1.91:1) */
        .thumbnail-wrapper {
            position: relative;
            width: 100%;
            aspect-ratio: 1200 / 630;
            /* ~1.91:1 */
            overflow: hidden;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .thumbnail-wrapper img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            display: block;
        }

        /* Placeholder box 1.91:1 */
        .thumbnail-placeholder-box {
            aspect-ratio: 1200 / 630;
            width: 100%;
            background-color: rgba(108, 117, 125, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s ease;
        }

        .thumbnail-placeholder-box:hover {
            background-color: rgba(108, 117, 125, 0.2);
        }

        .cropper-container-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        /* .edit-image */
        .modal-wrapper .modal-dialog {
            position: absolute;
            display: block;
            min-width: 320px;
            bottom: 40vh;
            right: 40px;
            z-index: 9;
        }

        .modal-footer .form-select {
            width: 100%;
            max-width: 100px;
        }

        #remove-thumbnail {
            width: 31px;
            height: 31px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s ease;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
            text-decoration-line: none;
        }

        #remove-thumbnail i {
            color: #dc3545;
            font-size: 18px;
        }

        #remove-thumbnail:hover {
            background-color: rgba(255, 255, 255, 1);
        }

        strong.fw-medium {
            font-weight: 600 !important;
        }
    </style>
@endpush

<div style="width: 280px;" class="sidebar-right flex-shrink-0 fs-6">
    <!-- Publish Section -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label fw-bold mb-0">Đăng bài</label>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-link">
                Về trang quản lý bài viết
            </a>
        </div>
        <div class="border rounded p-3 bg-light">

            <!-- Preview -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Xem trước</span>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#modalPreviewPost" title="Xem trước bài viết (chưa lưu)">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

            <!-- Status -->
            <div class="mb-2">
                <div class="d-flex justify-content-between">
                    <span>Trạng thái: <strong id="status-label" class="fw-medium">Bản nháp</strong></span>
                    <a href="#" class="text-decoration-none edit-toggle"
                        data-target="#edit-status">Chỉnh&nbsp;sửa</a>
                </div>
                <div class="mt-2 collapse" id="edit-status">
                    <select id="status-select" class="form-select form-select-sm mb-2">
                        <option value="published">Đã xuất bản (Công khai)</option>
                        <option value="draft">Bản nháp (Đăng ở chế độ riêng tư)</option>
                        <option value="pending">Đang chờ xét duyệt</option>
                    </select>
                    <button class="btn btn-sm btn-yes me-2" data-action="save-status">OK</button>
                    <button class="btn btn-sm btn-cancel cancel-toggle" data-target="#edit-status">Hủy</button>
                </div>
            </div>

            <!-- Visibility -->
            <div class="mb-2">
                <div class="d-flex justify-content-between">
                    <span>Hiển thị: <strong id="visibility-label" class="fw-medium">Riêng tư</strong></span>
                    <a href="#" class="text-decoration-none edit-toggle d-none"
                        data-target="#edit-visibility">Chỉnh&nbsp;sửa</a>
                </div>
                <div class="mt-2 collapse" id="edit-visibility">
                    <select id="visibility-select" class="form-select form-select-sm mb-2">
                        <option value="public">Công khai</option>
                        <option value="private">Riêng tư</option>
                        {{-- <option value="scheduled_public">Công khai theo lịch</option> --}}
                        {{-- <option value="password">Mật khẩu bảo vệ</option> --}}
                    </select>
                    <button class="btn btn-sm btn-yes me-2" data-action="save-visibility">OK</button>
                    <button class="btn btn-sm btn-cancel cancel-toggle" data-target="#edit-visibility">Hủy</button>
                </div>
            </div>

            <!-- Bản thảo -->
            {{-- <div class="mb-2">
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
                    <button class="btn btn-sm btn-cancel mt-2 cancel-toggle" data-target="#revision-list">Đóng</button>
                </div>
            </div> --}}

            <!-- Publish -->
            <div class="mb-2">
                <div class="d-flex justify-content-between">
                    <span>
                        Thời gian đăng:
                        {{-- <strong id="publish-label" class="fw-medium">
                            {{ $post->publish_at ? \Carbon\Carbon::parse($post->publish_at)->format('d/m/Y H:i') : '--time--' }}
                        </strong> --}}
                        <strong id="publish-label" data-raw="{{ optional($post)->publish_at ?? now() }}">
                            {{ optional($post)->publish_at ?? '--time--' }}
                        </strong>
                    </span>

                    <a href="#" class="text-decoration-none edit-toggle ms-1"
                        data-target="#edit-date">Chỉnh&nbsp;sửa</a>
                </div>
                <div class="mt-2 collapse" id="edit-date">
                    <input type="datetime-local" class="form-control form-control-sm mb-2"
                        value="{{ optional($post)->publish_at ? $post->publish_at : '' }}">
                    <button class="btn btn-sm btn-yes me-2" id="save-schedule">OK</button>
                    <button class="btn btn-sm btn-cancel cancel-toggle" data-target="#edit-date">Hủy</button>
                </div>
            </div>

            <div class="mb-2">
                <span id="seo-score-box" class="seo-score-badge seo-score-box"> </span>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between mt-3 align-items-center" id="trash-actions">
                {{-- <div class="d-flex align-items-center">
                    <a href="#" class="trash-link">Bỏ vào thùng rác</a>

                    <!-- Trash status -->
                    <div class="trash-status d-none me-3 d-flex align-items-center w-100">
                        <span class="text-danger me-1 flex-grow-1 text-wrap">Đã bỏ vào thùng rác</span>
                        <a href="#" class="restore-link flex-shrink-0">Khôi phục</a>
                    </div>
                </div> --}}

                <!-- Actions publish post-->
                <button id="publish-submit" type="button" class="flex-shrink-0 px-4 publish-submit btn btn-yes">Cập
                    nhật</button>
            </div>
        </div>
    </div>

    <!-- category -->
    <div class="mb-4">
        <label class="form-label required-label fw-bold">Chuyên mục</label>

        @error('category_ids')
            <div class="text-danger">{{ $message }}</div>
        @enderror

        <!-- Search box -->
        <div class="input-group mb-2">
            <span class="input-group-text">🔍</span>
            <input type="text" class="form-control" id="category-search" placeholder="Tìm kiếm chuyên mục...">
        </div>

        <!-- Category list -->
        <div class="border rounded p-3" style="max-height: 160px; overflow-y: auto;" id="category-list">
            @foreach ($categories as $category)
                <div class="form-check category-item px-4">
                    <input class="form-check-input" type="checkbox" value="{{ $category->id }}"
                        id="category-{{ $category->id }}"
                        {{ in_array($category->id, old('category_ids', $selectedCategoryIds ?? [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="category-{{ $category->id }}">
                        {{ $category->name }}
                    </label>
                </div>
            @endforeach
        </div>

        <!-- Add category link -->
        <div class="mt-2">
            <a href="#" class="text-decoration-none text-primary fw-medium" data-bs-toggle="modal"
                data-bs-target="#modalAddCategory">
                + Thêm chuyên mục
            </a>
        </div>

        <!-- Modal Add Category -->
        <x-admin.modals.add-category :fromPostCreate="true" />
    </div>

    <div class="mb-4 thumbnail-og">
        <div class="thumbnail-inner">
            <p class="form-label fw-bold">Preview Thumbnail</p>
            @php
                $hasThumbnail = isset($thumbnailMedia);
            @endphp

            <div id="thumbnail-preview-container" class="mt-2 {{ $hasThumbnail ? '' : 'd-none' }}">
                <img id="thumbnail-preview-img" class="img-fluid rounded"
                    src="{{ $hasThumbnail ? asset($thumbnailMedia->getVariantPath('og')) : '' }}"
                    alt="{{ $hasThumbnail ? $thumbnailMedia->alt ?? 'Thumbnail preview' : '' }}" />
            </div>

            <div id="thumbnail-placeholder"
                class="border rounded p-3 text-muted text-center {{ $hasThumbnail ? 'd-none' : '' }}">
                Chưa chọn ảnh thumbnail, click nút Media để chọn ảnh đại diện...
            </div>

            <small class="text-muted">
                Ảnh sẽ được hiển thị ở danh sách bài viết và khi chia sẻ lên MXH (Facebook, Zalo, LinkedIn, Twitter/X …)
            </small>
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

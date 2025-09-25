@push('styles')
    <style>
        .nav-item .nav-link {
            color: #373535;
            transition: box-shadow 0.3s ease;
        }

        .nav-item .nav-link.active {
            color: #1E3A8A !important;
            font-weight: 500;
            background: rgba(30, 58, 138, 0.1);

        }

        .text-secondary {
            color: #1E3A8A !important;
        }

        .edit-image {
            color: #e3c900 !important;
            transition: color 0.3s;
        }

        .edit-image:hover {
            color: #d1b800 !important;
        }

        .delete-image:hover {
            color: rgb(231, 76, 60) !important;
        }

        /* overlay tick */
        .selected-overlay {
            z-index: 2;
            background-color: #1E3A8A;
        }

        .media-item.selected .selected-overlay {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 24px;
            height: 24px;
            padding: 4px;
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        .selected-overlay i {
            color: #FFF !important;
        }

        #deleteImageBtn:disabled {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
@endpush

<div class="modal fade" id="mediaLibraryModal" tabindex="-1" aria-labelledby="mediaLibraryLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title fw-bold fs-5">Thư viện Media</span>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <ul class="nav nav-tabs px-4 pt-3" id="mediaTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="upload-tab" data-bs-toggle="tab"
                            data-bs-target="#upload" type="button" role="tab">
                            <i class="fa-solid fa-upload"></i>
                            <span>Tải lên</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active d-flex align-items-center gap-2" id="library-tab"
                            data-bs-toggle="tab" data-bs-target="#library" type="button" role="tab">
                            <i class="fa-regular fa-image"></i>
                            <span>Thư viện</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content d-flex" id="mediaTabContent" style="height: 80vh;">
                    <!-- Media Grid -->
                    <div class="tab-pane fade show active flex-grow-1 p-4 overflow-auto" id="library" role="tabpanel">
                        <div class="row g-3" id="mediaGrid">
                            <!-- JS render ảnh -->
                        </div>
                    </div>

                    <!-- Upload Tab -->
                    <div class="tab-pane fade flex-grow-1 p-4" id="upload" role="tabpanel">
                        <div class="upload-area border rounded-3 p-4 text-center position-relative" id="uploadArea">
                            <i class="fa-solid fa-cloud-arrow-up fa-2x text-primary mb-3"></i>
                            <h5 class="mb-2">Nhấn để tải lên ngay!</h5>

                            <!-- Label -->
                            <label for="mediaUploadInput" class=" mt-3 fs-6 btn btn-outline-primary px-5 py-4"
                                id="chooseFileBtn">
                                Chọn tập tin
                            </label>

                            <!-- Button -->
                            <button id="uploadMediaBtn" class="btn btn-yes px-5 py-4 d-none mt-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-file-arrow-up fa-lg"></i>
                                    <span class="fs-6">Tải lên</span>
                                </div>
                            </button>

                            <div id="uploadStatus" class="mt-3 text-primary fw-semibold d-none">
                                <i class="fa-solid fa-spinner fa-spin me-2"></i> Đang tải lên...
                            </div>

                            <p class="text-muted mt-2 small">Kích thước tập tin tải lên tối đa: <strong>10MB</strong>
                            </p>

                            <!-- Input file hidden -->
                            <input type="file" id="mediaUploadInput" class="d-none" multiple>
                            <ul id="selectedFileList" class="list-unstyled mt-3 text-start small"></ul>
                        </div>
                    </div>

                    <!-- Sidebar Preview -->
                    <div class="flex-shrink-0 border-start p-3" style="width: 300px;" id="mediaSidebar">
                        <h6 class="text-uppercase text-muted mb-3">CHI TIẾT ĐÍNH KÈM</h6>

                        <!-- Preview Image -->
                        <div id="previewImage" class="mb-3 text-left"
                            data-default="{{ asset('adminAssets/img/no_image.png') }}">
                            <img src="{{ asset('adminAssets/img/no_image.png') }}" alt="IMG"
                                class="img-fluid rounded border" style="max-height: 116px;">
                        </div>

                        <!-- File Info -->
                        <div id="mediaInfo" class="mb-4 small text-muted">
                            <div><strong>Tên tệp:</strong> <span id="fileName">Đang cập nhật...</span></div>
                            <div><strong>Ngày tải lên:</strong> <span id="uploadDate">Đang cập nhật...</span></div>
                            <div><strong>Kích thước:</strong> <span id="fileSize">Đang cập nhật...</span></div>
                            <div><strong>Kích thước ảnh:</strong> <span id="imageDimensions">Đang cập nhật...</span>
                            </div>

                            {{-- <button type="button" class="btn btn-link p-0 edit-image" id="editImageBtn">Sửa
                                ảnh (resize - crop)</button> --}}
                            <div class="mt-2 d-flex gap-3">
                                <button type="button" class="btn btn-link p-0 delete-image text-danger"
                                    id="deleteImageBtn">Xóa vĩnh
                                    viễn</button>
                            </div>
                        </div>

                        <!-- Metadata Form -->
                        <form id="editMetadataForm">
                            <div class="mb-3 row">
                                <label for="title" class="col-sm-4 col-form-label required-label text-end fs-7">Tiêu đề</label>
                                <div class="col-sm-8">
                                    <input type="text" name="title" id="title" class="form-control fs-7">
                                    <small class="invalid-feedback">Vui lòng nhập tiêu đề hợp lệ (chỉ chữ và
                                        số).</small>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="caption" class="col-sm-4 col-form-label required-label text-end fs-7">Chú thích</label>
                                <div class="col-sm-8">
                                    <textarea name="caption" id="caption" class="form-control fs-7" rows="2"></textarea>
                                    <small class="invalid-feedback">Vui lòng nhập chú thích hợp lệ (chỉ chữ và
                                        số).</small>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="description" class="col-sm-4 col-form-label text-end fs-7">Mô tả</label>
                                <div class="col-sm-8 mb-5">
                                    <textarea name="description" id="description" class="form-control fs-7" rows="2"></textarea>
                                    <small class="invalid-feedback">Vui lòng nhập mô tả hợp lệ (chỉ chữ và số).</small>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-primary px-2 d-flex align-items-center gap-2 me-3"
                    id="insertToThumbnailBtn">
                    <i class="fa-solid fa-image"></i>
                    <span>Chọn làm ảnh thumbnail</span>
                </button>

                <button class="btn btn-yes px-5 d-flex align-items-center gap-2" id="insertToEditorBtn">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Chèn vào bài viết</span>
                </button>
            </div>
        </div>
    </div>
</div>

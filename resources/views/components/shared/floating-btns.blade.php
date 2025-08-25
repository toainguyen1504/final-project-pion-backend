@once
    @push('styles')
        <style>
            .floating-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                height: 3em;
                min-width: 112px;
                background: #fff;
                border: none;
                border-radius: 999px;
                cursor: pointer;
                letter-spacing: 0.5px;
                font-weight: 500;
                box-shadow: 3px 3px 10px #d1d1d1, -3px -3px 10px #ffffff;
                transition: all 0.3s ease;
                text-decoration: none;
                color: #333;
            }

            .floating-btn svg {
                transition: all 0.3s ease;
                font-size: 16px;
            }

            .floating-btn:hover {
                box-shadow: 6px 6px 20px #d1d1d1, -6px -6px 20px #ffffff;
                transform: translateY(-3px);
            }

            .floating-btn:hover svg {
                transform: translateX(-4px);
            }

            /* màu cho từng nút */
            .floating-preview {
                background: #e7f1ff;
                color: #0d6efd;
            }

            .floating-preview:hover {
                background: #d0e3ff;
            }

            .floating-submit {
                background: #e9fbe7;
                color: #198754;
            }

            .floating-submit:hover {
                background: #d1f7cf;
            }

            .floating-top {
                background: #f0f0f0;
                color: #6c757d;
                margin-top: 20px;
            }

            .floating-top:hover {
                background: #e0e0e0;
            }

            .floating-btn:disabled,
            .floating-btn.disabled {
                opacity: 0.6;
                cursor: not-allowed;
                pointer-events: none;
                filter: grayscale(0.6);
            }
        </style>
    @endpush
@endonce

<!-- Floating buttons -->
<div class="position-fixed" style="bottom: 120px; right: 24px; z-index: 1050;" id="floating-buttons">
    <div class="d-flex flex-column gap-3">
        <!-- Back btn -->
        <a href="{{ route('admin.posts.index') }}" class="floating-btn floating-back" data-bs-toggle="tooltip"
            data-bs-placement="left" title="Quay lại trang quản lý tin tức">
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
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye"
                viewBox="0 0 16 16">
                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4a4 4 0 1 1 0-8 4 4 0 0 1 0 8z" />
                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z" />
            </svg>
            <span>Preview</span>
        </button>

        <!-- Publish btn -->
        <button type="submit" class="floating-btn floating-submit" data-bs-toggle="tooltip" data-bs-placement="left"
            title="Xuất bản bài viết (lưu)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-send-check-fill" viewBox="0 0 16 16">
                <path
                    d="M15.964.686a.5.5 0 0 0-.65-.65L.273 7.313a.5.5 0 0 0 .065.93l4.765 1.278 1.278 4.765a.5.5 0 0 0 .93.065l7.277-15.665zm-5.5 6.707l-4 4a.5.5 0 0 1-.708-.708l4-4a.5.5 0 0 1 .708.708z" />
            </svg>
            <span>Publish</span>
        </button>

        <!-- Scroll to top btn -->
        <button type="button" class="floating-btn floating-top" data-bs-toggle="tooltip" data-bs-placement="left"
            data-bs-trigger="hover" title="Cuộn lên đầu trang"
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

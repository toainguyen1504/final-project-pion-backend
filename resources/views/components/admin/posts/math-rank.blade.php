<div class="mt-4 px-4">
    <h5 class="fw-bold mb-3">🔍 Rank Math SEO</h5>

    <!-- Preview -->
    <div class="mb-4">
        <label class="form-label fw-bold">Preview</label>
        <div class="border rounded p-3 bg-light">
            <p class="text-muted mb-1">https://yourdomain.com/tin-tuc/huong-dan-dan-giay</p>
            <p class="fw-bold text-primary">Hướng Dẫn Cách Dán Giấy Dán Tường Mới Nhất</p>
            <p class="text-secondary">Hướng Dẫn Cách Dán Giấy Dán Tường Mới Nhất. Nếu tường nhà bạn bị ẩm
                mốc hay không bằng phẳng bạn cần xử lý trước khi dán bảo.</p>
            <button type="button" class="btn btn-sm btn-warning mt-2" data-bs-toggle="modal"
                data-bs-target="#snippetModal">
                Edit Snippet
            </button>
        </div>
    </div>

    <!-- Focus Keyword -->
    <div class="mb-4">
        <label class="form-label fw-bold">🎯 Focus Keyword</label>
        <div class="position-relative">
            <div class="d-flex align-items-center border rounded px-2 py-1" id="keyword-container"
                style="flex-wrap: wrap; min-height: 42px;">
                <input type="text" id="keyword-input" class="border-0 flex-grow-1"
                    placeholder="Nhập từ khóa và nhấn Enter..." style="outline: none;">
            </div>
            <span class="position-absolute top-50 end-0 translate-middle-y me-3 fw-bold text-success">🔢 79
                / 100</span>
        </div>
    </div>


    <!-- Checklist -->
    <div class="card card-outline card-secondary">
        <div class="card-body p-0">

            {{-- Accordion Item: Basic SEO --}}
            <div class="border-bottom">
                <button
                    class="btn w-100 text-start d-flex justify-content-between align-items-center px-3 py-2 accordion-toggle"
                    data-target="#basic-seo-list">
                    <span class="fw-bold">Basic SEO</span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success">✅ All Good</span>
                        <i class="fas fa-chevron-down chevron-icon"></i>
                    </div>
                </button>
                <ul class="list-group list-group-flush collapse" id="basic-seo-list">
                    <li class="list-group-item text-success small">✔️ Hurray! You're using Focus Keyword in
                        the SEO Title.</li>
                    <li class="list-group-item text-success small">✔️ Focus Keyword used inside SEO Meta
                        Description.</li>
                    <li class="list-group-item text-success small">✔️ Focus Keyword used in the URL.</li>
                    <li class="list-group-item text-success small">✔️ Focus Keyword appears in the first
                        10% of the content.</li>
                    <li class="list-group-item text-success small">✔️ Focus Keyword found in the content.
                    </li>
                    <li class="list-group-item text-success small">✔️ Content is 1907 words long. Good job!
                    </li>
                </ul>
            </div>

            {{-- Accordion Item: Additional --}}
            <div class="border-bottom">
                <button
                    class="btn w-100 text-start d-flex justify-content-between align-items-center px-3 py-2 accordion-toggle"
                    data-target="#additional-list">
                    <span class="fw-bold">Additional</span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger">❌ 4 Errors</span>
                        <i class="fas fa-chevron-down chevron-icon"></i>
                    </div>
                </button>
                <ul class="list-group list-group-flush collapse" id="additional-list">
                    <li class="list-group-item text-danger small">❌ Thiếu ảnh có thẻ ALT</li>
                    <li class="list-group-item text-danger small">❌ Không có internal link</li>
                    <li class="list-group-item text-danger small">❌ Không có external link</li>
                    <li class="list-group-item text-danger small">❌ Meta Description quá ngắn</li>
                </ul>
            </div>

            {{-- Accordion Item: Title Readability --}}
            <div>
                <button
                    class="btn w-100 text-start d-flex justify-content-between align-items-center px-3 py-2 accordion-toggle"
                    data-target="#title-readability">
                    <span class="fw-bold">Title Readability</span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger">❌ 1 Error</span>
                        <i class="fas fa-chevron-down chevron-icon"></i>
                    </div>
                </button>
                <ul class="list-group list-group-flush collapse" id="title-readability">
                    <li class="list-group-item text-danger small">❌ Tiêu đề quá dài hoặc khó hiểu</li>
                </ul>
            </div>

        </div>
    </div>

</div>

{{-- once: helps avoid duplicate loading if the component is rendered multiple times. --}}
@once
    @push('scripts-admin')
        <script src="{{ asset('adminAssets/js/components/posts/math-rank.js') }}"></script>
    @endpush
@endonce

@once
    @push('styles')
        <style>
            .title-primary {
                color: #1E3A8A !important;
            }

            /* Keywords */
            .focus-keyword {
                max-width: 100%;
            }

            /* SEO Badge */
            .seo-score-badge {
                padding: 2px 20px;
                border-radius: 99px;
                font-size: 16px;
                font-weight: bold;
                color: #000;
                background-color: transparent;
            }

            .highlight-seo {
                box-shadow: 0 0 10px rgba(30, 58, 138, 0.4);
                transition: box-shadow 0.3s ease;
            }

            /* Keyword Wrapper */
            .keyword-wrapper {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                border: 1px solid #dee2e6;
                border-radius: 6px;
                padding: 8px 12px;
                max-height: 148px;
                overflow-y: auto;
            }

            /* Keyword Tags */
            .keyword-tags {
                margin-top: 8px;
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
            }

            /* Keyword Input */
            .keyword-input {
                width: 100%;
                border: none;
                outline: none;
                background: transparent;
                padding: 8px 0;
            }

            /* Custom Keyword Tags */
            .keyword-tags .keyword-tag {
                display: inline-flex;
                align-items: center;
                padding: 6px 10px;
                border-radius: 20px;
                font-weight: normal;
                margin-right: 6px;
                margin-bottom: 6px;
                cursor: default;
            }

            .keyword-tags .keyword-tag:first-child {
                color: #fff;
                /* background-color: #ffd142; */
                background-color: #929292;
            }

            .keyword-tags .keyword-tag:not(:first-child) {
                color: #333;
                /* background-color: #fff3cd; */
                background-color: rgb(200, 200, 199);
            }

            /* Remove button inside tag */
            .keyword-tags .keyword-tag .remove-tag {
                display: inline-block;
                padding: 2px 4px;
                margin-left: 6px;
                cursor: pointer;
                color: inherit;
                font-style: normal;
                font-weight: bold;

            }


            /*End  Keywords */

            /* Check list */
            .seo-badge {
                display: inline-flex;
                align-items: center;
                font-weight: 600;
                font-size: 0.875rem;
                padding: 0.25rem 0.5rem;
                border-radius: 0.5rem;
                gap: 0.25rem;
            }

            .bg-light-success {
                background-color: #e6f4ea;
                color: #198754;
            }

            .bg-light-danger {
                background-color: #fbeaea;
                color: #dc3545;
            }

            .accordion-toggle {
                background-color: #fff;
                border: none;
                font-size: 15px;
                cursor: pointer;
            }

            .chevron-icon {
                transition: transform 0.3s ease;
                font-size: 14px;
                color: #6c757d;
                transition: 0.3s;
            }

            .rotate {
                transform: rotate(180deg);
            }

            .collapse {
                display: none;
            }

            .collapse.show {
                display: block;
            }

            .list-group-item {
                font-size: 14px;
                padding: 8px 16px;
            }
        </style>
    @endpush
@endonce

<div class="mt-4 px-4">
    <h5 class="fw-bold mb-3">Rank Math SEO</h5>

    <!-- Preview -->
    <div class="mb-4">
        <label class="form-label fw-bold">Preview</label>
        <div class="border rounded p-3 bg-light">
            <p class="fs-6 text-muted mb-1" id="preview_slug">
                {{ 'https://pion.edu.vn/tin-tuc/' . ($post->slug ?? 'duong-dan-mac-dinh') }} </p>
            <p class="fs-5 fw-bold title-primary mb-1" id="preview_title">
                {{ $post->seo_title ?? 'Tiêu đề bài viết sẽ hiển thị ở đây...' }}</p>

            <p class="fs-6 text-secondary" id="preview_sapo">
                {{ $post->seo_description ?? 'Đoạn giới thiệu ngắn sẽ hiển thị ở đây...' }}</p>
            <button type="button" class="btn btn-yes mt-2" data-bs-toggle="modal" data-bs-target="#snippetModal">
                Edit Snippet
            </button>
        </div>
    </div>

    <!-- Focus Keyword -->
    <div class="focus-keyword mb-4">
        <label class="form-label fw-bold d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                Focus Keyword
                <!-- Icon ? -->
                <i class="fa-solid fa-circle-question text-secondary" tabindex="0" data-bs-toggle="popover"
                    data-bs-trigger="focus" data-bs-placement="right"
                    data-bs-content="Nhập keyword hợp lệ và nhấn enter để kích hoạt edit keyword!"
                    style="cursor: pointer;"></i>
            </div>
            <span id="seo-score-target" class="seo-score-badge"></span>
        </label>

        <div id="keyword-wrapper" class="keyword-wrapper"
            data-keywords="{{ !empty($keywords) && count($keywords) > 0 ? implode(',', $keywords) : '' }}">
            <input type="text" id="keyword-input" class="keyword-input"
                placeholder="Nhập từ khóa và nhấn Enter..." />
            <div class="keyword-tags">
                @if (!empty($keywords) && count($keywords) > 0)
                    @foreach ($keywords as $keyword)
                        <span class="keyword-tag">{{ $keyword }}</span>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <!-- End Focus Keyword -->

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
                        <span class="seo-badge">
                            <!-- JS will update icon + text + color -->
                        </span>
                        <i class="fas fa-chevron-down chevron-icon"></i>
                    </div>
                </button>
                <ul class="list-group list-group-flush collapse" id="basic-seo-list">
                    <!-- JS will add <li> in here -->
                </ul>
            </div>

            {{-- Accordion Item: Additional --}}
            <div class="border-bottom">
                <button
                    class="btn w-100 text-start d-flex justify-content-between align-items-center px-3 py-2 accordion-toggle"
                    data-target="#additional-list">
                    <span class="fw-bold">Additional</span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="seo-badge">
                            <!-- JS will update icon + text + color -->
                        </span>
                        <i class="fas fa-chevron-down chevron-icon"></i>
                    </div>
                </button>
                <ul class="list-group list-group-flush collapse" id="additional-list">
                    <!-- JS will add <li> in here -->
                </ul>
            </div>

            {{-- Accordion Item: Title and Content Readability --}}
            <div>
                <button
                    class="btn w-100 text-start d-flex justify-content-between align-items-center px-3 py-2 accordion-toggle"
                    data-target="#content-readability">
                    <span class="fw-bold">Content Readability</span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="seo-badge">
                            <!-- JS will update icon + text + color -->
                        </span>
                        <i class="fas fa-chevron-down chevron-icon"></i>
                    </div>
                </button>
                <ul class="list-group list-group-flush collapse" id="content-readability">
                    <!-- JS will add <li> in here -->
                </ul>
            </div>

        </div>
    </div>

    <!-- Edit Snippet Modal -->
    <div class="modal custom-modal fade" id="snippetModal" tabindex="-1" aria-labelledby="snippetModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="snippetModalLabel">Preview Snippet Editor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Preview URL -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Preview</label>
                        <div class="d-flex flex-wrap align-items-center mb-1">
                            <span class="text-muted fs-6">https://pion.edu.vn/</span>
                            <span class="text-muted fs-6" id="preview_slug_modal"></span>
                        </div>
                        <p class="fs-5 fw-bold title-primary mb-1" id="preview_title_modal"></p>
                        <p class="fs-6 text-secondary" id="preview_description_modal"></p>
                    </div>

                    <!-- SEO Title -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <!-- Label -->
                            <label class="form-label fw-bold mb-0">Title</label>

                            <div class="d-flex align-items-center">
                                <small id="titleCount" class="text-muted me-3 flex-shrink-0">0 / 60</small>
                                <div class="progress" style="height: 8px; width: 300px;">
                                    <div class="progress-bar" id="titleBar" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Input -->
                        <input type="text" class="form-control" id="seo_title"
                            value="{{ old('seo_title', $post->title ?? '') }}">
                        <small class="text-muted">This is what will appear in the first line when this post shows up in
                            the search results.</small>
                    </div>

                    <!-- Permalink -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">Permalink</label>

                            <div class="d-flex align-items-center">
                                <small id="slugCount" class="text-muted me-3 flex-shrink-0">0 / 75</small>
                                <div class="progress" style="height: 8px; width: 300px;">
                                    <div class="progress-bar" id="slugBar" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>

                        <input type="text" class="form-control" id="seo_slug"
                            value="{{ old('seo_slug', $post->slug ?? '') }}">
                        <small class="text-muted">This is the unique URL of this page, displayed below the post title
                            in the search results.</small>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">Description</label>

                            <div class="d-flex align-items-center">
                                <small id="descCount" class="text-muted me-3 flex-shrink-0">0 / 160 </small>
                                <div class="progress" style="height: 8px; width: 300px;">
                                    <div class="progress-bar" id="descBar" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>

                        <textarea class="form-control" rows="3" id="seo_description">{{ old('seo_description', $post->description ?? '') }}</textarea>
                        <small class="text-muted">This is what will appear as the description when this post shows up
                            in the search results.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="btn-save" class="btn btn-yes">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
                var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl)
                })
            });
        </script>
    @endpush
@endonce

@extends('layouts.app')

@push('styles')
    <style>
        /* floating btn */
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

        .floating-media {
            position: fixed;
            top: 80px;
            right: 356px;
            z-index: 1002;
            background: #E0E7FF;
            color: #1E3A8A;
        }

        .floating-media:hover {
            background: #D6DEFF;
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

@section('content')
    <div class="container-fluid bg-light py-4">
        <div class="d-flex gap-4 align-items-start">
            <!-- Content Section -->
            <div class="flex-grow-1" style="min-width: 794px;">
                <div class="bg-white shadow rounded px-5 py-4" style="min-width: 794px;">
                    <form id="postForm" action="{{ route('admin.posts.update', $post->id) }}" method="POST"
                        enctype="multipart/form-data">
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

                        <!-- Title -->
                        <div class="mb-4">
                            <input type="text" name="title" id="post-title" class="form-control border-0 fs-4 fw-bold"
                                placeholder="Tiêu đề bài viết..." value="{{ old('title', $post->title) }}">
                        </div>

                        <!-- Intro (Sapo) -->
                        <div class="mb-4">
                            <textarea name="sapo_text" id="sapo_text" class="form-control" rows="4"
                                placeholder="Nhập đoạn giới thiệu ngắn (sapo)...">{{ old('sapo_text', $post->sapo_text) }}</textarea>
                            <small class="text-muted">Đoạn sapo sẽ hiển thị ở đầu bài viết và tối ưu SEO.</small>
                        </div>

                        <!-- Content CKEditor -->
                        <div class="mb-4">
                            <div class="border rounded"
                                style="padding: 0 12px; min-height: 560px; max-height: 660px; overflow-y: auto;">
                                <textarea name="content" id="editor">{{ old('content', $post->content->content_html ?? '') }}</textarea>
                            </div>
                        </div>

                        <!-- Hidden input to submit id img -->
                        <input type="hidden" name="featured_media_id" id="featured_media_id"
                            value="{{ old('featured_media_id') ?? ($post->featured_media_id ?? '') }}">

                        <!-- Hidden SEO fields -->
                        <input type="hidden" name="seo_title" id="hidden_seo_title"
                            value="{{ $post->seo_title ?? '' }}">
                        <input type="hidden" name="slug" id="hidden_slug" value="{{ $post->slug   ?? '' }}">
                        <input type="hidden" name="seo_description" id="hidden_seo_description"
                            value="{{ $post->seo_description ?? $post->sapo_text }}">
                        <input type="hidden" name="seo_keywords" id="hidden_keywords"
                            value="{{ $post->seo_keywords ?? '' }}">

                        <!-- Hidden inputs for status & visibility -->
                        <input type="hidden" name="status" id="hidden_status" value="{{ $post->status }}">
                        <input type="hidden" name="visibility" id="hidden_visibility" value="{{ $post->visibility }}">
                        <input type="hidden" name="publish_at" id="hidden_publish_at" value="{{ $post->publish_at }}">

                        <!-- Hidden container for categories -->
                        <div id="category-hidden-container"></div>
                    </form>
                </div>

                <!-- SEO section -->
                <x-admin.posts.math-rank :post="$post" :keywords="$keywords"/>
            </div>

            <!-- Sidebar -->
            <x-admin.posts.sidebar :categories="$categories" :selectedCategoryIds="$selectedCategoryIds" :post="$post" :thumbnailMedia="$thumbnailMedia" />

            <!-- Media Library Modal -->
            <x-admin.modals.media-library />
        </div>
    </div>

    <!-- Floating Media Button -->
    <div class="mb-3 d-flex gap-2">
        <button type="button" class="floating-btn floating-media" id="openMediaLibrary" title="Thêm media vào bài viết">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path
                    d="M14 2H2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM2 3h12a1 1 0 0 1 1 1v5.586l-2.5-2.5a1 1 0 0 0-1.414 0L8 10l-1.5-1.5a1 1 0 0 0-1.414 0L2 12V4a1 1 0 0 1 1-1zm0 10v-.586l4-4 1.5 1.5a1 1 0 0 0 1.414 0L12 7.414l3 3V12a1 1 0 0 1-1 1H2z" />
                <circle cx="4.5" cy="5.5" r="1.5" />
            </svg>
            <span>Media</span>
        </button>
    </div>

    <!-- Toast -->
    <div id="toast-message" class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
        <div class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive"
            aria-atomic="true" data-bs-autohide="true" data-bs-delay="2000" id="liveToast">
            <div class="d-flex">
                <div class="toast-body fw-bold" id="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
@endsection

@once
    @push('scripts')
        <script>
            window.ckUploadUrl = "{{ route('ckeditor.upload') . '?_token=' . csrf_token() }}";
        </script>

        <script src="{{ asset('adminAssets/js/components/medias/media-library.js') }}"></script>
        <script src="{{ asset('adminAssets/js/components/posts/editor.js') }}"></script>
        <script src="{{ asset('adminAssets/js/components/posts/math-rank.js') }}"></script>
        <script src="{{ asset('adminAssets/js/components/posts/sidebar.js') }}"></script>

        <script>
            // Submit form with categories
            document.addEventListener("DOMContentLoaded", function() {
                const form = document.getElementById("postForm");
                const categoryContainer = document.getElementById("category-hidden-container");
                const publishBtn = document.querySelector(".publish-submit");

                if (!form || !categoryContainer || !publishBtn) return;
                publishBtn.addEventListener("click", function() {
                    categoryContainer.innerHTML = "";

                    const checkedCategories = document.querySelectorAll(
                        "#category-list input[type='checkbox']:checked");

                    checkedCategories.forEach((checkbox) => {
                        const hiddenInput = document.createElement("input");
                        hiddenInput.type = "hidden";
                        hiddenInput.name = "category_ids[]";
                        hiddenInput.value = checkbox.value;
                        categoryContainer.appendChild(hiddenInput);
                    });

                    form.requestSubmit();
                });
            });
        </script>

        <script>
            const mediaBtn = document.querySelector('.floating-media');
            window.addEventListener('scroll', () => {
                const scrollY = window.scrollY;
                mediaBtn.style.top = scrollY >= 332 ? '16px' : '80px';
            });
        </script>
    @endpush
@endonce

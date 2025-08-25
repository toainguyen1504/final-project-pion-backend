@extends('layouts.app')

@section('content')
    <div class="container-fluid bg-light py-4">

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

                        <!-- title section-->
                        <div class="mb-4 title-section">
                            <input type="text" name="title" id="post-title" class="form-control border-0 fs-4 fw-bold"
                                placeholder="Tiêu đề bài viết..." required value="{{ old('title') }}">

                        </div>

                        <!-- INTRO - SAPO COMPONENT -->
                        <div class="mb-4">
                            <label for="sapo_text" class="form-label fw-bold">📝 Giới thiệu ngắn (Intro / Sapo)</label>
                            <textarea name="sapo_text" id="sapo_text" class="form-control" rows="4"
                                placeholder="Nhập đoạn giới thiệu ngắn...">{{ old('sapo_text') }}</textarea>
                            <small class="text-muted">Đoạn sapo sẽ hiển thị ở đầu bài viết và dùng để tối ưu SEO. Không cần
                                chèn ảnh tại đây.</small>
                        </div>

                        <!-- Add medias -->
                        <div class="mb-3 d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm">
                                📎 Thêm Media
                            </button>
                        </div>

                        <!-- content CKEditor -->
                        <div class="mb-4">
                            <div class="border rounded"
                                style="padding: 0 12px; min-height: 540px; max-height: 640px; overflow-y: auto;">
                                <textarea name="content" class="seo_content" id="editor">{{ old('seo_content') }}</textarea>
                            </div>
                        </div>

                        <!-- Hidden SEO section -->
                        <input type="hidden" name="seo_title" id="hidden_seo_title">
                        <input type="hidden" name="slug" id="hidden_slug">
                        <input type="hidden" name="seo_description" id="hidden_seo_description">
                        <input type="hidden" name="seo_keywords" id="hidden_keywords">

                        <!-- Hidden inputs for status & visibility -->
                        <input type="hidden" name="status" id="hidden_status">
                        <input type="hidden" name="visibility" id="hidden_visibility">
                        <input type="hidden" name="publish_at" id="hidden_publish_at">

                        <!-- Hidden container to JS push DATA CATEGORIES -->
                        <div id="category-hidden-container"></div>
                    </form>
                </div>

                <!-- SEO Section -->
                <x-admin.posts.math-rank />
            </div>

            <!-- Sidebar Section -->
            <x-admin.posts.sidebar :categories="$categories" />

        </div>
    </div>

    <div id="toast-message" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-bold">
                    {{-- This is toast message --}}
                </div>
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

        <script src="{{ asset('adminAssets/js/components/posts/editor.js') }}"></script>

        <script src="{{ asset('adminAssets/js/components/posts/math-rank.js') }}"></script>

        <script src="{{ asset('adminAssets/js/components/posts/sidebar.js') }}"></script>

        <script>
            //========== submit form========= 
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

                    console.log("✅ Categories pushed:", [...checkedCategories].map(c => c.value));

                    // send data
                    form.requestSubmit();
                });
            });
            //========== end submit form=========
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalAdd = document.getElementById('modalAddCategory');
                const shouldOpenModal = @json(session('openModal') === 'modalAddCategory');

                if (shouldOpenModal && modalAdd) {
                    const modal = new bootstrap.Modal(modalAdd);
                    modal.show();
                }

                if (modalAdd) {
                    modalAdd.addEventListener('hidden.bs.modal', function() {
                        modalAdd.querySelectorAll('.alert-danger').forEach(el => el.remove());
                        const input = modalAdd.querySelector('input[name="name"]');
                        input?.classList.remove('is-invalid');
                        input && (input.value = '');
                    });
                }
            });
        </script>
    @endpush
@endonce

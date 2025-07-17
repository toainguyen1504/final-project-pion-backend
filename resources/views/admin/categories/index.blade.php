@extends('layouts.app')

@section('content')
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h1 class="mb-0 fs-2">Quản lý danh mục bài viết</h1>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <button class="btn btn-dark d-flex align-items-center justify-content-center gap-2" style="width: 40%;"
                data-bs-toggle="modal" data-bs-target="#modalAddCategory">
                <i class="fas fa-plus"></i>
                <span>Thêm danh mục</span>
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle table-striped table-hover py-2" id="categories-table">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 60px;">STT</th>
                    <th style="min-width: 200px;">Tên danh mục</th>
                    <th class="text-center" style="width: 160px;">Ngày tạo</th>
                    <th class="text-center" style="width: 160px;">Ngày cập nhật</th>
                    <th class="text-center" style="width: 140px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $index => $category)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>

                        <td class="text-truncate" style="max-width: 240px;" title="{{ $category->name }}">
                            {{ \Illuminate\Support\Str::limit($category->name, 80) }}
                        </td>

                        <td class="text-center">{{ $category->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">{{ $category->updated_at->format('d/m/Y H:i') }}</td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <button class="btn btn-warning btn-xl px-4" data-bs-toggle="modal"
                                    data-bs-target="#modalEditCategory" data-id="{{ $category->id }}"
                                    data-name="{{ $category->name }}">
                                    Sửa
                                </button>

                                <button type="button" class="btn btn-danger text-white btn-xl px-4" data-bs-toggle="modal"
                                    data-bs-target="#modalConfirmDelete" data-id="{{ $category->id }}">
                                    Xóa
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                            <span>Chưa có danh mục nào được tạo.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal thêm danh mục -->
    <div class="modal fade" id="modalAddCategory" tabindex="-1" aria-labelledby="modalAddCategoryLabel" aria-hidden="true">
        <div class="modal-dialog" style="margin-top: 80px;">
            <div class="modal-content">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAddCategoryLabel">Thêm danh mục</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Hiển thị lỗi nếu có --}}
                        @if ($errors->any())
                            <div class="alert alert-danger mb-3">
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="mb-5">
                            <label for="name" class="form-label">Tên danh mục</label>
                            <input type="text" name="name" class="form-control" required maxlength="50">
                        </div>

                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-dark px-5">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal sửa danh mục -->
    <div class="modal fade" id="modalEditCategory" tabindex="-1" aria-labelledby="modalEditCategoryLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="margin-top: 80px;">
            <div class="modal-content">
                <form id="editCategoryForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditCategoryLabel">Chỉnh sửa danh mục</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger mb-3">
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label">Tên danh mục</label>
                            <input type="text" name="name" id="edit-category-name" class="form-control"
                                value="{{ old('name') }}" required maxlength="50">
                            <input type="hidden" id="edit-category-id">
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" id="btn-update-category" class="btn btn-dark px-4" disabled>Cập
                            nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal xác nhận xóa -->
    <div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-labelledby="modalConfirmDeleteLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="margin-top: 80px;">
            <div class="modal-content">
                <form id="deleteCategoryForm" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header bg-danger bg-opacity-10 border-0">
                        <h5 class="modal-title text-danger" id="modalConfirmDeleteLabel">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Xác nhận xóa danh mục
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>

                    <div class="modal-body">
                        Bạn có chắc chắn muốn <strong class="text-danger mx-2">XÓA</strong> danh mục này không?
                    </div>

                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger px-4">Xóa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- pull data into form edit --}}
    <script>
        const modalEdit = document.getElementById('modalEditCategory');
        modalEdit.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const name = button.getAttribute('data-name');
            const id = button.getAttribute('data-id');

            document.getElementById('edit-category-name').value = name;
            document.getElementById('editCategoryForm').action = `/admin/categories/${id}`;
        });
    </script>

    {{-- Theo dõi thay đổi của data --}}
    <script>
        let originalName = '';

        modalEdit.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const name = button.getAttribute('data-name');
            const id = button.getAttribute('data-id');

            const input = document.getElementById('edit-category-name');
            const form = document.getElementById('editCategoryForm');
            const submitBtn = document.getElementById('btn-update-category');

            // Gán giá trị ban đầu
            input.value = name;
            originalName = name.trim();
            form.action = `/categories/${id}`;
            submitBtn.disabled = true; // Mặc định disable

            // Theo dõi thay đổi
            input.addEventListener('input', function() {
                const current = input.value.trim();
                submitBtn.disabled = (current === originalName || current === '');
            });
        });
    </script>


    {{-- Mở modal Delete --}}
    <script>
        const modalDelete = document.getElementById('modalConfirmDelete');
        modalDelete.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const form = document.getElementById('deleteCategoryForm');
            form.action = `/categories/${id}`;
        });
    </script>

    {{-- toast message --}}
    <script>
        const toastElList = [].slice.call(document.querySelectorAll('.toast'));
        toastElList.map(function(toastEl) {
            new bootstrap.Toast(toastEl, {
                delay: 3000
            }).show();
        });
    </script>

    {{-- Xóa lỗi khi modal Add đóng --}}
    <script>
        const modalAdd = document.getElementById('modalAddCategory');
        modalAdd.addEventListener('hidden.bs.modal', function() {
            // Xóa lỗi
            modalAdd.querySelectorAll('.alert-danger').forEach(el => el.remove());

            // Xóa input lỗi
            const input = modalAdd.querySelector('input[name="name"]');
            input?.classList.remove('is-invalid');

            // Nếu có old input (giữ lại sau lỗi), xóa luôn
            input && (input.value = '');
        });
    </script>

    {{-- Xóa lỗi khi modal Edit đóng --}}
    <script>
        modalEdit.addEventListener('hidden.bs.modal', function() {
            modalEdit.querySelectorAll('.alert-danger').forEach(el => el.remove());

            const input = modalEdit.querySelector('input[name="name"]');
            input?.classList.remove('is-invalid');
            input && (input.value = '');

            // Reset nút cập nhật về disable
            const submitBtn = document.getElementById('btn-update-category');
            if (submitBtn) submitBtn.disabled = true;
        });
    </script>

    {{-- Script Categories DataTables --}}
    <script>
        $(document).ready(function() {
            $('#categories-table').DataTable({
                lengthMenu: [
                    [6, 12, 18, -1],
                    [6, 12, 18, "Tất cả"]
                ],
                pageLength: 6,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
                },
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true
            });
        });
    </script>

    {{-- open modal again if err --}}
    @if (session('openModal') === 'modalEditCategory' && session('editingId'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('modalEditCategory'));
                document.getElementById('edit-category-name').value = @json(old('name'));
                document.getElementById('editCategoryForm').action = `/admin/categories/{{ session('editingId') }}`;
                modal.show();
            });
        </script>
    @elseif (session('openModal') === 'modalAddCategory')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('modalAddCategory'));
                modal.show();
            });
        </script>
    @endif
@endsection
{{-- Với slug sau này sẽ có hàm, và tự động gán slug vào DB mà không cần phải nhập. slug sẽ được chuyển từ name --}}

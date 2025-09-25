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
        <table class="table table-bordered align-middle table-striped table-hover" id="categories-table">
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
                                <button class="btn btn-warning btn-sm py-1 px-3" data-bs-toggle="modal"
                                    data-bs-target="#modalEditCategory" data-id="{{ $category->id }}"
                                    data-name="{{ $category->name }}">
                                    Sửa
                                </button>

                                <button type="button" class="btn btn-danger text-white btn-sm py-1 px-3"
                                    data-bs-toggle="modal" data-bs-target="#modalConfirmDelete"
                                    data-id="{{ $category->id }}">
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

    <!-- Modal add category -->
    <x-admin.modals.add-category />

    <!-- Modal edit category -->
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

    <!-- Modal confirm delete -->
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

@push('scripts')
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

    {{-- Track changes to the data --}}
    <script>
        let originalName = '';

        modalEdit.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const name = button.getAttribute('data-name');
            const id = button.getAttribute('data-id');

            const input = document.getElementById('edit-category-name');
            const form = document.getElementById('editCategoryForm');
            const submitBtn = document.getElementById('btn-update-category');

            // initial value
            input.value = name;
            originalName = name.trim();
            form.action = `/categories/${id}`;
            submitBtn.disabled = true;

            //Track changes
            input.addEventListener('input', function() {
                const current = input.value.trim();
                submitBtn.disabled = (current === originalName || current === '');
            });
        });
    </script>

    {{-- Delete err when modal Edit is closed --}}
    <script>
        modalEdit.addEventListener('hidden.bs.modal', function() {
            modalEdit.querySelectorAll('.alert-danger').forEach(el => el.remove());

            const input = modalEdit.querySelector('input[name="name"]');
            input?.classList.remove('is-invalid');
            input && (input.value = '');

            // Reset btn
            const submitBtn = document.getElementById('btn-update-category');
            if (submitBtn) submitBtn.disabled = true;
        });
    </script>

    {{-- Script Categories DataTables --}}
    <script>
        $.fn.dataTable.ext.errMode = 'none'; //off warming when no data

        $(document).ready(function() {
            var table = $('#categories-table').DataTable({
                scrollX: true,

                dom: '<"top"Blfr>t<"bottom"ip>',
                buttons: [{
                        extend: 'colvis',
                        text: '<i class="fas fa-columns me-1"></i>&nbsp;Tùy chỉnh cột'
                    },
                    {
                        text: '<i class="fas fa-list-ol me-1"></i>&nbsp;Số dòng',
                        extend: 'collection',
                        autoClose: true,
                        buttons: [{
                                text: '4 dòng',
                                action: function() {
                                    table.page.len(4).draw();
                                }
                            },
                            {
                                text: '6 dòng',
                                action: function() {
                                    table.page.len(6).draw();
                                }
                            },
                            {
                                text: '12 dòng',
                                action: function() {
                                    table.page.len(12).draw();
                                }
                            },
                            {
                                text: 'Tất cả',
                                action: function() {
                                    table.page.len(-1).draw();
                                }
                            }
                        ]
                    }
                ],
                fixedColumns: {
                    leftColumns: 2,
                    rightColumns: 1
                },
                pageLength: 6,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json',
                    info: "Đang hiển thị _START_ - _END_ trong tổng số _TOTAL_ dữ liệu",
                    infoEmpty: "Không có dữ liệu để hiển thị",
                    infoFiltered: "(lọc từ _MAX_ dữ liệu)"
                },
                paging: true,
                lengthChange: false, // hidden lengthMenu default
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: false // off responsive to avoid conflict scrollX + FixedColumns
            });
        });
    </script>

    {{-- Open modal Delete --}}
    <script>
        const modalDelete = document.getElementById('modalConfirmDelete');
        modalDelete.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const form = document.getElementById('deleteCategoryForm');
            form.action = `/categories/${id}`;
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

    {{-- Delete errs when modal Add is closed --}}
    <script>
        const modalAdd = document.getElementById('modalAddCategory');
        modalAdd.addEventListener('hidden.bs.modal', function() {
            modalAdd.querySelectorAll('.alert-danger').forEach(el => el.remove());

            const input = modalAdd.querySelector('input[name="name"]');
            input?.classList.remove('is-invalid');
            input && (input.value = '');
        });
    </script>
@endpush

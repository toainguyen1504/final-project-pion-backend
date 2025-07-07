@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h1 class="mb-0 fs-2">Danh sách quản trị viên</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.users.create') }}" class="btn btn-dark" style="width: 40%;">+ Thêm quản trị viên mới</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Ngày tạo</th>
                        <th class="text-center">Cập nhật</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if ($user->role)
                                    <span class="w-100 badge bg-{{ $user->role->name === 'admin' ? 'dark' : 'secondary' }}">
                                        {{ $user->role->name }}
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">Chưa gán</span>
                                @endif
                            </td>
                            <td>
                                {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'Chưa xác định' }}
                            </td>
                            <td class="text-center">
                                {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'Chưa xác định' }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-3">
                                    {{-- nút edit --}}
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                        class="btn btn-warning btn-sm px-3">Sửa</a>

                                    <!-- Nút Xóa kích hoạt modal -->
                                    <!-- Nút Xóa -->
                                    <button type="button" class="btn btn-danger btn-sm px-3" data-bs-toggle="modal"
                                        data-bs-target="#modalDeleteUser" data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}">
                                        Xóa
                                    </button>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Chưa có người dùng nào.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

    <!-- Modal xác nhận xóa user -->
    <div class="modal fade" id="modalDeleteUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="margin-top: 80px;">
            <div class="modal-content border-0 shadow">
                <form method="POST" id="delete-user-form">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header bg-danger bg-opacity-10 border-0">
                        <h5 class="modal-title text-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Xác nhận xóa người dùng
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>

                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn <strong class="text-danger mx-2">XÓA</strong> người dùng sau không?</p>
                        <div class="border rounded p-3 small bg-light">
                            <span id="delete-user-name" class="fw-bold text-dark"></span>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger px-4">Xóa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        const deleteModal = document.getElementById('modalDeleteUser');
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-id');
            const userName = button.getAttribute('data-name');

            const form = document.getElementById('delete-user-form');
            form.action = `/admin/users/${userId}`;
            document.getElementById('delete-user-name').textContent = userName;
        });
    </script>
@endsection

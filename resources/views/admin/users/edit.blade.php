@extends('layouts.app')

@section('content')
    <div class="container">
        <h3 class="mb-4">Chỉnh sửa quản trị viên</h3>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Họ tên</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Địa chỉ Email</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email) }}">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu mới (nếu muốn đổi)</label>
                <input type="password" name="password" class="form-control" placeholder="Để trống nếu không đổi">
            </div>

            <button type="submit" class="btn btn-success">Cập nhật</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
@endsection

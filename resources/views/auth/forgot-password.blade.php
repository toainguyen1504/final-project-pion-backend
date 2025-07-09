<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('adminAssets/favicon/favicon.ico') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container" style="max-width: 400px; margin-top: 80px;">
        <h4 class="mb-4 text-center">Khôi phục mật khẩu</h4>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Nhập email của bạn</label>
                <input type="email" name="email" class="form-control" required autofocus>
            </div>
            <button type="submit" class="btn btn-dark w-100">Gửi liên kết đặt lại mật khẩu</button>
            <div class="mt-3 text-center">
                <a href="{{ route('login') }}" class="text-muted">← Quay lại đăng nhập</a>
            </div>
        </form>
    </div>
</body>

</html>

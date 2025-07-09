<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập quản trị PION</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('adminAssets/favicon/favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Sora:wght@100..800&display=swap");

        body {
            background-color: #f8f9fa;
            /* font-family: "Montserrat", sans-serif; */
        }

        .login-container {
            max-width: 424px;
            min-height: 50vh;
            margin: 124px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .login-container .logo {
            width: 100px;
            margin-bottom: 20px;
        }

        .login-container .title {
            font-size: 24px;
            font-weight: 600;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="login-container text-center">
        <a href="/"><img src="{{ asset('adminAssets/img/logo.png') }}" alt="Pion Logo" class="logo"></a>
        <h1 class="mb-4 title">Đăng nhập quản trị</h1>

        @if (session('error'))
            <div class="alert alert-danger text-start">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3 text-start">
                <label for="email" class="form-label">Địa chỉ Email</label>
                <input type="email" name="email" class="form-control" required autofocus>
            </div>
            <div class="mb-3 text-start">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-danger w-100">Đăng nhập</button>
            <div class="mt-3">
                {{-- Ghi nhớ đăng nhập --}}
                <a href="{{ route('password.request') }}" class="text-dark">Quên mật khẩu?</a>
            </div>
        </form>
    </div>
</body>

</html>

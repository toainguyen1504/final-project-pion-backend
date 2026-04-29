<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>PION ADMIN @yield('title')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('adminAssets/favicon/favicon.ico') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" />

    <style>
        body {
            font-family: 'Source Sans 3', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .app-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .app-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .admin-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #343a40;
        }

        .admin-title strong {
            color: #dc3545;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
    </style>

</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary">
    <div class="app-wrapper">

        <main class="app-main px-5 py-4">
            {{--  CONTENT --}}
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>

</html>

<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Quản trị PION</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('adminAssets/favicon/favicon.ico') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
        integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg=" crossorigin="anonymous" />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI=" crossorigin="anonymous" />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('adminAssets/css/adminlte.css') }}" />
    <!--end::Required Plugin(AdminLTE)-->

    {{-- DataTables Bootstrap5 CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">


    <!-- CKEditor -->
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/45.2.0/ckeditor5.css">

    {{-- awesome ICON --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Custom Bootstrap CSS --}}
    <style>
        /* Common */
        /* Bỏ outline của btn */
        button:focus,
        button:active {
            outline: none;
            box-shadow: none;
        }

        .text-primary,
        .text-danger {
            color: #ce232d !important;
        }

        .bg-primary {
            background-color: #ebb5b8f4 !important;
        }

        /* Custom btn link */
        .btn-link {
            color: #ce232d !important;
            transition: color 0.2s ease;
        }

        .btn-link:hover {
            color: rgb(207, 33, 44) !important;
            text-decoration-thickness: 1.5px;
        }

        /* End Common */

        button.btn-dark:disabled {
            opacity: 0.4;
        }

        .btn-close.red-close {
            filter: invert(24%) sepia(97%) saturate(5989%) hue-rotate(355deg) brightness(92%) contrast(98%);
            padding: 8px 24px;
        }

        .btn-close:focus {
            outline: none;
            box-shadow: none;
        }

        /* Custom floating-buttons*/
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

        /* màu cho từng nút */
        .floating-preview {
            background: #e7f1ff;
            color: #0d6efd;
        }

        .floating-preview:hover {
            background: #d0e3ff;
        }

        .floating-submit {
            background: #e9fbe7;
            color: #198754;
        }

        .floating-submit:hover {
            background: #d1f7cf;
        }

        .floating-top {
            background: #f0f0f0;
            color: #6c757d;
            margin-top: 20px;
        }

        .floating-top:hover {
            background: #e0e0e0;
        }

        .floating-btn:disabled,
        .floating-btn.disabled {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
            filter: grayscale(0.6);
        }

        /* End Custom floating-buttons*/

        /* Css title for table */
        .title-link-custom {
            color: #212529;
            text-decoration: underline;
            text-underline-offset: 3px;
            letter-spacing: 0.3px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .title-link-custom:hover {
            color: #ce232d;
            text-decoration-thickness: 1.5px;
        }

        /* DataTables Pagination Styling */
        .dataTables_wrapper .dataTables_paginate .pagination .page-item .page-link {
            color: #212529;
            background-color: #fff;
            border: 1px solid #dee2e6;
            transition: all 0.2s ease;
        }

        .dataTables_wrapper .dataTables_paginate .pagination .page-item.active .page-link {
            background-color: #ce232d;
            color: #fff;
            border-color: #ce232d;
            font-weight: bold;
            box-shadow: 0 0 4px rgba(206, 35, 45, 0.4);
        }

        .dataTables_wrapper .dataTables_paginate .pagination .page-item .page-link:hover,
        .dataTables_wrapper .pagination .page-link:focus {
            background-color: #fce7e9;
            color: #ce232d;
            border-color: #ce232d;
        }

        /* Nút Trước / Sau bị vô hiệu (ở đầu hoặc cuối) */
        .dataTables_wrapper .dataTables_paginate .pagination .page-item.disabled .page-link {
            background-color: #f5f5f5;
            color: #999;
            border-color: #dee2e6;
            cursor: not-allowed;
            opacity: 0.6;
            pointer-events: none;
        }

        /* Bỏ outline cho các nút trong pagination */
        .dataTables_wrapper .pagination .page-link:focus,
        .dataTables_wrapper .pagination .page-link:active {
            outline: none;
            box-shadow: none;
        }

        /* Ô tìm kiếm DataTables & Dropdown chọn số dòng hiển thị */
        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            outline: none;
            border-color: #ce232d;
            box-shadow: none;
        }

        .dataTables_wrapper .dataTables_length select:hover {
            cursor: pointer;
        }

        .dataTables_wrapper .dataTables_filter input:hover,
        .dataTables_wrapper .dataTables_length select:hover {
            border-color: #ce232d;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/post.css') }}">

</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary">
    <div class="app-wrapper">
        @include('layouts.components.header')
        @include('layouts.components.sidebar')

        <main class="app-main px-5 py-4">
            @if (session('success') || session('error'))
                <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
                    <div class="toast align-items-center {{ session('success') ? 'text-bg-success' : 'text-bg-danger' }} border-0 show"
                        role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                {{ session('success') ?? session('error') }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                                aria-label="Đóng"></button>
                        </div>
                    </div>
                </div>
            @endif

            @yield('content') <!-- Đây là nơi nội dung của các view con sẽ được chèn vào -->
        </main>

        @include('layouts.components.footer')
    </div>

    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
        integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ=" crossorigin="anonymous"></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
    </script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('adminAssets/js/adminlte.js') }}"></script>
    <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script>
        const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
        const Default = {
            scrollbarTheme: 'os-theme-light',
            scrollbarAutoHide: 'leave',
            scrollbarClickScroll: true,
        };
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
            if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script>
    <!--end::OverlayScrollbars Configure-->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    {{-- DataTables Bootstrap5 JS --}}
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


    {{-- @stack('scripts') --}}

    @yield('scripts')
    <!--end::Script-->
</body>

</html>

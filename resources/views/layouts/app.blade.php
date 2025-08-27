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

    <!--Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
        integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg=" crossorigin="anonymous" />

    <!--Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI=" crossorigin="anonymous" />

    <!--Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('adminAssets/css/adminlte.css') }}" />

    {{-- DataTables Bootstrap5 CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <!-- FixedColumns CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">

    <!-- CKEditor -->
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/45.2.0/ckeditor5.css">

    <!-- CropperJS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />

    {{-- awesome ICON --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Custom Bootstrap CSS --}}
    <style>
        /* Common */
        /* Reset outline btn */
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

        .btn-yes {
            background-color: #1E3A8A !important;
            color: #fff !important;
            padding-left: 16px;
            padding-right: 16px;
            transition: background-color 0.3s;
        }

        .btn-yes:hover {
            background-color: #2563EB !important;
            color: #fff !important;
        }

        .btn-cancel {
            background-color: transparent !important;
            color: #ce232d !important;
            border: 1px solid #ce232d;
            transition: background-color 0.3s;
        }

        .btn-cancel:hover {
            background-color: rgb(206 35 45 / 6%) !important;
            border: 1px solid #ce232d;
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

        .dataTables_wrapper .dt-button-collection {
            max-height: 300px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Input search DataTables */
        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none !important;
            box-shadow: none !important;
            border-color: #000 !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 8px 16px;
        }

        .dataTables_wrapper .dataTables_length select:hover {
            cursor: pointer;
        }

        .dataTables_wrapper .dataTables_filter input:hover {
            border-color: #000 !important;
        }

        /* Bottom DataTables */
        .dataTables_wrapper .bottom {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
        }

        /* DataTables Info */
        .dataTables_wrapper .dataTables_info {
            display: inline-block;
        }

        /* DataTables Pagination */
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

        /* btn before / after disable */
        .dataTables_wrapper .dataTables_paginate .pagination .page-item.disabled .page-link {
            background-color: #f5f5f5;
            color: #999;
            border-color: #dee2e6;
            cursor: not-allowed;
            opacity: 0.6;
            pointer-events: none;
        }

        /* unset outline button pagination */
        .dataTables_wrapper .pagination .page-link:focus,
        .dataTables_wrapper .pagination .page-link:active {
            outline: none;
            box-shadow: none;
        }

        /* END Bottom DataTables */

        /* Style DataTables Buttons */
        .dt-buttons>.dt-button {
            background-color: #fff !important;
            border: 1px solid #dee2e6 !important;
            color: #333 !important;
            padding: 4px 10px !important;
            border-radius: 6px !important;
            margin-right: 6px !important;
            font-size: 13px !important;
            box-shadow: none !important;
            transition: all 0.2s ease-in-out;
        }

        .dt-buttons>.dt-button:focus:not(.disabled) {
            outline: none !important;
            box-shadow: !important;
        }

        /* Hover effect */
        .dt-buttons>.dt-button:hover {
            background-color: #f8f9fa !important;
            border-color: #ced4da !important;
            color: #000 !important;
        }

        /* Custom btn modal close Bootstrap */
        .btn-icon-only.btn-close-custom {
            background: none;
            border: none;
            padding: 8px 12px;
            color: #ce232d;
            font-size: 20px;
            transition: color 0.3s ease;
        }

        .btn-icon-only.btn-close-custom:hover {
            color: rgb(165, 19, 33);
            cursor: pointer;
        }

        /* logo */
        .brand-link:hover .brand-text {
            color: rgb(165, 19, 33) !important;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        /*  Customize posts UI */
        .sidebar-right.fs-6 a,
        .sidebar-right.fs-6 span,
        .sidebar-right .category-item label,
        #category-search {
            font-size: 14px;
        }

        .title-section .fs-6 span,
        .title-section .fs-6 a,
        .title-section .fs-6 label,
        .title-section .fs-6 input {
            font-size: 14px;
        }

        /* customize modal */
        .custom-modal .modal-dialog {
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .custom-modal .modal-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .custom-modal .modal-header,
        .custom-modal .modal-footer {
            flex-shrink: 0;
            position: sticky;
            z-index: 1;
            background-color: #fff;
        }

        .custom-modal .modal-header {
            top: 0;
            border-bottom: 1px solid #dee2e6;
        }

        .custom-modal .modal-footer {
            bottom: 0;
            border-top: 1px solid #dee2e6;
        }

        .custom-modal .modal-body {
            overflow-y: auto;
            overflow-x: hidden;
            flex-grow: 1;
            padding: 1rem;
        }

        /* End customize posts UI */
    </style>

    <link rel="stylesheet" href="{{ asset('adminAssets/post-css/template-news.css') }}">

    @stack('styles')
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary">
    <div class="app-wrapper">
        {{-- Header --}}
        <x-admin.blocks.header />

        {{-- Sidebar --}}
        <x-admin.blocks.sidebar />

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
            {{--  CONTENT --}}
            @yield('content')
        </main>
        {{-- Footer --}}
        <x-admin.blocks.footer />
    </div>

    <!--Third Party Plugin(OverlayScrollbars)-->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
        integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ=" crossorigin="anonymous"></script>

    <!--Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
    </script>

    <!--Required Plugin(AdminLTE)-->
    <script src="{{ asset('adminAssets/js/adminlte.js') }}"></script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- DataTables Bootstrap5 JS --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <!-- FixedColumns JS -->
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>

    {{-- CKEditor --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/classic/ckeditor.js"></script>

    {{-- support export --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    {{-- Buttons export --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

    <!-- CropperJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

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

    <script src="{{ asset('adminAssets/js/slugify.js') }}"></script>

    @stack('scripts')
</body>

</html>

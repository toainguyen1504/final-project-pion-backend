<!--begin::Sidebar-->
{{-- data-bs-theme="dark" --}}
<aside class="app-sidebar bg-body-secondary shadow">
    <div class="sidebar-brand">
        <a href="/admin" class="brand-link logo-switch">
            <img src="{{ asset('adminAssets/img/logo.png') }}" alt="Pion"
                class="brand-image-xl logo-xl opacity-75 px-2" />
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="/admin" class="nav-link fw-semibold">
                        {{-- <i class="nav-icon bi bi-circle-fill"></i> --}}
                        <i class="nav-icon bi bi-house-door-fill"></i>
                        <p>Tổng quan</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/admin/categories" class="nav-link fw-semibold">
                        <i class="nav-icon bi bi-folder-fill"></i>
                        <p>
                            Quản lý danh mục
                        </p>
                    </a>

                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link fw-semibold">
                        <i class="nav-icon bi bi-newspaper"></i>
                        <p>
                            Tin tức
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/admin/news" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Quản lý</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin/news/create" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Thêm tin tức</p>
                            </a>
                        </li>
                    </ul>
                </li>

                @if (Auth::user()?->hasRole('admin'))
                    <li class="nav-item">
                        <a href="#" class="nav-link fw-semibold">
                            <i class="nav-icon bi bi-people-fill"></i>
                            <p>
                                Quản lý tài khoản
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/admin/users" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Người dùng</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#!" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Quyền truy cập</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#!" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Vai trò</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <li class="nav-item">
                    <a href="#!" class="nav-link fw-semibold">
                        <i class="nav-icon bi bi-chat-dots-fill"></i>
                        <p>Quản lý tư vấn</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
<!--end::Sidebar-->

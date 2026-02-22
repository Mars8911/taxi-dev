<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '後台管理') - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar { min-height: 100vh; transition: transform 0.3s, margin 0.3s; }
        @media (max-width: 991.98px) {
            .sidebar { position: fixed; left: 0; top: 0; z-index: 1050; margin-left: -280px; }
            .sidebar.show { margin-left: 0; box-shadow: 0.5rem 0 1rem rgba(0,0,0,.15); }
            .sidebar-backdrop { display: none; }
            .sidebar-backdrop.show { display: block; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1040; }
        }
    </style>
</head>
<body class="bg-light">
    {{-- 行動版側邊欄遮罩 --}}
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    {{-- 頂部導航列（行動版） --}}
    <nav class="navbar navbar-dark bg-dark d-lg-none sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">後台管理</a>
            <button class="btn btn-outline-light btn-sm" type="button" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </nav>

    <div class="d-flex">
        {{-- 側邊欄 --}}
        <aside class="sidebar bg-dark text-white flex-shrink-0" id="sidebar">
            <div class="d-flex flex-column h-100" style="width: 280px;">
                <div class="p-4 border-bottom border-secondary">
                    <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none fs-5 fw-semibold">
                        <i class="bi bi-speedometer2 me-2"></i>後台管理
                    </a>
                </div>
                <nav class="flex-grow-1 p-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link text-white-50 {{ request()->routeIs('admin.dashboard') ? 'bg-secondary bg-opacity-25 text-white' : 'hover-bg-secondary hover-bg-opacity-25' }}">
                                <i class="bi bi-grid-1x2 me-2"></i>儀表板
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}" class="nav-link text-white-50 {{ request()->routeIs('admin.users.*') ? 'bg-secondary bg-opacity-25 text-white' : 'hover-bg-secondary hover-bg-opacity-25' }}">
                                <i class="bi bi-people me-2"></i>會員管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.orders.index') }}" class="nav-link text-white-50 {{ request()->routeIs('admin.orders.*') ? 'bg-secondary bg-opacity-25 text-white' : 'hover-bg-secondary hover-bg-opacity-25' }}">
                                <i class="bi bi-receipt me-2"></i>訂單管理
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="p-3 border-top border-secondary">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm w-100 text-start">
                            <i class="bi bi-box-arrow-right me-2"></i>登出
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- 主內容 --}}
        <main class="flex-grow-1 p-4 p-lg-5">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarBackdrop').classList.toggle('show');
        }
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', () => { if (window.innerWidth < 992) toggleSidebar(); });
        });
    </script>
</body>
</html>

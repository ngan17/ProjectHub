<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản lý Đăng ký Đề tài Nhóm')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin-bottom: 5px;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: #ffd700;
        }
        .navbar-header {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .content-area {
            padding: 30px;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 5px;
            padding: 8px 20px;
        }
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
        }
        .logo {
            color: white;
            font-weight: bold;
            font-size: 20px;
            padding: 0 20px;
            margin-bottom: 30px;
        }
        .logo i {
            margin-right: 10px;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -250px;
                width: 250px;
                z-index: 1000;
            }
            .sidebar.active {
                left: 0;
            }
            .main-container.sidebar-active {
                margin-left: 250px;
            }
        }
    </style>
</head>

<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar" style="width: 250px;">
        <div class="logo">
            <i class="fas fa-book"></i> Quản lý Đề tài
        </div>
        <nav class="nav flex-column">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Trang chủ
            </a>
            <a href="{{ route('topics.index') }}" class="nav-link {{ request()->routeIs('topics.*') ? 'active' : '' }}">
                <i class="fas fa-list"></i> Danh sách đề tài
            </a>
            <a href="{{ route('groups.index') }}" class="nav-link {{ request()->routeIs('groups.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Quản lý nhóm
            </a>
            <a href="{{ route('topic_requests.index') }}" class="nav-link">
                <i class="fas fa-check-circle"></i> Duyệt đăng ký
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-chart-bar"></i> Thống kê
            </a>
            <hr style="background-color: rgba(255,255,255,0.2); margin: 20px 0;">
            <a href="#" class="nav-link">
                <i class="fas fa-cog"></i> Cài đặt
            </a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="nav-link text-start bg-transparent border-0 w-100">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </button>
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-container flex-grow-1">
        <!-- Header -->
        <div class="navbar-header d-flex justify-content-between align-items-center px-4 py-3">
            <div>
                <button class="btn btn-light d-md-none" id="toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0 d-inline ms-2">Hệ thống quản lý đăng ký đề tài nhóm</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i>
                        {{ Auth::user()->name ?? 'Người dùng' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('users.profile') }}">Hồ sơ</a></li>
                        <li><a class="dropdown-item" href="#">Cài đặt</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Nội dung chính -->
        <div class="content-area">
            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('toggle-sidebar').addEventListener('click', function () {
        document.querySelector('.sidebar').classList.toggle('active');
        document.querySelector('.main-container').classList.toggle('sidebar-active');
    });
</script>
</body>
</html>

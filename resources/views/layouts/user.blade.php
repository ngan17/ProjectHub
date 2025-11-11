<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Portal')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
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
            border-left-color: #fbbf24;
        }
        .navbar-header {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .content-area {
            padding: 30px;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            border-radius: 5px;
            padding: 8px 20px;
            color: white;
        }
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
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
        .notification-badge {
            position: absolute;
            top: 5px;
            right: 10px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -250px;
                width: 250px;
                z-index: 1000;
                transition: left 0.3s ease;
            }
            .sidebar.active {
                left: 0;
            }
            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }
            .overlay.active {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
<div class="overlay" id="overlay"></div>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar" style="width: 250px;">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i> Student Portal
        </div>
        <nav class="nav flex-column">
            <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="{{ route('user.topics') }}" class="nav-link {{ request()->routeIs('user.topics*') ? 'active' : '' }}">
                <i class="fas fa-book"></i> Đề tài
            </a>
            <a href="{{ route('user.my_groups') }}" class="nav-link {{ request()->routeIs('user.my-groups*') || request()->routeIs('user.group*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Nhóm của tôi
            </a>
            <a href="{{ route('user.invites') }}" class="nav-link position-relative {{ request()->routeIs('user.invites') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i> Lời mời
                @php
                    $invCount = is_int($pendingInvites ?? 0) ? ($pendingInvites ?? 0) : (isset($pendingInvites) ? $pendingInvites->count() : 0);
                @endphp
                @if($invCount > 0)
                    <span class="notification-badge">{{ $invCount }}</span>
                @endif
            </a>
            <a href="{{ route('user.join-requests') }}" class="nav-link position-relative {{ request()->routeIs('user.join-requests') ? 'active' : '' }}">
                <i class="fas fa-paper-plane"></i> Yêu cầu
                @php
                    $reqCount = is_int($pendingRequests ?? 0) ? ($pendingRequests ?? 0) : (isset($pendingRequests) ? $pendingRequests->count() : 0);
                @endphp
                @if($reqCount > 0)
                    <span class="notification-badge">{{ $reqCount }}</span>
                @endif
            </a>
            <a href="{{ route('user.my_topics') }}" class="nav-link {{ request()->routeIs('user.my-topics') ? 'active' : '' }}">
                <i class="fas fa-bookmark"></i> Đề tài của tôi
            </a>
            <hr style="background-color: rgba(255,255,255,0.2); margin: 20px 0;">
            <a href="{{ route('user.classes') }}" class="nav-link {{ request()->routeIs('user.classes*') ? 'active' : '' }}">
                <i class="fas fa-chalkboard"></i> Lớp học
            </a>
            <a href="{{ route('user.subjects') }}" class="nav-link {{ request()->routeIs('user.subjects*') ? 'active' : '' }}">
                <i class="fas fa-book-open"></i> Môn học
            </a>
            <hr style="background-color: rgba(255,255,255,0.2); margin: 20px 0;">
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
                <h5 class="mb-0 d-inline ms-2">Hệ thống quản lý nhóm và đề tài</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="btn btn-light position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        @php
                            $invitesCount = is_int($pendingInvites ?? 0) ? ($pendingInvites ?? 0) : (isset($pendingInvites) ? $pendingInvites->count() : 0);
                            $requestsCount = is_int($pendingRequests ?? 0) ? ($pendingRequests ?? 0) : (isset($pendingRequests) ? $pendingRequests->count() : 0);
                            $totalNotif = $invitesCount + $requestsCount;
                        @endphp
                        @if($totalNotif > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $totalNotif }}
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 300px;">
                        <li class="dropdown-header"><strong>Thông báo</strong></li>
                        @if($invitesCount > 0)
                            <li>
                                <a class="dropdown-item" href="{{ route('user.invites') }}">
                                    <i class="fas fa-envelope text-primary"></i>
                                    {{ $invitesCount }} lời mời mới
                                </a>
                            </li>
                        @endif
                        @if($requestsCount > 0)
                            <li>
                                <a class="dropdown-item" href="{{ route('user.join-requests') }}">
                                    <i class="fas fa-paper-plane text-success"></i>
                                    {{ $requestsCount }} yêu cầu đang chờ
                                </a>
                            </li>
                        @endif
                        @if($totalNotif == 0)
                            <li><span class="dropdown-item text-muted">Không có thông báo mới</span></li>
                        @endif
                    </ul>
                </div>

                <!-- User Menu -->
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i>
                        {{ Auth::user()->name ?? 'User' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                       <li><a class="dropdown-item" href="{{ route('users.profile')}}"><i class="fas fa-user"></i> Hồ sơ</a></li>

                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Cài đặt</a></li>
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

        <!-- Flash Messages -->
        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle sidebar on mobile
    const toggleBtn = document.getElementById('toggle-sidebar');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    // Auto dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@stack('scripts')
</body>
</html>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - ProjectHub</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #ec4899;
            --success: #10b981;
            --info: #0ea5e9;
            --warning: #f59e0b;
            --danger: #ef4444;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Sidebar */
        .sidebar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            padding-top: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .sidebar .brand {
            text-align: center;
            margin-bottom: 40px;
            color: white;
            text-decoration: none;
        }

        .sidebar .brand h4 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .sidebar .brand small {
            color: rgba(255, 255, 255, 0.8);
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 12px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin: 0 10px;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .sidebar-menu i {
            width: 25px;
            margin-right: 15px;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }

        .navbar-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--primary);
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        /* Content */
        .content {
            flex: 1;
            padding: 30px;
        }

        /* Cards */
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 20px;
            border: none;
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }

        /* Buttons */
        .btn-custom {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-primary-custom:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
            color: white;
        }

        /* Alerts */
        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar.show {
                width: 260px;
                transform: translateX(0);
            }

            .content {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <a href="{{ route('user.dashboard') }}" class="brand">
            <h4><i class="fas fa-rocket"></i> ProjectHub</h4>
            <small>Student Portal</small>
        </a>

        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('user.dashboard') }}"
                    class="@if (request()->routeIs('user.dashboard')) active @endif">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('user.topics') }}" class="@if (request()->routeIs('user.topics*')) active @endif">
                    <i class="fas fa-book"></i> Đề tài
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('my_topics') }}">
                    <i class="fas fa-bookmark"></i> Đề tài của tôi
                </a>
            </li>
            <li>
                <a href="{{ route('user.my_groups') }}"
                    class="@if (request()->routeIs('user.my-groups*')) active @endif">
                    <i class="fas fa-users"></i> Nhóm của tôi
                </a>
            </li>
            <li>
                <a href="{{ route('user.invites') }}" class="@if (request()->routeIs('user.invites*')) active @endif">
                    <i class="fas fa-envelope"></i> Lời mời
                    @php
                        $pendingInvites = Auth::user()->invites()
                            ->where('status', 'Pending')
                            ->count();
                    @endphp
                    @if ($pendingInvites > 0)
                        <span class="badge bg-danger ms-auto">{{ $pendingInvites }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
    <a href="{{ route('user.join-requests') }}" class="nav-link {{ request()->routeIs('user.join-requests') ? 'active' : '' }}">
        <i class="fas fa-handshake"></i>
        <span>Xin tham gia nhóm</span>
        @php
            $pendingRequests = \App\Models\Join_Requests::where('member_id', auth()->id())
                ->where('status', 'Pending')
                ->count();
        @endphp
        @if($pendingRequests > 0)
            <span class="badge badge-danger ms-2">{{ $pendingRequests }}</span>
        @endif
    </a>
</li>
            <li>
                <a href="{{ route('user.join-requests') }}"
                    class="@if (request()->routeIs('user.join-requests*')) active @endif">
                    <i class="fas fa-hourglass-half"></i> Yêu cầu
                </a>
            </li>
            <li>
                <a href="{{ route('users.profile') }}">
                    <i class="fas fa-user"></i> Hồ sơ
                </a>
            </li>

            <hr style="border-color: rgba(255, 255, 255, 0.2); margin: 20px 10px;">

            <li>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-link text-white text-decoration-none w-100 text-start"
                        style="padding: 12px 20px; display: flex; align-items: center;">
                        <i class="fas fa-sign-out-alt" style="width: 25px; margin-right: 15px;"></i> Đăng xuất
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <div class="navbar-custom">
            <div class="navbar-title">
                <i class="fas fa-bars cursor-pointer d-md-none" id="toggleSidebar"></i>
                @yield('page-title', 'Dashboard')
            </div>
            <div class="navbar-user">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <small class="text-muted d-block">Xin chào</small>
                    <strong>{{ Auth::user()->name }}</strong>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle sidebar on mobile
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Close sidebar when clicking outside
        document.addEventListener('click', function (event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('toggleSidebar');

            if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                sidebar.classList.remove('show');
            }
        });
    </script>
</body>

</html>
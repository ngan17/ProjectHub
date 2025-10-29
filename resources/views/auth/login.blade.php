<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            background-color: #fff;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            width: 400px;
            padding: 40px 35px;
            animation: fadeInUp 0.6s ease-in-out;
        }

        .login-card h4 {
            font-weight: 600;
            text-align: center;
            color: #4a4a4a;
            margin-bottom: 25px;
        }

        .form-control {
            border-radius: 10px;
            padding-left: 40px;
        }

        .input-group-text {
            border: none;
            background-color: transparent;
            color: #764ba2;
            position: absolute;
            left: 10px;
            top: 9px;
            z-index: 2;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            box-shadow: 0 5px 15px rgba(118, 75, 162, 0.4);
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 10px;
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            color: #777;
            font-size: 0.9rem;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h4><i class="fas fa-lock me-2 text-primary"></i>Đăng nhập hệ thống</h4>

        @if ($errors->any())
            <div class="alert alert-danger text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf

            <div class="mb-3 position-relative">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" class="form-control" placeholder="Nhập email" required autofocus>
            </div>

            <div class="mb-4 position-relative">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
            </div>

            <button type="submit" class="btn btn-login w-100">
                <i class="fas fa-sign-in-alt me-1"></i> Đăng nhập
            </button>
        </form>

        <div class="footer-text">
            <p class="mt-3">© 2025 Quản lý Đăng ký Đề tài Nhóm</p>
        </div>
    </div>

</body>
</html>

@extends('user.layouts.app')
@section('page-title', 'Dashboard')
@section('title', 'Dashboard - Sinh viên')


@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-4">
                    <h4 class="mb-0">
                        <i class="fas fa-user"></i> Hồ sơ cá nhân
                    </h4>
                </div>

                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-circle"></i> Lỗi!
                            </h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('users.updateProfile') }}" method="POST">
                        @csrf

                        <!-- Thông tin cơ bản -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="fas fa-info-circle"></i> Thông tin cơ bản
                            </h5>
                        </div>

                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">
                                <i class="fas fa-user"></i> Tên
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="role" class="form-label fw-bold">
                                <i class="fas fa-shield-alt"></i> Vai trò
                            </label>
                            <input type="text" class="form-control" id="role" value="{{ ucfirst($user->role) }}" disabled>
                            <small class="text-muted">Không thể thay đổi vai trò</small>
                        </div>

                        <div class="mb-4">
                            <label for="class" class="form-label fw-bold">
                                <i class="fas fa-graduation-cap"></i> Lớp học
                            </label>
                            <input type="text" class="form-control @error('class') is-invalid @enderror" 
                                id="class" name="class" value="{{ old('class', $user->class ?? '') }}">
                            @error('class')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-5">

                        <!-- Đổi mật khẩu -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="fas fa-lock"></i> Đổi mật khẩu
                            </h5>
                        </div>

                        <div class="mb-4">
                            <label for="current_password" class="form-label fw-bold">
                                Mật khẩu hiện tại
                            </label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                id="current_password" name="current_password">
                            <small class="text-muted">Để trống nếu không muốn đổi mật khẩu</small>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="new_password" class="form-label fw-bold">
                                Mật khẩu mới
                            </label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                id="new_password" name="new_password">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label fw-bold">
                                Xác nhận mật khẩu mới
                            </label>
                            <input type="password" class="form-control" 
                                id="new_password_confirmation" name="new_password_confirmation">
                        </div>

                        <hr class="my-5">

                        <!-- Thông tin thêm -->
                        <div class="row text-muted small">
                            <div class="col-md-6">
                                <p><strong>Ngày tạo tài khoản:</strong> {{ $user->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Cập nhật gần nhất:</strong> {{ $user->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg flex-grow-1">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
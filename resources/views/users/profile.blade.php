@extends('layouts.user')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header với Avatar đẹp hơn -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div class="d-flex align-items-center">
                            <div class="bg-gradient bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fs-2 fw-bold shadow" 
                                 style="width: 80px; height: 80px;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="ms-4">
                                <h1 class="mb-1">{{ $user->name }}</h1>
                                <p class="text-muted mb-1">
                                    <i class="bi bi-person-badge me-1"></i>{{ ucfirst($user->role) }}
                                </p>
                                @if($user->class)
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-mortarboard me-1"></i>Lớp: {{ $user->class }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                                <i class="bi bi-house-door me-1"></i>Trang chủ
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages với icon đẹp hơn -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <strong>Có lỗi xảy ra:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Profile Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h2 class="mb-0 h4">
                        <i class="bi bi-person-lines-fill me-2"></i>Thông tin cá nhân
                    </h2>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('users.updateProfile') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <!-- Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">
                                    Họ và tên <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input 
                                        type="text" 
                                        name="name" 
                                        id="name" 
                                        value="{{ old('name', $user->name) }}"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Nhập họ và tên"
                                        required
                                    >
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        id="email" 
                                        value="{{ old('email', $user->email) }}"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="email@example.com"
                                        required
                                    >
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Role (Read-only) -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vai trò</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                    <input 
                                        type="text" 
                                        value="{{ ucfirst($user->role) }}"
                                        class="form-control bg-light"
                                        readonly
                                    >
                                </div>
                                <small class="text-muted">Không thể thay đổi vai trò</small>
                            </div>

                            <!-- Class (Read-only) -->
                            @if($user->class)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Lớp</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-mortarboard"></i></span>
                                    <input 
                                        type="text" 
                                        value="{{ $user->class }}"
                                        class="form-control bg-light"
                                        readonly
                                    >
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Change Password Section -->
                        <hr class="my-4">
                        
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h3 class="h5 mb-0">
                                <i class="bi bi-shield-lock me-2"></i>Đổi mật khẩu
                            </h3>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>Để trống nếu không muốn đổi
                            </small>
                        </div>
                        
                        <div class="row g-3">
                            <!-- Current Password - ĐÃ THÊM -->
                            <div class="col-md-4">
                                <label for="current_password" class="form-label fw-semibold">
                                    Mật khẩu hiện tại
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input 
                                        type="password" 
                                        name="current_password" 
                                        id="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        placeholder="Nhập mật khẩu hiện tại"
                                    >
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- New Password -->
                            <div class="col-md-4">
                                <label for="new_password" class="form-label fw-semibold">
                                    Mật khẩu mới
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input 
                                        type="password" 
                                        name="new_password" 
                                        id="new_password"
                                        class="form-control @error('new_password') is-invalid @enderror"
                                        placeholder="Tối thiểu 8 ký tự"
                                    >
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Ít nhất 8 ký tự</small>
                            </div>

                            <!-- Confirm New Password -->
                            <div class="col-md-4">
                                <label for="new_password_confirmation" class="form-label fw-semibold">
                                    Xác nhận mật khẩu
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input 
                                        type="password" 
                                        name="new_password_confirmation" 
                                        id="new_password_confirmation"
                                        class="form-control"
                                        placeholder="Nhập lại mật khẩu"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                Cập nhật lần cuối: {{ $user->updated_at->format('d/m/Y H:i') }}
                            </small>
                            <div class="d-flex gap-2">
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Hủy
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>Cập nhật hồ sơ
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

                    <!-- Thông tin tài khoản -->
                    <hr class="my-4">
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border-start border-primary border-3 ps-3">
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-calendar-plus me-1"></i>Ngày tạo
                                </p>
                                <p class="mb-0 fw-semibold">{{ $user->created_at->format('d/m/Y') }}</p>
                                <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border-start border-success border-3 ps-3">
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-clock-history me-1"></i>Cập nhật
                                </p>
                                <p class="mb-0 fw-semibold">{{ $user->updated_at->format('d/m/Y') }}</p>
                                <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border-start border-info border-3 ps-3">
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-hash me-1"></i>ID người dùng
                                </p>
                                <p class="mb-0 fw-semibold font-monospace">#{{ $user->user_id }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }

    .input-group .form-control {
        border-left: none;
    }

    .input-group:focus-within .input-group-text {
        border-color: #86b7fe;
    }

    .bg-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .card {
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush
@endsection
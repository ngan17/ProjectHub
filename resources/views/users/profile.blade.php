@extends('layouts.user')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fs-2 fw-bold" 
                             style="width: 80px; height: 80px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="ms-4">
                            <h1 class="mb-1">{{ $user->name }}</h1>
                            <p class="text-muted mb-1">{{ ucfirst($user->role) }}</p>
                            @if($user->class)
                                <p class="text-muted small mb-0">Lớp: {{ $user->class }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                    <h2 class="mb-0 h4">Thông tin cá nhân</h2>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('users.updateProfile') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <!-- Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    Họ và tên <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    id="name" 
                                    value="{{ old('name', $user->name) }}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    required
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    id="email" 
                                    value="{{ old('email', $user->email) }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role (Read-only) -->
                            <div class="col-md-6">
                                <label class="form-label">Vai trò</label>
                                <input 
                                    type="text" 
                                    value="{{ ucfirst($user->role) }}"
                                    class="form-control bg-light"
                                    readonly
                                >
                            </div>

                            <!-- Class (Read-only) -->
                            @if($user->class)
                            <div class="col-md-6">
                                <label class="form-label">Lớp</label>
                                <input 
                                    type="text" 
                                    value="{{ $user->class }}"
                                    class="form-control bg-light"
                                    readonly
                                >
                            </div>
                            @endif
                        </div>

                        <!-- Change Password Section -->
                        <hr class="my-4">
                        
                        <h3 class="h5 mb-3">Đổi mật khẩu</h3>
                        
                        <div class="row g-3">
                            <!-- Current Password -->
                      

                            <!-- New Password -->
                            <div class="col-md-6">
                                <label for="new_password" class="form-label">
                                    Mật khẩu mới
                                </label>
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

                            <!-- Confirm New Password -->
                            <div class="col-md-6">
                                <label for="new_password_confirmation" class="form-label">
                                    Xác nhận mật khẩu mới
                                </label>
                                <input 
                                    type="password" 
                                    name="new_password_confirmation" 
                                    id="new_password_confirmation"
                                    class="form-control"
                                >
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Cập nhật hồ sơ
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="mb-0 h4">Thông tin bổ sung</h2>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        

                       

                        <div class="col-md-3 col-sm-6">
                            <div class="border-start border-warning border-4 ps-3">
                                <p class="text-muted small mb-1">Yêu cầu tham gia</p>
                                <p class="h2 mb-0 fw-bold">{{ $user->joinRequests->count() }}</p>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="border-start border-info border-4 ps-3">
                                <p class="text-muted small mb-1">Lời mời</p>
                                <p class="h2 mb-0 fw-bold">{{ $user->invites->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
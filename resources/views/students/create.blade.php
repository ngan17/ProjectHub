@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-user-plus"></i> Thêm sinh viên mới</h4>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('students.store') }}" method="POST">
                        @csrf

                        <!-- Name -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user text-primary"></i> Họ và tên <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   class="form-control form-control-lg @error('name') is-invalid @enderror"
                                   placeholder="Nguyễn Văn A"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-envelope text-success"></i> Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   class="form-control form-control-lg @error('email') is-invalid @enderror"
                                   placeholder="student@example.com"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-lock text-danger"></i> Mật khẩu <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   name="password" 
                                   class="form-control form-control-lg @error('password') is-invalid @enderror"
                                   placeholder="Tối thiểu 6 ký tự"
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Sinh viên sẽ phải đổi mật khẩu khi đăng nhập lần đầu
                            </small>
                        </div>

                        <!-- Classes -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-chalkboard-teacher text-warning"></i> Lớp học phần <span class="text-danger">*</span>
                            </label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                @foreach($classes as $class)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="class_ids[]" 
                                               value="{{ $class->class_id }}"
                                               id="class_{{ $class->class_id }}"
                                               {{ (is_array(old('class_ids')) && in_array($class->class_id, old('class_ids'))) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="class_{{ $class->class_id }}">
                                            {{ $class->class_name }} - {{ $class->subject->subject_name ?? '' }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('class_ids')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-info btn-lg text-white">
                                <i class="fas fa-save"></i> Lưu sinh viên
                            </button>
                            <a href="{{ route('students.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
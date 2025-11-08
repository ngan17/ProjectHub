@extends('layouts.user')

@section('title', 'Danh sách lớp học')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="fw-bold mb-2">Danh sách lớp học</h2>
        <p class="text-muted">Tất cả các lớp học trong hệ thống</p>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('user.classes') }}">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-search me-1"></i> Tìm kiếm
                        </label>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Tên lớp học..."
                               class="form-control">
                    </div>

                    <!-- Subject Filter -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-book me-1"></i> Môn học
                        </label>
                        <select name="subject_id" class="form-select">
                            <option value="">Tất cả môn học</option>
                            @foreach($subjects ?? [] as $subject)
                                <option value="{{ $subject->subject_id }}" {{ request('subject_id') == $subject->subject_id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i> Tìm kiếm
                    </button>
                    <a href="{{ route('user.classes') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i> Đặt lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Classes Grid -->
    @if($classes->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-chalkboard fa-4x text-muted mb-3"></i>
                <h5 class="text-muted mb-2">Không tìm thấy lớp học</h5>
                <p class="text-muted">Vui lòng thử lại với bộ lọc khác</p>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($classes as $class)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 hover-card">
                        <!-- Card Header -->
                        <div class="card-header text-white p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <h5 class="mb-2 fw-bold">{{ $class->class_name }}</h5>
                            <div class="d-flex align-items-center text-white-50">
                                <i class="fas fa-users me-2"></i>
                                <span>{{ $class->groups->count() }} nhóm</span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-4">
                            @if($class->subject)
                                <div class="mb-3">
                                    <p class="text-uppercase text-muted small mb-1">Môn học</p>
                                    <p class="fw-semibold mb-1">{{ $class->subject->name }}</p>
                                    <p class="text-muted small">{{ $class->subject->subject_code ?? '' }}</p>
                                </div>
                            @endif

                            @if($class->subject && $class->subject->lecturer)
                                <div class="mb-3">
                                    <p class="text-uppercase text-muted small mb-1">Giảng viên</p>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-center me-2" 
                                             style="width: 32px; height: 32px;">
                                            <strong>{{ strtoupper(substr($class->subject->lecturer->name ?? 'L', 0, 1)) }}</strong>
                                        </div>
                                        <span class="fw-semibold">{{ $class->subject->lecturer->name }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer bg-white border-top-0 p-4 pt-0">
                            <a href="{{ route('user.class.detail', $class->class_id) }}" 
                               class="btn btn-primary w-100">
                                <i class="fas fa-eye me-2"></i>
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($classes->hasPages())
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    {{ $classes->links() }}
                </div>
            </div>
        @endif
    @endif
</div>

<style>
    .hover-card {
        transition: all 0.3s ease;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endsection
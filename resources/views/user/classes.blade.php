@extends('user.layouts.app')
@section('page-title', 'Classes')
@section('title', 'Danh sách lớp học')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-primary">
                            <i class="fas fa-graduation-cap"></i> Danh sách lớp học
                        </h2>
                        <p class="text-muted">Tổng cộng: <strong>{{ $classes->total() }}</strong> lớp học</p>
                    </div>
                    @if ($userClass)
                        <div>
                            <span class="badge bg-success p-2">
                                <i class="fas fa-check-circle"></i> Lớp của bạn: {{ $userClass->class_name }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Danh sách lớp học -->
        <div class="row">
            @forelse ($classes as $class)
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title text-primary fw-bold mb-1">
                                        {{ $class->class_name }}
                                    </h5>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-book"></i>
                                        <strong>{{ $class->subject->subject_code }}</strong> - {{ $class->subject->subject_name }}
                                    </p>
                                </div>
                                @if ($userClass && $userClass->class_id === $class->class_id)
                                    <span class="badge bg-success">Lớp của bạn</span>
                                @endif
                            </div>

                            <!-- Thông tin giảng viên -->
                            <div class="mb-3 p-2 bg-light rounded">
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-chalkboard-user"></i> Giảng viên:
                                </small>
                                <small class="fw-bold">
                                    {{ $class->subject->lecturer->name ?? 'N/A' }}
                                </small>
                                <br>
                                <small class="text-muted">
                                    {{ $class->subject->lecturer->email ?? 'N/A' }}
                                </small>
                            </div>

                            <!-- Thống kê -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="text-center p-2 bg-primary bg-opacity-10 rounded">
                                        <h6 class="text-primary fw-bold mb-0">
                                            {{ $class->groups->count() }}
                                        </h6>
                                        <small class="text-muted">Nhóm</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                                        <h6 class="text-success fw-bold mb-0">
                                            {{ $class->subject->topics->count() }}
                                        </h6>
                                        <small class="text-muted">Đề tài</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Nút hành động -->
                            <div class="d-grid gap-2">
                                <a href="{{ route('user.class-detail', $class->class_id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-arrow-right"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center py-5" role="alert">
                        <i class="fas fa-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-3 mb-0">Hiện không có lớp học nào</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($classes->hasPages())
            <div class="row mt-4">
                <div class="col-12">
                    <nav aria-label="Page navigation">
                        {{ $classes->links('pagination::bootstrap-4') }}
                    </nav>
                </div>
            </div>
        @endif

        <!-- Nút quay lại -->
        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại Dashboard
                </a>
            </div>
        </div>
    </div>

    <style>
        .transition {
            transition: all 0.3s ease;
        }
        .hover-shadow:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endsection
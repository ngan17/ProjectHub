@extends('user.layouts.app')
@section('title', 'Danh sách đề tài')
@section('page-title', 'Danh sách đề tài')
@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-primary">
                <i class="fas fa-book"></i> Danh sách đề tài
            </h2>
            <p class="text-muted">Khám phá và đăng ký đề tài cho nhóm của bạn</p>
        </div>
    </div>

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

    <!-- Filter & Search Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('user.topics') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Tên đề tài, giảng viên..." 
                           value="{{ request('search') }}">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-book-open"></i> Môn học
                    </label>
                    <select name="subject_id" class="form-select">
                        <option value="">Tất cả môn học</option>
                        @foreach($userClasses->unique('subject_id') as $class)
                            @if($class->subject)
                                <option value="{{ $class->subject_id }}" 
                                    {{ request('subject_id') == $class->subject_id ? 'selected' : '' }}>
                                    {{ $class->subject->subject_code }} - {{ $class->subject->subject_name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-filter"></i> Trạng thái
                    </label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>
                            Còn trống
                        </option>
                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>
                            Đã có nhóm
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary bg-gradient text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-list-ul fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-white mb-0">Tổng số đề tài</h6>
                            <h3 class="mb-0 fw-bold">{{ $topics->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success bg-gradient text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-white mb-0">Còn trống</h6>
                            <h3 class="mb-0 fw-bold">{{ $topics->filter(fn($t) => !$t->assignedGroup)->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-info bg-gradient text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-white mb-0">Đã có nhóm</h6>
                            <h3 class="mb-0 fw-bold">{{ $topics->filter(fn($t) => $t->assignedGroup)->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Topics Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-list"></i> Kết quả tìm kiếm
                <span class="badge bg-primary">{{ $topics->total() }} đề tài</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($topics->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th style="width: 30%;">Tên đề tài</th>
                                <th style="width: 15%;">Môn học</th>
                                <th style="width: 15%;">Giảng viên</th>
                                <th style="width: 20%;">Mô tả</th>
                                <th style="width: 10%;" class="text-center">Trạng thái</th>
                                <th style="width: 5%;" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topics as $index => $topic)
                                <tr class="topic-row">
                                    <td class="text-center text-muted fw-semibold">
                                        {{ ($topics->currentPage() - 1) * $topics->perPage() + $index + 1 }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-primary">
                                            {{ Str::limit($topic->name, 60) }}
                                        </div>
                                        @if($topic->goal)
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-bullseye"></i> {{ Str::limit($topic->goal, 50) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($topic->subject)
                                            <span class="badge bg-light text-dark border">
                                                {{ $topic->subject->subject_code }}
                                            </span>
                                            <small class="d-block text-muted mt-1">
                                                {{ Str::limit($topic->subject->subject_name, 20) }}
                                            </small>
                                        @else
                                            <span class="text-muted">Chưa xác định</span>
                                        @endif
                                    </td>
                                    <td>
                                        <i class="fas fa-user-tie text-primary"></i>
                                        {{ Str::limit($topic->lecturer, 25) }}
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ Str::limit($topic->description, 60) }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        @if($topic->assignedGroup)
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-users"></i> Đã có nhóm
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Còn trống
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('user.topic-detail', $topic->topic_id) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           data-bs-toggle="tooltip" 
                                           title="Xem chi tiết & đăng ký">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Không tìm thấy đề tài nào</h5>
                    <p class="text-muted">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                    <a href="{{ route('user.topics') }}" class="btn btn-outline-primary">
                        <i class="fas fa-redo"></i> Xem tất cả
                    </a>
                </div>
            @endif
        </div>
        
        @if($topics->hasPages())
            <div class="card-footer bg-white border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Hiển thị {{ $topics->firstItem() }} - {{ $topics->lastItem() }} 
                        trong tổng số {{ $topics->total() }} đề tài
                    </div>
                    <div>
                        {{ $topics->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .topic-row {
        transition: background-color 0.2s ease;
    }
    .topic-row:hover {
        background-color: #f8f9fa;
    }
    .card {
        border-radius: 10px;
    }
    .table th {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge {
        font-weight: 500;
        padding: 0.4em 0.7em;
    }
</style>

<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
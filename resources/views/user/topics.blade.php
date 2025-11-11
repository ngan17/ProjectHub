@extends('layouts.user')

@section('title', 'Danh sách đề tài')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="fw-bold mb-2">Danh sách đề tài</h2>
        <p class="text-muted">Tìm kiếm và đăng ký đề tài phù hợp cho nhóm của bạn</p>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('user.topics') }}">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-search me-1"></i> Tìm kiếm
                        </label>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Tên đề tài, giảng viên..."
                               class="form-control">
                    </div>

                    <!-- Class Filter -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-chalkboard me-1"></i> Lớp học
                        </label>
                        <select name="class_id" class="form-select">
                            <option value="">Tất cả lớp</option>
                            @foreach($userClasses as $class)
                                <option value="{{ $class->class_id }}" {{ request('class_id') == $class->class_id ? 'selected' : '' }}>
                                    {{ $class->class_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subject Filter -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-book me-1"></i> Môn học
                        </label>
                        <select name="subject_id" class="form-select">
                            <option value="">Tất cả môn học</option>
                            @foreach($userClasses->pluck('subject')->unique('subject_id') as $subject)
                                @if($subject)
                                    <option value="{{ $subject->subject_id }}" {{ request('subject_id') == $subject->subject_id ? 'selected' : '' }}>
                                        {{ $subject->subject_name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-filter me-1"></i> Trạng thái
                        </label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="status" id="status-all" value="" {{ !request('status') ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="status-all">
                                <i class="fas fa-list me-1"></i> Tất cả
                            </label>

                            <input type="radio" class="btn-check" name="status" id="status-available" value="available" {{ request('status') == 'available' ? 'checked' : '' }}>
                            <label class="btn btn-outline-success" for="status-available">
                                <i class="fas fa-check-circle me-1"></i> Còn trống
                            </label>

                            <input type="radio" class="btn-check" name="status" id="status-assigned" value="assigned" {{ request('status') == 'assigned' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary" for="status-assigned">
                                <i class="fas fa-user-check me-1"></i> Đã có nhóm
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i> Tìm kiếm
                    </button>
                    <a href="{{ route('user.topics') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i> Đặt lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Topics List -->
    @if($topics->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-lightbulb fa-4x text-muted mb-3"></i>
                <h5 class="text-muted mb-2">Không tìm thấy đề tài</h5>
                <p class="text-muted">Vui lòng thử lại với bộ lọc khác</p>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($topics as $topic)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 hover-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h6 class="fw-bold mb-0" style="min-height: 48px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $topic->name }}
                                </h6>
                                @if($topic->assignedGroup)
                                    <span class="badge bg-danger ms-2">Đã có nhóm</span>
                                @else
                                    <span class="badge bg-success ms-2">Còn trống</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-user-tie me-1"></i>
                                    {{ $topic->lecturer }}
                                </p>
                                
                                @if($topic->subject)
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-book me-1"></i>
                                        {{ $topic->subject->name }}
                                    </p>
                                @endif

                                @if($topic->description)
                                    <p class="text-muted small mb-0" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $topic->description }}
                                    </p>
                                @endif
                            </div>

                            @if($topic->assignedGroup)
                                <div class="alert alert-light border mb-3 py-2">
                                    <small class="text-muted">
                                        <i class="fas fa-users me-1"></i>
                                        Nhóm: {{ $topic->assignedGroup->group_name }}
                                    </small>
                                </div>
                            @endif

                            <a href="{{ route('user.topic_detail', $topic->topic_id) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-eye me-1"></i> Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($topics->hasPages())
            <div class="mt-4">
                <div class="d-flex justify-content-center">
                    {{ $topics->links() }}
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
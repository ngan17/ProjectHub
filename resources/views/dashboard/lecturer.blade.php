{{-- resources/views/dashboard/lecturer.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    

    {{-- Overall Statistics --}}
    <div class="row mb-5">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Tổng số lớp</h6>
                            <h2 class="mb-0 text-primary">{{ $stats['total_classes'] }}</h2>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="fas fa-chalkboard-teacher fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Tổng đề tài</h6>
                            <h2 class="mb-0 text-info">{{ $stats['assigned_topics'] }}</h2>
                        </div>
                        <div class="rounded-circle bg-info bg-opacity-10 p-3">
                            <i class="fas fa-book fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Chờ duyệt</h6>
                            <h2 class="mb-0 text-warning">{{ $stats['pending_requests'] }}</h2>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Đã duyệt</h6>
                            <h2 class="mb-0 text-success">{{ $stats['approved_topics'] }}</h2>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Classes Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold">Các lớp phụ trách</h3>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-th-large me-2"></i>Xem tất cả
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($lecturerClasses as $class)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white">{{ $class->class_code }}</h5>
                        <span class="badge bg-white text-dark">{{ $class->semester ?? 'HK1' }} - {{ $class->year ?? '2024' }}</span>
                    </div>
                </div>
                
                <div class="card-body">
                    <h6 class="card-title fw-bold text-dark mb-3">{{ $class->class_name }}</h6>
                    
                    {{-- Class Statistics --}}
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <h5 class="mb-1 text-primary">{{ $classStats[$class->class_id]['topics'] ?? 0 }}</h5>
                                <small class="text-muted">Đề tài</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <h5 class="mb-1 text-warning">{{ $classStats[$class->class_id]['pending'] ?? 0 }}</h5>
                                <small class="text-muted">Chờ duyệt</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <h5 class="mb-1 text-success">{{ $classStats[$class->class_id]['approved'] ?? 0 }}</h5>
                                <small class="text-muted">Đã duyệt</small>
                            </div>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
@php
    $totalTopics = $classStats[$class->class_id]['topics'] ?? 0;
    $approvedTopics = $classStats[$class->class_id]['approved'] ?? 0;
    $progress = $totalTopics > 0 ? ($approvedTopics / $totalTopics) * 100 : 0;
    $progressWidth = round($progress) . '%';
@endphp

<div class="mb-3">
    <small class="text-muted">Tiến độ phân công</small>
    <div class="progress" style="height: 8px;">
        <div class="progress-bar bg-success" role="progressbar" 
    style="width: {{ round($progress, 0) }}%;"

             aria-valuenow="{{ round($progress) }}" 
             aria-valuemin="0" 
             aria-valuemax="100">
        </div>
    </div>
    <small class="text-muted">{{ round($progress) }}% đề tài đã được nhận</small>
</div>

                    {{-- Action Buttons --}}
                    <div class="d-grid gap-2">
                        <a href="{{ route('dashboard.class.detail', $class->class_id) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-eye me-2"></i>Xem chi tiết
                        </a>
                        <a href="{{ route('topics.index', ['class_id' => $class->class_id]) }}" 
                           class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list me-2"></i>Danh sách đề tài
                        </a>
                    </div>
                </div>

                <div class="card-footer bg-transparent text-muted small">
                    <i class="fas fa-users me-1"></i> 
                    {{ $classStats[$class->class_id]['students'] ?? 0 }} sinh viên
                    <span class="float-end">
                        <i class="fas fa-user-friends me-1"></i>
                        {{ $classStats[$class->class_id]['groups'] ?? 0 }} nhóm
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h5>Chưa có lớp nào được phân công</h5>
                <p class="mb-0">Vui lòng liên hệ admin để được phân công lớp giảng dạy.</p>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Recent Activities --}}
    @if($recentRequests && $recentRequests->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="fw-bold mb-3">Hoạt động gần đây</h3>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Lớp</th>
                                    <th>Đề tài</th>
                                    <th>Nhóm</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentRequests as $request)
                                <tr>
                                    <td>
                                        <small class="text-muted">
                                            {{ $request->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $request->topic->class->class_code ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($request->topic->name, 30) }}</td>
                                    <td>{{ $request->group->group_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($request->status == 'Pending')
                                            <span class="badge bg-warning">Chờ duyệt</span>
                                        @elseif($request->status == 'Accepted')
                                            <span class="badge bg-success">Đã duyệt</span>
                                        @else
                                            <span class="badge bg-danger">Từ chối</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->status == 'Pending')
                                            <button class="btn btn-sm btn-success approve-btn" 
                                                    data-request-id="{{ $request->request_id }}">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger reject-btn" 
                                                    data-request-id="{{ $request->request_id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.bg-gradient {
    background: linear-gradient(135deg, #667eea, #764ba2);
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Approve buttons
    document.querySelectorAll('.approve-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var requestId = this.getAttribute('data-request-id');
            if (confirm('Bạn có chắc chắn muốn duyệt yêu cầu này?')) {
                approveRequest(requestId);
            }
        });
    });

    // Reject buttons
    document.querySelectorAll('.reject-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var requestId = this.getAttribute('data-request-id');
            if (confirm('Bạn có chắc chắn muốn từ chối yêu cầu này?')) {
                rejectRequest(requestId);
            }
        });
    });
});

function approveRequest(requestId) {
    // Add approval logic here, e.g., AJAX call
    console.log('Approving request ' + requestId);
}

function rejectRequest(requestId) {
    // Add rejection logic here, e.g., AJAX call
    console.log('Rejecting request ' + requestId);
}
</script>
@endsection
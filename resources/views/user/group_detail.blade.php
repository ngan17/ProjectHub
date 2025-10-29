@extends('user.layouts.app')
@section('page-title', 'Dashboard')
@section('title', 'Chi tiết nhóm')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('user.my_groups') }}" class="btn btn-secondary mb-3">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <h2 class="fw-bold text-primary">
                    {{ $group->group_name }}
                </h2>
            </div>
        </div>
@include('user.partials.group-join-section')
        <div class="row">
            <!-- Thông tin nhóm -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle"></i> Thông tin nhóm
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="text-muted small fw-bold">TRƯỞNG NHÓM</label>
                            <p class="mb-0 fs-5">{{ $group->leader->name }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small fw-bold">SỐ THÀNH VIÊN</label>
                            <p class="mb-0 fs-5">
                                <span class="badge bg-info">{{ $members->count() }} thành viên</span>
                            </p>
                        </div>

                        @if ($group->topic_id)
                            <div class="mb-4">
                                <label class="text-muted small fw-bold">ĐỀ TÀI</label>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-2 fw-bold text-primary">{{ $group->topic->name }}</p>
                                    <p class="mb-2 small">
                                        <strong>Giảng viên:</strong> {{ $group->topic->lecturer }}
                                    </p>
                                    <p class="mb-0 small text-muted">{{ $group->topic->description }}</p>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Nhóm chưa có đề tài
                            </div>
                        @endif

                        <div class="mb-0">
                            <label class="text-muted small fw-bold">NGÀY TẠO</label>
                            <p class="mb-0">{{ $group->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách thành viên -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-users"></i> Danh sách thành viên
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($members->isEmpty())
                            <p class="text-muted text-center py-4">
                                Không có thành viên nào
                            </p>
                        @else
                            <div class="list-group">
                                @foreach ($members as $member)
                                    <div class="list-group-item border-bottom py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $member->name }}</h6>
                                                <small class="text-muted">
                                                    Lớp: {{ $member->class ?? 'N/A' }}
                                                </small>
                                            </div>
                                            @if ($member->user_id === $group->leader_id)
                                                <span class="badge bg-warning">Trưởng nhóm</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Đề tài chi tiết (nếu có) -->
        @if ($group->topic_id)
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-info text-white py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-book"></i> Chi tiết đề tài
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted">GIẢNG VIÊN HƯỚNG DẪN</h6>
                                    <p class="fs-5 fw-bold">{{ $group->topic->lecturer }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">NGÀY ĐƯỢC DUYỆT</h6>
                                    <p class="fs-5 fw-bold">
                                        @php
                                            $approvedRequest = App\Models\Topic_requests::where('group_id', $group->group_id)
                                                ->where('topic_id', $group->topic_id)
                                                ->where('status', 'Approved')
                                                ->first();
                                        @endphp
                                        {{ $approvedRequest?->created_at?->format('d/m/Y') ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-muted">MÔ TẢ</h6>
                                <div class="bg-light p-3 rounded">
                                    {{ nl2br($group->topic->description) }}
                                </div>
                            </div>

                            @if ($group->topic->goal)
                                <div class="mb-4">
                                    <h6 class="text-muted">MỤC TIÊU</h6>
                                    <div class="bg-light p-3 rounded border-start border-4 border-success">
                                        {{ nl2br($group->topic->goal) }}
                                    </div>
                                </div>
                            @endif

                            @if ($group->topic->requirements)
                                <div class="mb-0">
                                    <h6 class="text-muted">YÊU CẦU</h6>
                                    <div class="bg-light p-3 rounded border-start border-4 border-warning">
                                        {{ nl2br($group->topic->requirements) }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

{{-- =============== COMPONENT 1: NÚT XIN VÀO NHÓM (cho user thường) =============== --}}
{{-- Thêm code này vào phần nút actions trong group_detail.blade.php --}}

@php
    $user = Auth::user();
    $isMember = $group->members->contains('user_id', $user->user_id) || $group->leader_id == $user->user_id;
    $hasPendingRequest = \App\Models\Join_Requests::where('group_id', $group->group_id)
        ->where('member_id', $user->user_id)
        ->where('status', 'Pending')
        ->exists();
@endphp

@if (!$isMember)
    @if ($hasPendingRequest)
        <button class="btn btn-warning" disabled>
            <i class="fas fa-hourglass-half"></i> Đã gửi yêu cầu
        </button>
    @else
        <form action="{{ route('user.send-join-request') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="group_id" value="{{ $group->group_id }}">
            <button type="submit" class="btn btn-primary" 
                onclick="return confirm('Bạn có chắc muốn gửi yêu cầu tham gia nhóm này?');">
                <i class="fas fa-sign-in-alt"></i> Xin vào nhóm
            </button>
        </form>
    @endif
@endif



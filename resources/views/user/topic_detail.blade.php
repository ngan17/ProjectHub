@extends('layouts.user')

@section('title', 'Chi tiết đề tài')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.topics') }}">Đề tài</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($topic->name, 50) }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Topic Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <h4 class="mb-0 fw-bold text-white">{{ $topic->name }}</h4>
                        @if($topic->assignedGroup)
                            <span class="badge bg-danger">Đã có nhóm</span>
                        @else
                            <span class="badge bg-success">Còn trống</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background-color: #e3f2fd;">
                                <p class="text-muted small mb-1">Giảng viên hướng dẫn</p>
                                <p class="fw-semibold mb-0">{{ $topic->lecturer }}</p>
                            </div>
                        </div>
                        @if($topic->subject)
                            <div class="col-md-6">
                                <div class="p-3 rounded" style="background-color: #f3e5f5;">
                                    <p class="text-muted small mb-1">Môn học</p>
                                    <p class="fw-semibold mb-0">{{ $topic->subject->name }}</p>
                                    <p class="text-muted small mb-0">{{ $topic->subject->subject_code }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($topic->description)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Mô tả đề tài</h6>
                            <p class="text-muted">{{ $topic->description }}</p>
                        </div>
                    @endif

                    @if($topic->assignedGroup)
                        <div class="alert alert-info border-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Đề tài này đã được nhóm <strong>{{ $topic->assignedGroup->group_name }}</strong> đăng ký
                        </div>
                    @endif
                </div>
            </div>

            <!-- Available Classes -->
            @if($topic->subject && $topic->subject->classes->isNotEmpty())
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-chalkboard text-primary me-2"></i>
                            Các lớp học
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="list-group list-group-flush">
                            @foreach($topic->subject->classes as $class)
                                <div class="list-group-item px-0 py-3 {{ $userClass && $userClass->class_id == $class->class_id ? 'bg-light' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $class->class_name }}</h6>
                                            <p class="text-muted small mb-0">
                                                <i class="fas fa-users me-1"></i>
                                                {{ $class->groups->count() }} nhóm
                                            </p>
                                        </div>
                                        @if($userClass && $userClass->class_id == $class->class_id)
                                            <span class="badge bg-primary">Lớp của bạn</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Register Action -->
            @if(!$topic->assignedGroup)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-clipboard-check text-success me-2"></i>
                            Đăng ký đề tài
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if($myGroups->isEmpty())
                            <div class="alert alert-warning border-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Bạn chưa có nhóm nào. Vui lòng tạo hoặc tham gia nhóm trước.
                            </div>
                            <a href="{{ route('user.my-groups') }}" class="btn btn-primary w-100">
                                <i class="fas fa-users me-2"></i>
                                Quản lý nhóm
                            </a>
                        @else
                            <p class="text-muted small mb-3">Chọn nhóm để đăng ký đề tài này:</p>
                            <form action="{{ route('user.register-topic') }}" method="POST">
                                @csrf
                                <input type="hidden" name="topic_id" value="{{ $topic->topic_id }}">
                                
                                <div class="mb-3">
                                    <select name="group_id" class="form-select" required>
                                        <option value="">-- Chọn nhóm --</option>
                                        @foreach($myGroups as $group)
                                            @php
                                                $isLeader = $group->leader_id == Auth::id();
                                                $hasRegistered = in_array($group->group_id, $groupsRegistered);
                                            @endphp
                                            @if($isLeader && !$group->topic_id)
                                                <option value="{{ $group->group_id }}" {{ $hasRegistered ? 'disabled' : '' }}>
                                                    {{ $group->group_name }}
                                                    @if($hasRegistered)
                                                        (Đã đăng ký)
                                                    @endif
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Chỉ trưởng nhóm mới có thể đăng ký
                                    </small>
                                </div>

                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Đăng ký ngay
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Topic Requests -->
            @if($topic->topic_requests->where('status', 'Pending')->isNotEmpty())
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-clock text-warning me-2"></i>
                            Yêu cầu đăng ký
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">
                            Có {{ $topic->topic_requests->where('status', 'Pending')->count() }} nhóm đang chờ duyệt
                        </p>
                        <div class="list-group list-group-flush">
                            @foreach($topic->topic_requests->where('status', 'Pending') as $request)
                                <div class="list-group-item px-0 py-2">
                                    <small class="text-muted">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $request->group->group_name }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- My Groups -->
            @if($myGroups->isNotEmpty())
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-users text-primary me-2"></i>
                            Nhóm của tôi
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="list-group list-group-flush">
                            @foreach($myGroups as $group)
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $group->group_name }}</h6>
                                            <small class="text-muted d-block">
                                                {{ $group->members->count() + 1 }} thành viên
                                            </small>
                                            @if($group->topic)
                                                <small class="text-success d-block mt-1">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Đã có đề tài
                                                </small>
                                            @endif
                                        </div>
                                        @if($group->leader_id == Auth::id())
                                            <span class="badge bg-primary">Trưởng nhóm</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
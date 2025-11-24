@extends('layouts.user')

@section('title', 'Chi tiết nhóm')

@section('content')
    <div class="container-fluid">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="{{ route('user.my_groups') }}" class="btn btn-link text-primary p-0">
                <i class="fas fa-arrow-left me-2"></i> Quay lại danh sách nhóm
            </a>
        </div>

        <!-- Group Header -->
        <div class="card border-0 shadow-sm mb-4 text-white"
            style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="mb-3 fw-bold">{{ $group->group_name }}</h1>
                        <div class="d-flex flex-wrap gap-3">
                            <span class="d-flex align-items-center">
                                <i class="fas fa-user-friends me-2"></i>
                                {{ $memberCount + 1 }} thành viên
                            </span>
                            @if($group->class)
                                <span class="d-flex align-items-center">
                                    <i class="fas fa-chalkboard me-2"></i>
                                    {{ $group->class->class_name }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if(Auth::id() == $group->leader_id)
                        <div class="col-lg-4 mt-3 mt-lg-0">
                            <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                                <a href="{{ route('user.invite-member', $group->group_id) }}" class="btn btn-light">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Mời thành viên
                                </a>
                                <a href="{{ route('user.group-join-requests', $group->group_id) }}" class="btn btn-light">
                                    <i class="fas fa-inbox me-2"></i>
                                    Yêu cầu tham gia
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Topic Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-book text-success me-2"></i>
                            Đề tài
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if($group->topic)
                            <div>
                                <h5 class="fw-bold mb-3">{{ $group->topic->name }}</h5>
                                <p class="text-muted mb-4">{{ $group->topic->description }}</p>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="p-3 rounded" style="background-color: #e3f2fd;">
                                            <p class="text-muted small mb-1">Giảng viên hướng dẫn</p>
                                            <p class="fw-semibold mb-0">{{ $group->topic->lecturer }}</p>
                                        </div>
                                    </div>

                                    @if($group->topic->subject)
                                        <div class="col-md-6">
                                            <div class="p-3 rounded" style="background-color: #f3e5f5;">
                                                <p class="text-muted small mb-1">Môn học</p>
                                                <p class="fw-semibold mb-0">{{ $group->topic->subject->name }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <a href="{{ route('user.topic_detail', $group->topic->topic_id) }}" class="btn btn-primary">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Xem chi tiết đề tài
                                </a>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                                <p class="text-muted mb-3">Nhóm chưa đăng ký đề tài</p>

                                @if(Auth::id() == $group->leader_id)
                                    <a href="{{ route('user.topics') }}" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i>
                                        Tìm đề tài
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Class Information -->
                @if($group->class)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-chalkboard text-primary me-2"></i>
                                Thông tin lớp học
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <p class="text-muted small mb-1">Lớp</p>
                                <p class="h5 fw-bold">{{ $group->class->class_name }}</p>
                            </div>

                            @if($group->class->subject)
                                <div class="mb-3">
                                    <p class="text-muted small mb-1">Môn học</p>
                                    <p class="fw-semibold mb-1">{{ $group->class->subject->name }}</p>
                                    <p class="text-muted small">{{ $group->class->subject->subject_code ?? '' }}</p>
                                </div>

                                @if($group->class->subject->lecturer)
                                    <div class="mb-4">
                                        <p class="text-muted small mb-1">Giảng viên</p>
                                        <p class="fw-semibold">{{ $group->class->subject->lecturer->name }}</p>
                                    </div>
                                @endif
                            @endif

                            <a href="{{ route('user.class_detail', $group->class->class_id) }}" class="btn btn-primary">
                                <i class="fas fa-external-link-alt me-2"></i>
                                Xem thông tin lớp
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Leader Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-crown text-warning me-2"></i>
                            Trưởng nhóm
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold me-3"
                                style="width: 60px; height: 60px; font-size: 1.5rem;">
                                {{ strtoupper(substr($group->leader->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="fw-bold mb-1">{{ $group->leader->name }}</p>
                                <p class="text-muted small mb-0">{{ $group->leader->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Members List -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-users text-primary me-2"></i>
                            Thành viên ({{ $memberCount }})
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if($members->isEmpty())
                            <p class="text-muted text-center py-3 mb-0">Chưa có thành viên nào</p>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($members as $member)
                                    <div class="list-group-item px-0 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white fw-bold me-3"
                                                style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($member->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="fw-semibold mb-0">{{ $member->name }}</p>
                                                <p class="text-muted small mb-0">{{ $member->email }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(Auth::id() == $group->leader_id)
                            <a href="{{ route('user.invite-member', $group->group_id) }}" class="btn btn-success w-100 mt-3">
                                <i class="fas fa-user-plus me-2"></i>
                                Mời thêm thành viên
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                @if(Auth::id() != $group->leader_id && $members->contains('user_id', Auth::id()))
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-cog text-secondary me-2"></i>
                                Hành động
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('user.leave_group', $group->group_id) }}"
                                onsubmit="return confirm('Bạn có chắc muốn rời khỏi nhóm này?');">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    Rời khỏi nhóm
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
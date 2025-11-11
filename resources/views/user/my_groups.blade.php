@extends('layouts.user')

@section('title', 'Nhóm của tôi')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-2">Nhóm của tôi</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Nhóm của tôi</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('user.create_group') }}" class="btn btn-outline-primary">
                <i class="fas fa-eye me-2"></i>Tạo nhóm mới
            </a>
            @if($userClasses)
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#findGroupModal">
                    <i class="fas fa-search me-2"></i>Tìm nhóm để tham gia
                </button>
            @endif
        </div>

        <!-- Groups Grid -->
        @if($groups->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted mb-3">Bạn chưa có nhóm nào</h5>
                    @if($userClasses->count() > 0)
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#findGroupModal">
                            <i class="fas fa-search me-2"></i>Tìm nhóm để tham gia
                        </button>
                    @endif
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach($groups as $group)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100 hover-card">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="mb-0 fw-bold">{{ $group->group_name }}</h5>
                                    @if($group->leader_id == Auth::id())
                                        <span class="badge bg-primary">Trưởng nhóm</span>
                                    @else
                                        <span class="badge bg-secondary">Thành viên</span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-book me-2"></i>
                                        <strong>Lớp:</strong> {{ $group->class->class_name ?? 'N/A' }}
                                    </p>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-user-tie me-2"></i>
                                        <strong>Trưởng nhóm:</strong> {{ $group->leader->name }}
                                    </p>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-users me-2"></i>
                                        <strong>Thành viên:</strong> {{ $group->members->count() + 1 }} người
                                    </p>

                                    @if($group->topic)
                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-lightbulb me-2"></i>
                                            <strong>Đề tài:</strong> {{ Str::limit($group->topic->name, 40) }}
                                        </p>
                                    @else
                                        <p class="text-danger small mb-0">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            Chưa có đề tài
                                        </p>
                                    @endif
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="{{ route('user.group_detail', $group->group_id) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-2"></i>Chi tiết
                                    </a>

                                    @if($group->leader_id == Auth::id())
                                        <a href="{{ route('user.invite-member', $group->group_id) }}" class="btn btn-outline-success">
                                            <i class="fas fa-user-plus me-2"></i>Mời thành viên
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($groups->hasPages())
                <div class="mt-4">
                    <div class="d-flex justify-content-center">
                        {{ $groups->links() }}
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- Modal Find Groups -->
    @if($userClasses)
        <div class="modal fade" id="findGroupModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tìm nhóm để tham gia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @foreach($userClasses as $class)
                            <div class="mb-4">
                                <div class="alert alert-info border-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Danh sách các nhóm trong lớp
                                </div>

                                <div class="row g-3">
                                    @forelse($class->groups as $classGroup)
                                        @php
                                            $isMember = $classGroup->members->contains('user_id', Auth::id()) || $classGroup->leader_id == Auth::id();
                                            $hasPendingRequest = $classGroup->joinRequests()
                                                ->where('member_id', Auth::id())
                                                ->where('status', 'Pending')
                                                ->exists();
                                        @endphp

                                        <div class="col-md-6 col-lg-4">
                                            <div class="card h-100 {{ $isMember ? 'border-success' : '' }}">
                                                <div class="card-body">
                                                    <h6 class="mb-2 fw-bold">{{ $classGroup->group_name }}</h6>

                                                    <p class="text-muted small mb-2">
                                                        <i class="fas fa-user-tie me-1"></i>
                                                        {{ $classGroup->leader->name }}
                                                    </p>

                                                    <p class="text-muted small mb-3">
                                                        <i class="fas fa-users me-1"></i>
                                                        {{ $classGroup->members->count() + 1 }} thành viên
                                                    </p>

                                                    @if($isMember)
                                                        <span class="badge bg-success w-100">Đã tham gia</span>
                                                    @elseif($hasPendingRequest)
                                                        <span class="badge bg-warning w-100">Đang chờ duyệt</span>
                                                    @else
                                                        <form action="{{ route('user.send-join-request') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="group_id" value="{{ $classGroup->group_id }}">
                                                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                                                <i class="fas fa-paper-plane me-1"></i>Xin tham gia
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center py-3">
                                            <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                            <p class="text-muted">Lớp này chưa có nhóm nào</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

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
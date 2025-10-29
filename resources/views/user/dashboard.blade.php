@extends('user.layouts.app')
@section('page-title', 'Dashboard')
@section('title', 'Dashboard - Sinh viên')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-primary">
                    <i class="fas fa-tachometer-alt"></i> Xin chào, {{ Auth::user()->name }}!
                </h2>
                <div class="mt-2">
                    <p class="text-muted mb-1">
                        <i class="fas fa-graduation-cap"></i>
                        @if ($userClass)
                            <strong>{{ $userClass->class_name }}</strong>
                            @if ($userSubject)
                                - <strong>{{ $userSubject->subject_name }}</strong> ({{ $userSubject->subject_code }})
                            @endif
                        @else
                            <span class="badge bg-warning">Chưa được xếp lớp</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Thống kê - 4 cột -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <h3 class="mb-2">{{ $myGroups->count() }}</h3>
                        <p class="mb-0"><i class="fas fa-users"></i> Nhóm của tôi</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm bg-success text-white h-100">
                    <div class="card-body text-center">
                        <h3 class="mb-2">{{ $myTopics->count() }}</h3>
                        <p class="mb-0"><i class="fas fa-book"></i> Đề tài của nhóm</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm bg-warning text-white h-100">
                    <div class="card-body text-center">
                        <h3 class="mb-2">{{ $pendingInvites }}</h3>
                        <p class="mb-0"><i class="fas fa-envelope"></i> Lời mời chưa xử lý</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm bg-info text-white h-100">
                    <div class="card-body text-center">
                        <h3 class="mb-2">{{ $pendingRequests }}</h3>
                        <p class="mb-0"><i class="fas fa-hourglass-half"></i> Yêu cầu chờ duyệt</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nhóm của tôi & Lời mời gần đây -->
        <div class="row mb-4">
            <!-- Nhóm của tôi -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-users"></i> Nhóm của tôi
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($myGroups->isEmpty())
                            <p class="text-muted text-center py-4">
                                <i class="fas fa-inbox"></i> Bạn chưa có nhóm nào
                            </p>
                            <a href="{{ route('user.my_groups') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i> Tham gia hoặc tạo nhóm
                            </a>
                        @else
                            <div class="list-group">
                                @foreach ($myGroups->take(5) as $group)
                                    <a href="{{ route('user.group-detail', $group->group_id) }}" 
                                       class="list-group-item list-group-item-action border-bottom py-3 hover-shadow transition">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold">{{ $group->group_name }}</h6>
                                                <small class="text-muted d-block mb-1">
                                                    <i class="fas fa-user-circle"></i> Trưởng: {{ $group->leader->name }}
                                                </small>
                                                @if ($group->class)
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-graduation-cap"></i> {{ $group->class->class_name }}
                                                    </small>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                @if ($group->topic_id)
                                                    <span class="badge bg-success ms-2">
                                                        <i class="fas fa-check-circle"></i> Có đề tài
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary ms-2">
                                                        <i class="fas fa-times-circle"></i> Chưa có
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <small class="text-muted mt-2 d-block">
                                            {{ $group->members->count() }} thành viên
                                        </small>
                                    </a>
                                @endforeach
                            </div>
                            @if ($myGroups->count() > 5)
                                <a href="{{ route('user.my-groups') }}" class="btn btn-link mt-3 d-block text-center">
                                    Xem tất cả <i class="fas fa-arrow-right"></i>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Lời mời gần đây -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-warning text-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-envelope"></i> Lời mời gần đây
                            @if ($pendingInvites > 0)
                                <span class="badge bg-danger float-end">{{ $pendingInvites }} mới</span>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $recentInvites = Auth::user()->invites()
                                ->with('group', 'invitedBy')
                                ->latest()
                                ->limit(5)
                                ->get();
                        @endphp
                        
                        @if ($recentInvites->isEmpty())
                            <p class="text-muted text-center py-4">
                                <i class="fas fa-inbox"></i> Không có lời mời nào
                            </p>
                        @else
                            <div class="list-group">
                                @foreach ($recentInvites as $invite)
                                    <div class="list-group-item border-bottom py-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold">{{ $invite->group->group_name }}</h6>
                                                <small class="text-muted d-block mb-2">
                                                    <i class="fas fa-user"></i> Từ: {{ $invite->invitedBy->name ?? 'N/A' }}
                                                </small>
                                                @if ($invite->status === 'Pending')
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <form action="{{ route('user.invite-accept', $invite->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="fas fa-check"></i> Chấp nhận
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('user.invite-reject', $invite->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-times"></i> Từ chối
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <span class="badge bg-@if($invite->status === 'Accepted')success @else danger @endif">
                                                        @if($invite->status === 'Accepted')
                                                            <i class="fas fa-check"></i> Đã chấp nhận
                                                        @else
                                                            <i class="fas fa-times"></i> Đã từ chối
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if (Auth::user()->invites()->count() > 5)
                                <a href="{{ route('user.invites') }}" class="btn btn-link mt-3 d-block text-center">
                                    Xem tất cả <i class="fas fa-arrow-right"></i>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Đề tài gợi ý -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-book"></i> Các đề tài khác nhau
                            </h5>
                            <a href="{{ route('user.topics') }}" class="btn btn-sm btn-light">
                                Xem tất cả
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse ($suggestedTopics as $topic)
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow-sm transition hover-shadow">
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title text-primary mb-2">
                                                {{ Str::limit($topic->name, 40) }}
                                            </h6>
                                            <p class="card-text small text-muted flex-grow-1 mb-2">
                                                {{ Str::limit($topic->description, 80) }}
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-secondary">
                                                    <i class="fas fa-user-tie"></i> {{ Str::limit($topic->lecturer, 20) }}
                                                </small>
                                                @if ($topic->subject)
                                                    <small class="badge bg-light text-dark">
                                                        {{ $topic->subject->subject_code }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent border-top">
                                            <a href="{{ route('user.topic-detail', $topic->topic_id) }}" 
                                               class="btn btn-sm btn-primary w-100">
                                                <i class="fas fa-eye"></i> Xem chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <i class="fas fa-book-open text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-3">Không có đề tài nào</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Links nhanh -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('user.my_groups') }}" class="btn btn-outline-primary">
                        <i class="fas fa-users"></i> Tất cả nhóm của tôi
                    </a>
                    <a href="{{ route('user.topics') }}" class="btn btn-outline-success">
                        <i class="fas fa-book"></i> Xem tất cả đề tài
                    </a>
                    <a href="{{ route('user.classes') }}" class="btn btn-outline-info">
                        <i class="fas fa-graduation-cap"></i> Các lớp học
                    </a>
                    <a href="{{ route('user.subjects') }}" class="btn btn-outline-warning">
                        <i class="fas fa-list"></i> Các môn học
                    </a>
                </div>
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
        .list-group-item:hover {
            background-color: #f8f9fa;
        }
    </style>
@endsection
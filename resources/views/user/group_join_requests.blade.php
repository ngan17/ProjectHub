@extends('user.layouts.app')
@section('page-title', 'Yêu cầu tham gia')
@section('title', 'Quản lý yêu cầu tham gia nhóm')

@section('content')
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.my_groups') }}">Nhóm của tôi</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.group-detail', $group->group_id) }}">{{ $group->group_name }}</a></li>
                <li class="breadcrumb-item active">Yêu cầu tham gia</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-primary mb-1">
                            <i class="fas fa-user-check"></i> Yêu cầu tham gia nhóm
                        </h2>
                        <p class="text-muted mb-0">Nhóm: <strong>{{ $group->group_name }}</strong></p>
                    </div>
                    <a href="{{ route('user.group-detail', $group->group_id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($requests->isEmpty())
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
                    <h5 class="text-muted">Không có yêu cầu tham gia nào</h5>
                    <p class="text-muted mb-3">Các yêu cầu tham gia nhóm sẽ hiển thị ở đây</p>
                </div>
            </div>
        @else
            <div class="row">
                @foreach ($requests as $request)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0 request-card">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="avatar-circle mx-auto mb-2">
                                        <i class="fas fa-user fa-2x text-primary"></i>
                                    </div>
                                    <h5 class="card-title mb-1">{{ $request->member->name }}</h5>
                                    <p class="text-muted small mb-0">{{ $request->member->email }}</p>
                                </div>

                                <div class="info-section">
                                    @if ($request->member->classSection)
                                        <div class="info-item">
                                            <i class="fas fa-graduation-cap text-primary"></i>
                                            <span>{{ $request->member->classSection->class_name }}</span>
                                        </div>
                                    @endif

                                    <div class="info-item">
                                        <i class="fas fa-calendar text-success"></i>
                                        <span>{{ $request->created_at->format('d/m/Y H:i') }}</span>
                                    </div>

                                    <div class="info-item">
                                        <i class="fas fa-clock text-warning"></i>
                                        <span>{{ $request->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent border-top">
                                <div class="btn-group w-100" role="group">
                                    <form action="{{ route('user.approve-join-request', $request->request_id) }}" 
                                        method="POST" class="flex-grow-1">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100" 
                                            onclick="return confirm('Bạn có chắc muốn chấp nhận yêu cầu này?');">
                                            <i class="fas fa-check"></i> Chấp nhận
                                        </button>
                                    </form>
                                    <form action="{{ route('user.reject-join-request', $request->request_id) }}" 
                                        method="POST" class="flex-grow-1">
                                        @csrf
                                        <button type="submit" class="btn btn-danger w-100" 
                                            onclick="return confirm('Bạn có chắc muốn từ chối yêu cầu này?');">
                                            <i class="fas fa-times"></i> Từ chối
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $requests->links() }}
            </div>
        @endif

        <!-- Thông tin nhóm -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin nhóm</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-2">
                                    <strong><i class="fas fa-users text-primary"></i> Số thành viên hiện tại:</strong><br>
                                    <span class="badge bg-primary">{{ $group->members->count() + 1 }} thành viên</span>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2">
                                    <strong><i class="fas fa-crown text-warning"></i> Trưởng nhóm:</strong><br>
                                    {{ $group->leader->name }}
                                </p>
                            </div>
                            @if ($group->class)
                                <div class="col-md-4">
                                    <p class="mb-2">
                                        <strong><i class="fas fa-graduation-cap text-success"></i> Lớp học:</strong><br>
                                        {{ $group->class->class_name }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .request-card {
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .request-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .avatar-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .info-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-item i {
            width: 25px;
            margin-right: 10px;
        }

        .info-item span {
            font-size: 14px;
            color: #6c757d;
        }

        .btn-group .btn {
            border-radius: 0;
        }

        .btn-group .flex-grow-1:first-child .btn {
            border-radius: 0 0 0 12px;
        }

        .btn-group .flex-grow-1:last-child .btn {
            border-radius: 0 0 12px 0;
        }

        .card-footer {
            padding: 0;
        }

        .btn-success {
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-danger {
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
    </style>
@endsection
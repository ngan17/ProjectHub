@extends('user.layouts.app')
@section('page-title', 'Dashboard')
@section('title', 'Lời mời tham gia nhóm')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-primary">
                    <i class="fas fa-envelope"></i> Lời mời tham gia nhóm
                </h2>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($invites->isEmpty())
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-inbox fa-2x mb-3"></i>
                <p class="mt-3 mb-0">Bạn không có lời mời nào</p>
            </div>
        @else
            <div class="row">
                @foreach ($invites as $invite)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title text-primary mb-0">
                                        {{ $invite->group->group_name }}
                                    </h5>
                                    @if ($invite->status === 'Pending')
                                        <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                    @elseif ($invite->status === 'Accepted')
                                        <span class="badge bg-success">Đã chấp nhận</span>
                                    @else
                                        <span class="badge bg-danger">Đã từ chối</span>
                                    @endif
                                </div>

                                <p class="text-muted small mb-2">
                                    <i class="fas fa-crown text-warning"></i> <strong>Trưởng nhóm:</strong>
                                </p>
                                <p class="mb-3">{{ $invite->group->leader->name }}</p>

                                <p class="text-muted small mb-2">
                                    <i class="fas fa-user"></i> <strong>Lời mời từ:</strong>
                                </p>
                                <p class="mb-3">{{ $invite->invitedBy->name ?? 'N/A' }}</p>

                                <p class="text-muted small mb-2">
                                    <i class="fas fa-calendar"></i> <strong>Ngày gửi:</strong>
                                </p>
                                <p class="mb-0">{{ $invite->created_at?->format('d/m/Y H:i') }}</p>
                            </div>

                            @if ($invite->status === 'Pending')
                                <div class="card-footer bg-transparent border-top">
                                    <div class="btn-group w-100" role="group">
                                        <form action="{{ route('user.accept-invite', $invite) }}" method="POST" class="flex-grow-1">
                                            @csrf
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-check"></i> Chấp nhận
                                            </button>
                                        </form>
                                        <form action="{{ route('user.reject-invite', $invite) }}" method="POST" class="flex-grow-1">
                                            @csrf
                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="fas fa-times"></i> Từ chối
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $invites->links() }}
            </div>
        @endif
    </div>
@endsection
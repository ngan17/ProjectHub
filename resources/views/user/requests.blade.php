@extends('user.layouts.app')
@section('page-title', 'Dashboard')
@section('title', 'Yêu cầu tham gia nhóm')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-primary">
                    <i class="fas fa-hourglass-half"></i> Yêu cầu tham gia nhóm
                </h2>
                <p class="text-muted">Theo dõi các yêu cầu tham gia nhóm của bạn</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($requests->isEmpty())
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-inbox fa-2x mb-3"></i>
                <p class="mt-3 mb-0">Bạn không có yêu cầu tham gia nào</p>
            </div>
        @else
            <div class="row">
                @foreach ($requests as $request)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title text-primary mb-0">
                                        {{ $request->group->group_name }}
                                    </h5>
                                    @if ($request->status === 'Pending')
                                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                    @elseif ($request->status === 'Approved')
                                        <span class="badge bg-success">Đã chấp nhận</span>
                                    @else
                                        <span class="badge bg-danger">Đã từ chối</span>
                                    @endif
                                </div>

                                <p class="text-muted small mb-2">
                                    <i class="fas fa-crown text-warning"></i> <strong>Trưởng nhóm:</strong>
                                </p>
                                <p class="mb-3">{{ $request->group->leader->name }}</p>

                                <p class="text-muted small mb-2">
                                    <i class="fas fa-calendar"></i> <strong>Ngày gửi:</strong>
                                </p>
                                <p class="mb-0">{{ $request->created_at?->format('d/m/Y H:i') }}</p>
                            </div>

                            @if ($request->status === 'Pending')
                                <div class="card-footer bg-transparent border-top">
                                    <form action="{{ route('user.cancel-request', $request) }}" method="POST" 
                                        onsubmit="return confirm('Bạn chắc chắn muốn hủy yêu cầu này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="fas fa-times"></i> Hủy yêu cầu
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
@endsection
@extends('user.layouts.app')

@section('page-title', 'Yêu cầu tham gia nhóm')
@section('title', 'Yêu cầu tham gia nhóm')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-primary">
                    <i class="fas fa-handshake"></i> Yêu cầu tham gia nhóm của bạn
                </h2>
                <p class="text-muted">Theo dõi các yêu cầu tham gia nhóm bạn đã gửi</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($requests->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3" style="opacity: 0.5;"></i>
                <h5 class="text-muted mt-3">Bạn không có yêu cầu tham gia nào</h5>
                <p class="text-muted mb-3">Các yêu cầu tham gia nhóm của bạn sẽ hiển thị ở đây</p>
                <div class="mt-4">
                    <a href="{{ route('user.my_groups') }}" class="btn btn-primary me-2">
                        <i class="fas fa-users"></i> Xem nhóm của tôi
                    </a>
                    <a href="{{ route('user.my_groups') }}" class="btn btn-outline-primary">
                        <i class="fas fa-handshake"></i> Xin tham gia nhóm mới
                    </a>
                </div>
            </div>
        @else
            <!-- Tabs for Status Filter -->
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
                        <i class="fas fa-list-ul"></i> Tất cả
                        <span class="badge bg-primary ms-2">{{ $requests->total() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">
                        <i class="fas fa-hourglass-half"></i> Chờ duyệt
                        <span class="badge bg-warning text-dark ms-2">
                            {{ $requests->where('status', 'Pending')->count() }}
                        </span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab" aria-controls="approved" aria-selected="false">
                        <i class="fas fa-check-circle"></i> Đã chấp nhận
                        <span class="badge bg-success ms-2">
                            {{ $requests->where('status', 'Approved')->count() }}
                        </span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">
                        <i class="fas fa-times-circle"></i> Bị từ chối
                        <span class="badge bg-danger ms-2">
                            {{ $requests->where('status', 'Rejected')->count() }}
                        </span>
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- All Requests Tab -->
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    <div class="row">
                        @foreach ($requests as $request)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 shadow-sm border-0 transition-transform">
                                    <div class="card-body">
                                        <!-- Header -->
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title text-primary mb-0 fw-bold">
                                                {{ $request->group->group_name }}
                                            </h5>
                                            @php
                                                $statusConfig = [
                                                    'Pending' => ['badge' => 'warning', 'text' => 'dark', 'icon' => 'hourglass-half', 'label' => 'Chờ duyệt'],
                                                    'Approved' => ['badge' => 'success', 'text' => 'white', 'icon' => 'check', 'label' => 'Đã chấp nhận'],
                                                    'Rejected' => ['badge' => 'danger', 'text' => 'white', 'icon' => 'times', 'label' => 'Bị từ chối'],
                                                ];
                                                $config = $statusConfig[$request->status] ?? ['badge' => 'secondary', 'text' => 'white', 'label' => $request->status];
                                            @endphp
                                            <span class="badge bg-{{ $config['badge'] }} text-{{ $config['text'] }}">
                                                <i class="fas fa-{{ $config['icon'] }}"></i> {{ $config['label'] }}
                                            </span>
                                        </div>

                                        <!-- Trưởng nhóm -->
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-crown text-warning"></i> <strong>Trưởng nhóm:</strong>
                                        </p>
                                        <p class="mb-3">{{ $request->group->leader->name }}</p>

                                        <!-- Số thành viên -->
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-users"></i> <strong>Số thành viên:</strong>
                                        </p>
                                        <p class="mb-3">
                                            <span class="badge bg-info">{{ $request->group->members->count() + 1 }} thành viên</span>
                                        </p>

                                        <!-- Đề tài -->
                                        @if ($request->group->topic)
                                            <div class="mb-3 p-2 rounded bg-light border-start border-3" style="border-color: var(--success);">
                                                <small class="text-muted"><i class="fas fa-book"></i> Đề tài:</small>
                                                <p class="mb-0 fw-bold text-truncate" title="{{ $request->group->topic->name }}">
                                                    {{ $request->group->topic->name }}
                                                </p>
                                            </div>
                                        @else
                                            <div class="alert alert-sm alert-warning mb-3" style="padding: 8px 12px; font-size: 13px;">
                                                <i class="fas fa-exclamation-triangle"></i> Nhóm chưa có đề tài
                                            </div>
                                        @endif

                                        <!-- Ngày gửi -->
                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-calendar"></i> <strong>Ngày gửi:</strong><br>
                                            {{ $request->created_at?->format('d/m/Y H:i') }}
                                        </p>
                                    </div>

                                    <!-- Actions -->
                                    @if ($request->status === 'Pending')
                                        <div class="card-footer bg-transparent border-top">
                                            <form action="{{ route('user.request-cancel', $request) }}" method="POST" 
                                                onsubmit="return confirm('Bạn chắc chắn muốn hủy yêu cầu này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger w-100 btn-sm">
                                                    <i class="fas fa-times"></i> Hủy yêu cầu
                                                </button>
                                            </form>
                                        </div>
                                    @elseif ($request->status === 'Approved')
                                        <div class="card-footer bg-transparent border-top">
                                            <a href="{{ route('user.group-detail', $request->group->group_id) }}" class="btn btn-success w-100 btn-sm">
                                                <i class="fas fa-eye"></i> Xem nhóm
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pending Requests Tab -->
                <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    <div class="row">
                        @forelse ($requests->where('status', 'Pending') as $request)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 shadow-sm border-0 border-start border-4" style="border-color: var(--warning);">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title text-primary mb-0 fw-bold">{{ $request->group->group_name }}</h5>
                                            <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half"></i> Chờ duyệt</span>
                                        </div>
                                        <p class="text-muted small mb-2"><i class="fas fa-crown text-warning"></i> <strong>Trưởng nhóm:</strong></p>
                                        <p class="mb-3">{{ $request->group->leader->name }}</p>
                                        <p class="text-muted small mb-2"><i class="fas fa-users"></i> <strong>Số thành viên:</strong></p>
                                        <p class="mb-3"><span class="badge bg-info">{{ $request->group->members->count() + 1 }} thành viên</span></p>
                                        @if ($request->group->topic)
                                            <div class="alert alert-success mb-0" style="padding: 8px 12px; font-size: 13px;">
                                                <i class="fas fa-book"></i> {{ $request->group->topic->name }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer bg-transparent border-top">
                                        <form action="{{ route('user.request-cancel', $request) }}" method="POST" 
                                            onsubmit="return confirm('Bạn chắc chắn muốn hủy yêu cầu này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger w-100 btn-sm">
                                                <i class="fas fa-times"></i> Hủy yêu cầu
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12"><div class="alert alert-info text-center"><i class="fas fa-inbox"></i> Không có yêu cầu chờ duyệt</div></div>
                        @endforelse
                    </div>
                </div>

                <!-- Approved Requests Tab -->
                <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                    <div class="row">
                        @forelse ($requests->where('status', 'Approved') as $request)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 shadow-sm border-0 border-start border-4" style="border-color: var(--success);">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title text-success mb-0 fw-bold">{{ $request->group->group_name }}</h5>
                                            <span class="badge bg-success"><i class="fas fa-check-circle"></i> Đã chấp nhận</span>
                                        </div>
                                        <p class="text-muted small mb-2"><i class="fas fa-crown"></i> <strong>Trưởng nhóm:</strong></p>
                                        <p class="mb-3">{{ $request->group->leader->name }}</p>
                                        <p class="text-muted small mb-2"><i class="fas fa-users"></i> <strong>Số thành viên:</strong></p>
                                        <p class="mb-3"><span class="badge bg-info">{{ $request->group->members->count() + 1 }} thành viên</span></p>
                                        @if ($request->group->topic)
                                            <div class="alert alert-success mb-0" style="padding: 8px 12px; font-size: 13px;">
                                                <i class="fas fa-book"></i> {{ $request->group->topic->name }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer bg-transparent border-top">
                                        <a href="{{ route('user.group-detail', $request->group->group_id) }}" class="btn btn-success w-100 btn-sm">
                                            <i class="fas fa-eye"></i> Xem nhóm
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12"><div class="alert alert-info text-center"><i class="fas fa-inbox"></i> Không có yêu cầu được chấp nhận</div></div>
                        @endforelse
                    </div>
                </div>

                <!-- Rejected Requests Tab -->
                <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                    <div class="row">
                        @forelse ($requests->where('status', 'Rejected') as $request)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 shadow-sm border-0 border-start border-4" style="border-color: var(--danger);">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title text-danger mb-0 fw-bold">{{ $request->group->group_name }}</h5>
                                            <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Bị từ chối</span>
                                        </div>
                                        <p class="text-muted small mb-2"><i class="fas fa-crown"></i> <strong>Trưởng nhóm:</strong></p>
                                        <p class="mb-3">{{ $request->group->leader->name }}</p>
                                        <p class="text-muted small mb-2"><i class="fas fa-calendar"></i> <strong>Ngày gửi:</strong></p>
                                        <p class="mb-0">{{ $request->created_at?->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="card-footer bg-transparent border-top">
                                        <a href="{{ route('user.topics') }}" class="btn btn-outline-primary w-100 btn-sm">
                                            <i class="fas fa-search"></i> Tìm nhóm khác
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12"><div class="alert alert-info text-center"><i class="fas fa-inbox"></i> Không có yêu cầu bị từ chối</div></div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if ($requests->hasPages())
                <div class="d-flex justify-content-center mt-5">{{ $requests->links() }}</div>
            @endif
        @endif

        <!-- Statistics Section -->
        @if (!$requests->isEmpty())
            <div class="row mt-5 mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card text-center shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="text-primary fw-bold">{{ $requests->total() }}</h3>
                            <p class="text-muted mb-0">Tổng cộng</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="text-warning fw-bold">{{ $requests->where('status', 'Pending')->count() }}</h3>
                            <p class="text-muted mb-0">Chờ duyệt</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="text-success fw-bold">{{ $requests->where('status', 'Approved')->count() }}</h3>
                            <p class="text-muted mb-0">Đã chấp nhận</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="text-danger fw-bold">{{ $requests->where('status', 'Rejected')->count() }}</h3>
                            <p class="text-muted mb-0">Bị từ chối</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body d-flex gap-2">
                            <a href="{{ route('user.my_groups') }}" class="btn btn-primary">
                                <i class="fas fa-users"></i> Xem nhóm của tôi
                            </a>
                            <a href="{{ route('user.my_groups') }}" class="btn btn-outline-primary">
                                <i class="fas fa-handshake"></i> Xin tham gia nhóm mới
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .card {
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }
        .nav-link {
            color: #6c757d;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #0d6efd;
            border-bottom-color: #0d6efd;
        }
        .nav-link.active {
            color: #0d6efd;
            border-bottom-color: #0d6efd;
            background: transparent;
        }
        .badge {
            font-weight: 500;
            padding: 0.4em 0.7em;
        }
        .alert-sm {
            margin-bottom: 0;
        }
        .transition-transform {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .transition-transform:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
@endsection
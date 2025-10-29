@php
    $user = Auth::user();
    $isMember = $group->members()->where('group_members.user_id', $user->user_id)->exists();
    $isLeader = $group->leader_id == $user->user_id;
    $pendingRequest = \App\Models\Join_Requests::where('group_id', $group->group_id)
        ->where('member_id', $user->user_id)
        ->where('status', 'Pending')
        ->first();
    $rejectedRequest = \App\Models\Join_Requests::where('group_id', $group->group_id)
        ->where('member_id', $user->user_id)
        ->where('status', 'Rejected')
        ->first();
@endphp

<div class="row mb-4">
    <div class="col-md-8">
        <h3 class="fw-bold text-primary mb-4">
            <i class="fas fa-info-circle"></i> Thông tin nhóm
        </h3>

        <!-- Nhóm Information Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title text-primary fw-bold mb-3">{{ $group->group_name }}</h5>

                <!-- Leader Info -->
                <div class="mb-3 pb-3 border-bottom">
                    <p class="text-muted small mb-1">
                        <i class="fas fa-crown text-warning"></i> <strong>Trưởng nhóm:</strong>
                    </p>
                    <p class="mb-0 fs-6">{{ $group->leader->name }}</p>
                </div>

                <!-- Members Count -->
                <div class="mb-3 pb-3 border-bottom">
                    <p class="text-muted small mb-1">
                        <i class="fas fa-users"></i> <strong>Số thành viên:</strong>
                    </p>
                    <p class="mb-0">
                        <span class="badge bg-info fs-6">
                            {{ $group->members->count() + 1 }} thành viên
                        </span>
                    </p>
                </div>

                <!-- Topic -->
                @if ($group->topic)
                    <div class="mb-3 pb-3 border-bottom">
                        <p class="text-muted small mb-1">
                            <i class="fas fa-book"></i> <strong>Đề tài:</strong>
                        </p>
                        <p class="mb-0 fw-bold text-success">{{ $group->topic->name }}</p>
                    </div>
                @else
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle"></i> Nhóm chưa có đề tài
                    </div>
                @endif

                <!-- Class -->
                @if ($group->class)
                    <div class="mb-0">
                        <p class="text-muted small mb-1">
                            <i class="fas fa-graduation-cap"></i> <strong>Lớp học:</strong>
                        </p>
                        <p class="mb-0">{{ $group->class->name }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Action Card -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-bolt"></i> Hành động
                </h6>
            </div>
            <div class="card-body">
                @if (!$user)
                    <!-- Not logged in -->
                    <p class="text-muted mb-3">Vui lòng đăng nhập để tham gia nhóm</p>
                    <a href="{{ route('login') }}" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt"></i> Đăng nhập
                    </a>

                @elseif ($isLeader)
                    <!-- Is Leader -->
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-crown text-warning"></i>
                        <strong>Bạn là trưởng nhóm</strong>
                    </div>
                    <a href="{{ route('user.invite-member-form', $group->group_id) }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-plus-circle"></i> Mời thành viên
                    </a>
                    <a href="{{ route('user.group-join-requests', $group->group_id) }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-list"></i> Xem yêu cầu tham gia
                    </a>

                @elseif ($isMember)
                    <!-- Is Member -->
                    <div class="alert alert-success mb-3">
                        <i class="fas fa-check-circle"></i>
                        <strong>Bạn đã là thành viên</strong>
                    </div>
                    <button type="button" class="btn btn-success w-100" disabled>
                        <i class="fas fa-check"></i> Đã tham gia
                    </button>

                @elseif ($pendingRequest)
                    <!-- Pending Request -->
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-hourglass-half"></i>
                        <strong>Yêu cầu đang chờ duyệt</strong>
                    </div>
                    <p class="text-muted small mb-3">
                        Yêu cầu của bạn đã được gửi vào <strong>{{ $pendingRequest->created_at->format('d/m/Y H:i') }}</strong>
                    </p>
                    <form action="{{ route('user.request-cancel', $pendingRequest) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Bạn chắc chắn muốn hủy yêu cầu này?')">
                            <i class="fas fa-times"></i> Hủy yêu cầu
                        </button>
                    </form>

                @elseif ($rejectedRequest)
                    <!-- Rejected Request -->
                    <div class="alert alert-danger mb-3">
                        <i class="fas fa-times-circle"></i>
                        <strong>Yêu cầu bị từ chối</strong>
                    </div>
                    <p class="text-muted small mb-3">
                        Trưởng nhóm đã từ chối yêu cầu của bạn.
                    </p>
                    <button type="button" class="btn btn-danger w-100" disabled>
                        <i class="fas fa-times"></i> Yêu cầu bị từ chối
                    </button>

                @else
                    <!-- Can Request -->
                    <p class="text-muted small mb-3">
                        Nhấn nút dưới để gửi yêu cầu tham gia nhóm này
                    </p>
                    <form action="{{ route('user.send-join-request', $group->group_id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-handshake"></i> Xin tham gia nhóm
                        </button>
                    </form>
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle"></i> Trưởng nhóm sẽ phê duyệt yêu cầu của bạn
                    </small>
                @endif
            </div>
        </div>

        <!-- Request Status Timeline (Optional) -->
        @if ($pendingRequest || $rejectedRequest)
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-muted">
                        <i class="fas fa-history"></i> Lịch sử
                    </h6>
                </div>
                <div class="card-body">
                    @if ($pendingRequest)
                        <div class="d-flex mb-2">
                            <div class="me-2">
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-hourglass-half"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0 small"><strong>Chờ duyệt</strong></p>
                                <p class="mb-0 text-muted small">{{ $pendingRequest->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    @if ($rejectedRequest)
                        <div class="d-flex">
                            <div class="me-2">
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0 small"><strong>Bị từ chối</strong></p>
                                <p class="mb-0 text-muted small">{{ $rejectedRequest->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .card {
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .btn {
        border-radius: 6px;
        font-weight: 500;
    }

    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
    }
</style>



























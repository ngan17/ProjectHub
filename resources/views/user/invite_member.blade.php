@extends('user.layouts.app')

@section('page-title', 'Mời thành viên')
@section('title', 'Mời thành viên vào nhóm')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-primary">
                    <i class="fas fa-user-plus"></i> Mời thành viên vào nhóm
                </h2>
                <p class="text-muted">Nhóm: <strong>{{ $group->group_name }}</strong></p>
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

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Form Mời Thành Viên -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-envelope"></i> Mời thành viên
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($availableUsers->isEmpty())
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Không có sinh viên khác trong lớp để mời
                            </div>
                        @else
                            <form action="{{ route('user.send-invite') }}" method="POST">
                                @csrf

                                <!-- Hidden group_id -->
                                <input type="hidden" name="group_id" value="{{ $group->group_id }}">

                                <!-- Select Member -->
                                <div class="mb-3">
                                    <label for="member_id" class="form-label">
                                        <i class="fas fa-user"></i> Chọn thành viên
                                    </label>
                                    <select id="member_id" name="member_id" class="form-select" required>
                                        <option value="">-- Chọn sinh viên --</option>
                                        @foreach ($availableUsers as $user)
                                            <option value="{{ $user->user_id }}">
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('member_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-check"></i> Gửi lời mời
                                </button>
                            </form>

                            <!-- Info -->
                            <hr>
                            <p class="text-muted small mb-0">
                                <i class="fas fa-lightbulb"></i>
                                Người dùng được liệt kê là sinh viên cùng lớp nhưng chưa là thành viên nhóm này
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Danh sách lời mời chờ xử lý -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-hourglass-half"></i> Lời mời chờ xử lý
                            <span class="badge bg-light text-dark float-end">{{ $pendingInvites->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        @if ($pendingInvites->isEmpty())
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-inbox"></i> Không có lời mời chờ xử lý
                            </div>
                        @else
                            <div class="list-group">
                                @foreach ($pendingInvites as $invite)
                                    <div class="list-group-item border-start border-4" style="border-color: var(--warning);">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <i class="fas fa-user-circle"></i> {{ $invite->member->name }}
                                                </h6>
                                                <small class="text-muted">{{ $invite->member->email }}</small>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar"></i> 
                                                    {{ $invite->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-hourglass-half"></i> Chờ
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin nhóm -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle"></i> Thông tin nhóm
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-bookmark"></i> <strong>Tên nhóm:</strong>
                                </p>
                                <p class="mb-0">{{ $group->group_name }}</p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-users"></i> <strong>Số thành viên:</strong>
                                </p>
                                <p class="mb-0">
                                    <span class="badge bg-info">{{ $group->members->count() + 1 }}</span>
                                </p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-book"></i> <strong>Đề tài:</strong>
                                </p>
                                <p class="mb-0">
                                    @if ($group->topic)
                                        {{ $group->topic->name }}
                                    @else
                                        <span class="text-muted">Chưa có</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-graduation-cap"></i> <strong>Lớp:</strong>
                                </p>
                                <p class="mb-0">
                                    @if ($group->class)
                                        {{ $group->class->name }}
                                    @else
                                        <span class="text-muted">Chưa có</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('user.my_groups') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
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

        .list-group-item {
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
        }

        .form-select, .form-control {
            border-radius: 6px;
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
        }
    </style>

    <script>
        // Auto-hide alerts after 5 seconds
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
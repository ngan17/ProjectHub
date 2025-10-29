@extends('user.layouts.app')
@section('page-title', 'Dashboard')
@section('title', 'Nhóm của tôi')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-primary">
                    <i class="fas fa-users"></i> Nhóm của tôi
                </h2>
            </div>
        </div>

        @if ($groups->isEmpty())
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-inbox fa-2x mb-3"></i>
                <p class="mt-3 mb-3">Bạn chưa tham gia nhóm nào</p>
                <a href="{{ route('user.topics') }}" class="btn btn-primary">
                    Khám phá đề tài & Tạo nhóm
                </a>
            </div>
        @else
            <div class="row">
                @foreach ($groups as $group)
                    @php
                        $memberCount = DB::table('group_members')->where('group_id', $group->group_id)->count();
                    @endphp
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0 transition-transform">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title text-primary fw-bold mb-0">
                                        {{ $group->group_name }}
                                    </h5>
                                    @if ($group->topic_id)
                                        <span class="badge bg-success">Có đề tài</span>
                                    @else
                                        <span class="badge bg-secondary">Chưa có</span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-crown text-warning"></i> <strong>Trưởng nhóm:</strong>
                                    </p>
                                    <p class="mb-0">{{ $group->leader->name }}</p>
                                </div>

                                <div class="mb-3 pb-3 border-bottom">
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-users"></i> <strong>Số thành viên:</strong>
                                    </p>
                                    <p class="mb-0">
                                        <span class="badge bg-info">{{ $memberCount }} thành viên</span>
                                    </p>
                                </div>

                                @if ($group->topic_id)
                                    <div class="mb-3">
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-book"></i> <strong>Đề tài:</strong>
                                        </p>
                                        <p class="mb-0 text-truncate" title="{{ $group->topic->name ?? '—' }}">
                                            {{ $group->topic->name ?? '—' }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <div class="card-footer bg-transparent border-top">
                                <a href="{{ route('user.group-detail', $group) }}" class="btn btn-primary w-100">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $groups->links() }}
            </div>
        @endif
    </div>

    <style>
        .transition-transform {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .transition-transform:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
@endsection
@extends('user.layouts.app')
@section('title', 'Đề tài của tôi')
@section('page-title', 'Đề tài của tôi')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-primary">
                <i class="fas fa-bookmark"></i> Đề tài của tôi
            </h2>
            <p class="text-muted">Các đề tài mà nhóm của bạn đang thực hiện</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Topics Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($topics->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th style="width: 25%;">Tên đề tài</th>
                                <th style="width: 20%;">Nhóm</th>
                                <th style="width: 15%;">Môn học</th>
                                <th style="width: 12%;">Giảng viên</th>
                                <th style="width: 10%;">Ngày giao</th>
                                <th style="width: 8%;" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topics as $index => $topic)
                                <tr class="topic-row">
                                    <td class="text-center text-muted fw-semibold">
                                        {{ $index + 1 }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-primary">
                                            {{ Str::limit($topic->name, 60) }}
                                        </div>
                                        @if($topic->goal)
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-bullseye"></i> {{ Str::limit($topic->goal, 40) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($topic->group))
                                            <a href="{{ route('user.group-detail', $topic->group->group_id) }}" 
                                               class="text-decoration-none">
                                                <i class="fas fa-users text-primary"></i>
                                                {{ Str::limit($topic->group->group_name, 25) }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($topic->subject)
                                            <span class="badge bg-light text-dark border">
                                                {{ $topic->subject->subject_code }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($topic->lecturer)
                                            <small>
                                                <i class="fas fa-user-tie text-primary"></i>
                                                {{ Str::limit($topic->lecturer, 20) }}
                                            </small>
                                        @else
                                            <span class="text-muted">Chưa phân công</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $topic->created_at ? $topic->created_at->format('d/m/Y') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('user.topic-detail', $topic->topic_id) }}" 
                                           class="btn btn-outline-info btn-sm"
                                           data-bs-toggle="tooltip" 
                                           title="Xem chi tiết đề tài">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Bạn chưa tham gia nhóm nào có đề tài</h5>
                    <p class="text-muted">Hãy tham gia nhóm hoặc chờ duyệt đề tài.</p>
                    <div class="mt-4">
                        <a href="{{ route('user.topics') }}" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Tìm đề tài
                        </a>
                        <a href="{{ route('user.my_groups') }}" class="btn btn-outline-primary">
                            <i class="fas fa-users"></i> Nhóm của tôi
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .topic-row {
        transition: background-color 0.2s ease;
    }
    .topic-row:hover {
        background-color: #f8f9fa;
    }
    .card {
        border-radius: 10px;
    }
    .table th {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge {
        font-weight: 500;
        padding: 0.4em 0.7em;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection

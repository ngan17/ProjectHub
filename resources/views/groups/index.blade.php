@extends('layouts.app')

@section('title', 'Quản lý nhóm')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="fw-bold text-primary">
                    <i class="fas fa-users"></i> Danh sách nhóm
                </h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('groups.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus"></i> Tạo nhóm mới
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($groups->isEmpty())
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-inbox"></i>
                <p class="mt-3 mb-0">Không có nhóm nào. <a href="{{ route('groups.create') }}">Tạo nhóm mới ngay</a></p>
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
                                        <span class="badge bg-secondary">Chưa có đề tài</span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-crown text-warning"></i> <strong>Trưởng nhóm:</strong>
                                    </p>
                                    <p class="mb-0">{{ $group->leader->name ?? '—' }}</p>
                                </div>

                                <div class="mb-3 pb-3 border-bottom">
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-users"></i> <strong>Số thành viên:</strong>
                                    </p>
                                    <p class="mb-0 fs-5">
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
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('groups.show', $group) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                    <a href="{{ route('groups.edit', $group) }}" class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="{{ route('groups.destroy', $group) }}" method="POST" class="d-inline" 
                                        onsubmit="return confirm('Bạn chắc chắn muốn xóa nhóm này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                </div>
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
@extends('layouts.app')

@section('title', 'Quản lý đề tài')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="fw-bold text-primary">
                    <i class="fas fa-book"></i> Danh sách đề tài
                </h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('topics.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus"></i> Thêm đề tài mới
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($topics->isEmpty())
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-inbox"></i>
                <p class="mt-3 mb-0">Không có đề tài nào. <a href="{{ route('topics.create') }}">Tạo mới ngay</a></p>
            </div>
        @else
            <div class="row">
                @foreach ($topics as $topic)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0 transition-transform" style="transition: transform 0.3s;">
                            <div class="card-body">
                                <h5 class="card-title text-primary fw-bold">
                                    {{ Str::limit($topic->name, 50) }}
                                </h5>
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-user-tie"></i> <strong>Giảng viên:</strong> {{ $topic->lecturer }}
                                </p>
                                <p class="card-text text-muted">
                                    {{ Str::limit($topic->description, 80) }}
                                </p>
                                @if ($topic->goal)
                                    <p class="small text-secondary">
                                        <strong>Mục tiêu:</strong> {{ Str::limit($topic->goal, 60) }}
                                    </p>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent border-top">
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('topics.show', $topic) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('topics.edit', $topic) }}" class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('topics.destroy', $topic) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $topics->links() }}
            </div>
        @endif
    </div>

    <style>
        .transition-transform:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
@endsection
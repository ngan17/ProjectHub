@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-user-graduate"></i> Quản lý Sinh viên</h4>
            <div>
                <a href="{{ route('students.import.form') }}" class="btn btn-light btn-sm me-2">
                    <i class="fas fa-file-import"></i> Import Excel
                </a>
                <a href="{{ route('students.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus"></i> Thêm sinh viên
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filter & Export Section -->
            <div class="card mb-4 border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body">
                    <form method="GET" action="{{ route('students.index') }}">
                        <div class="row g-3">
                            <!-- Search -->
                            <div class="col-md-4">
                                <label class="form-label text-white"><i class="fas fa-search"></i> Tìm kiếm</label>
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       class="form-control" 
                                       placeholder="Tên hoặc email...">
                            </div>

                            <!-- Filter by Class -->
                            <div class="col-md-4">
                                <label class="form-label text-white"><i class="fas fa-chalkboard"></i> Lọc theo lớp</label>
                                <select name="class_id" class="form-select">
                                    <option value="">Tất cả lớp</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->class_id }}" 
                                                {{ request('class_id') == $class->class_id ? 'selected' : '' }}>
                                            {{ $class->class_name }} - {{ $class->subject->subject_name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-light me-2">
                                    <i class="fas fa-filter"></i> Lọc
                                </button>
                                <a href="{{ route('students.index') }}" class="btn btn-outline-light me-2">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                                <a href="{{ route('students.export', ['class_id' => request('class_id')]) }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Export
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Students Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Họ và tên</th>
                            <th>Email</th>
                            <th>Lớp học phần</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                            <tr>
                                <td>{{ $students->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-bold">{{ $student->name }}</div>
                                </td>
                                <td>{{ $student->email }}</td>
                                <td>
                                    @foreach($student->classes as $class)
                                        <span class="badge bg-info text-dark me-1">
                                            {{ $class->class_name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @if($student->isHaveGroup)
                                        <span class="badge bg-success">Đã có nhóm</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Chưa có nhóm</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('students.show', $student->user_id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('students.edit', $student->user_id) }}" 
                                           class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('students.destroy', $student->user_id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Bạn chắc chắn muốn xóa sinh viên này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Chưa có sinh viên nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
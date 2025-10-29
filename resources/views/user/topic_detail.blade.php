@extends('user.layouts.app')

@section('page-title', 'Chi tiết đề tài')
@section('title', 'Chi tiết đề tài')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('user.topics') }}" class="btn btn-secondary mb-3">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Thông tin đề tài -->
            <div class="col-lg-8 mb-4">
                <div class="card-custom mb-4">
                    <div class="card-header-custom">
                        <h4 class="mb-0">{{ $topic->name }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted small fw-bold">GIẢNG VIÊN HƯỚNG DẪN</h6>
                                <p class="fs-5 fw-bold">{{ $topic->lecturer }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted small fw-bold">NGÀY TẠO</h6>
                                <p class="fs-5">{{ $topic->created_at?->format('d/m/Y') ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-muted small fw-bold">MÔ TẢ CHI TIẾT</h6>
                            <div class="bg-light p-4 rounded-3 border-start border-4" style="border-color: var(--primary);">
                                {!! nl2br($topic->description) !!}
                            </div>
                        </div>

                        @if ($topic->goal)
                            <div class="mb-4">
                                <h6 class="text-muted small fw-bold">MỤC TIÊU</h6>
                                <div class="bg-light p-4 rounded-3 border-start border-4" style="border-color: var(--success);">
                                    {!! nl2br($topic->goal) !!}
                                </div>
                            </div>
                        @endif

                        @if ($topic->requirements)
                            <div class="mb-0">
                                <h6 class="text-muted small fw-bold">YÊU CẦU</h6>
                                <div class="bg-light p-4 rounded-3 border-start border-4" style="border-color: var(--warning);">
                                    {!! nl2br($topic->requirements) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Đăng ký đề tài -->
            <div class="col-lg-4">
                <div class="card-custom">
                    <div class="card-header-custom">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check"></i> Đăng ký đề tài
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($myGroups->isEmpty())
                            <div class="alert alert-info alert-custom">
                                <i class="fas fa-info-circle"></i>
                                <p class="mb-0 mt-2">Bạn chưa có nhóm nào. Vui lòng tạo hoặc tham gia nhóm trước.</p>
                            </div>
                            <a href="{{ route('user.my-groups') }}" class="btn btn-primary-custom w-100">
                                <i class="fas fa-users"></i> Tới nhóm của tôi
                            </a>
                        @else
                            <form action="{{ route('user.topic-register') }}" method="POST">
                                @csrf
                                <input type="hidden" name="topic_id" value="{{ $topic->topic_id }}">

                                <div class="mb-3">
                                    <label for="group_id" class="form-label fw-bold">
                                        <i class="fas fa-users"></i> Chọn nhóm
                                    </label>
                                    <select class="form-select form-select-lg @error('group_id') is-invalid @enderror" 
                                        id="group_id" name="group_id" required>
                                        <option value="">-- Chọn nhóm --</option>
                                        @foreach ($myGroups as $group)
                                            <option value="{{ $group->group_id }}" 
                                                @if ($group->topic_id) disabled @endif>
                                                {{ $group->group_name }}
                                                @if ($group->topic_id)
                                                    (Đã có đề tài)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('group_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary-custom w-100 btn-lg">
                                    <i class="fas fa-check"></i> Gửi yêu cầu đăng ký
                                </button>
                            </form>

                            <hr class="my-3">

                            <h6 class="text-muted small fw-bold mb-3">NHÓM CỦA BẠN:</h6>
                            <div class="list-group">
                                @foreach ($myGroups as $group)
                                    <div class="list-group-item py-2 px-0 border-0 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="fw-bold">{{ $group->group_name }}</small>
                                            @if ($group->topic_id)
                                                <span class="badge bg-success">Có đề tài</span>
                                            @else
                                                <span class="badge bg-secondary">Chưa có</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card-custom {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .card-header-custom {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            padding: 20px;
            border: none;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            background: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
            color: white;
        }
    </style>
@endsection
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-primary">Bảng điều khiển Quản trị viên</h2>

    <div class="row text-center">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <h5>Người dùng</h5>
                <h3>{{ $stats['total_users'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <h5>Đề tài</h5>
                <h3>{{ $stats['total_topics'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <h5> Nhóm</h5>
                <h3>{{ $stats['total_groups'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <h5>Yêu cầu chờ duyệt</h5>
                <h3>{{ $stats['pending_requests'] }}</h3>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="row">
        <div class="col-md-6">
            <h5> Người dùng mới nhất</h5>
            <ul class="list-group">
                @foreach($recentUsers as $user)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $user->name }}</span>
                        <small class="text-muted">{{ $user->role }}</small>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="col-md-6">
            <h5> Đề tài mới</h5>
            <ul class="list-group">
                @foreach($recentTopics as $topic)
                    <li class="list-group-item">
                        {{ $topic->title ?? 'Đề tài không tên' }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <hr class="my-4">
    <h5>Yêu cầu đề tài đang chờ duyệt</h5>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Đề tài</th>
                <th>Nhóm</th>
                <th>Trưởng nhóm</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendingTopicRequests as $req)
                <tr>
                    <td>{{ $req->topic->title ?? '—' }}</td>
                    <td>{{ $req->group->group_name ?? '—' }}</td>
                    <td>{{ $req->group->leader->name ?? '—' }}</td>
                    <td><span class="badge bg-warning text-dark">{{ $req->status }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

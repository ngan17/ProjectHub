@extends('layouts.app')

@section('title', 'Lecturer Dashboard')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-success">🎓 Bảng điều khiển Giảng viên</h2>

    <div class="row text-center">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3">
                <h5>📘 Đề tài được giao</h5>
                <h3>{{ $stats['assigned_topics'] }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3">
                <h5>🕒 Đang chờ duyệt</h5>
                <h3>{{ $stats['pending_requests'] }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3">
                <h5>Đã duyệt</h5>
                <h3>{{ $stats['approved_topics'] }}</h3>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <h5>📋 Danh sách yêu cầu của sinh viên</h5>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Đề tài</th>
                <th>Nhóm</th>
                <th>Trưởng nhóm</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topicRequests as $req)
                <tr>
                    <td>{{ $req->topic->title ?? '—' }}</td>
                    <td>{{ $req->group->group_name ?? '—' }}</td>
                    <td>{{ $req->group->leader->name ?? '—' }}</td>
                    <td>
                        <span class="badge 
                            @if($req->status == 'Approved') bg-success 
                            @elseif($req->status == 'Rejected') bg-danger 
                            @else bg-warning text-dark @endif">
                            {{ $req->status }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

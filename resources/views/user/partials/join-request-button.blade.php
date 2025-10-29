@auth
    @php
        $user = Auth::user();
        $isMember = $group->members()->where('group_members.user_id', $user->user_id)->exists();
        $isLeader = $group->leader_id == $user->user_id;
        $pendingRequest = \App\Models\Join_Requests::where('group_id', $group->group_id)
            ->where('member_id', $user->user_id)
            ->where('status', 'Pending')
            ->exists();
    @endphp

    @if (!$isMember && !$isLeader)
        @if ($pendingRequest)
            <!-- Already sent request -->
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-hourglass-half"></i> Bạn đã gửi yêu cầu tham gia nhóm này rồi (Chờ phê duyệt)
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @else
            <!-- Send request button -->
            <form action="{{ route('user.send-join-request', ['group' => $group->group_id]) }}" 
                  method="POST" 
                  style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                    <i class="fas fa-handshake"></i> Xin tham gia nhóm
                </button>
            </form>
        @endif
    @elseif ($isLeader)
        <div class="alert alert-info">
            <i class="fas fa-crown"></i> Bạn là trưởng nhóm
        </div>
    @else
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Bạn đã là thành viên của nhóm này
        </div>
    @endif
@endauth
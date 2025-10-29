<?php

namespace App\Http\Controllers;

use App\Models\Groups;
use App\Models\Topics;
use App\Models\Invites;
use App\Models\Join_Requests;
use App\Models\Topic_requests;
use App\Models\ClassSection;
use App\Models\Group_Members;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    /**
     * User Dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Nhóm của user
        $myGroups = Groups::where('leader_id', $user->user_id)
            ->orWhereHas('members', function ($query) use ($user) {
                $query->where('group_members.user_id', $user->user_id);
            })
            ->with(['leader', 'topic', 'class', 'class.subject', 'members'])
            ->get();

        // Lời mời chưa xử lý
        $pendingInvites = Invites::where('member_id', $user->user_id)
            ->where('status', 'Pending')
            ->count();

        // Yêu cầu tham gia chưa xử lý
        $pendingRequests = Join_Requests::where('member_id', $user->user_id)
            ->where('status', 'Pending')
            ->count();

        // Đề tài mà nhóm của user đang có
        $myTopics = Topics::whereIn('topic_id', $myGroups->pluck('topic_id'))
            ->with('subject')
            ->get();

        // Lớp học của user
        $userClass = $user->classSection ?? null;

        // Môn học của user (nếu có lớp)
        $userSubject = $userClass?->subject ?? null;

        // Đề tài gợi ý (random 6 đề tài)
        $suggestedTopics = Topics::inRandomOrder()->limit(6)->get();

        return view('user.dashboard', compact(
            'myGroups',
            'pendingInvites',
            'pendingRequests',
            'myTopics',
            'userClass',
            'userSubject',
            'suggestedTopics'
        ));
    }

    /**
     * Danh sách đề tài (có lọc theo lớp/môn học)
     */
    public function topics(Request $request)
    {
        $user = Auth::user();

        // Lấy tất cả các lớp mà user đang học
        $userClasses = $user->classes;

        // Nếu user không có lớp nào
        if ($userClasses->isEmpty()) {
            return view('user.topics', [
                'topics' => collect(value: []),
                'userClasses' => $userClasses
            ]);
        }

        // Lấy tất cả subject_id từ các lớp của user
        $subjectIds = $userClasses->pluck('subject_id')->unique()->filter();

        // Query builder
        $query = Topics::with(['subject', 'assignedGroup'])
            ->whereIn('subject_id', $subjectIds);

        // Filter theo search (tên đề tài hoặc giảng viên)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('lecturer', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter theo môn học
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter theo trạng thái
        if ($request->filled('status')) {
            if ($request->status === 'available') {
                // Đề tài còn trống (chưa có nhóm)
                $query->whereDoesntHave('assignedGroup');
            } elseif ($request->status === 'assigned') {
                // Đề tài đã có nhóm
                $query->whereHas('assignedGroup');
            }
        }

        // Sắp xếp và phân trang
        $topics = $query->orderBy('created_at', 'desc')->paginate(15);

        // Giữ lại các tham số filter khi phân trang
        $topics->appends($request->query());

        return view('user.topics', compact('topics', 'userClasses'));
    }

    public function cancelTopicRequest($requestId)
    {
        $topicRequest = Topic_requests::find($requestId);

        // Kiểm tra quyền + chỉ hủy được khi Pending
        if ($topicRequest->status !== 'Pending') {
            return redirect()->back()->with('error', 'Chỉ có thể hủy đăng ký khi chưa được duyệt');
        }

        $topicRequest->delete();

        return redirect()->back()->with('success', 'Đã hủy đăng ký đề tài');
    }

    /**
     * Đề tài của tôi (các đề tài đã đăng ký)
     */
    public function myTopics()
    {
        $user = Auth::user();

        $groups = Groups::where('leader_id', $user->user_id)
            ->orWhereIn('group_id', function ($query) use ($user) {
                $query->select('group_id')
                    ->from('group_members')
                    ->where('user_id', $user->user_id);
            })
            ->with(['topic.subject'])
            ->get();

        // Lấy danh sách topic có cả group (để biết topic thuộc group nào)
        $topics = $groups->map(function ($group) {
            if ($group->topic) {
                $group->topic->group = $group;
                return $group->topic;
            }
            return null;
        })->filter();

        return view('user.my_topics', compact('topics'));
    }

    /**
     * Chi tiết đề tài - đăng ký
     */
    public function topicDetail($id)
    {
        $topic = Topics::with(['subject', 'subject.classes', 'assignedGroup', 'topic_requests'])->findOrFail($id);
        $user = Auth::user();

        // Kiểm tra user có group không
        $myGroups = Groups::where('leader_id', $user->user_id)
            ->orWhereHas('members', function ($query) use ($user) {
                $query->where('group_members.user_id', $user->user_id);
            })
            ->with('members')
            ->get();

        // Lấy thông tin lớp học
        $userClass = $user->classSection ?? null;

        // Kiểm tra group nào đã đăng ký đề tài này
        $groupsRegistered = $topic->topic_requests()
            ->where('status', 'Accepted')
            ->pluck('group_id')
            ->toArray();

        return view('user.topic_detail', compact('topic', 'myGroups', 'userClass', 'groupsRegistered'));
    }

    /**
     * Nhóm của tôi
     */
    public function myGroups()
    {
        $user = Auth::user();

        $groups = Groups::where('leader_id', $user->user_id)
            ->orWhereHas('members', function ($query) use ($user) {
                $query->where('group_members.user_id', $user->user_id);
            })
            ->with(['leader', 'topic', 'members', 'class', 'class.subject'])
            ->paginate(9);

        $userClass = $user->classSection ?? null;

        return view('user.my_groups', compact('groups', 'userClass'));
    }

    /**
     * Hiển thị form mời thành viên vào nhóm
     */
    public function inviteMemberForm($groupId)
    {
        $group = Groups::with(['members', 'leader', 'class'])->findOrFail($groupId);

        // Kiểm tra quyền (chỉ leader mới được mời)
        if ($group->leader_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Chỉ trưởng nhóm mới có thể mời thành viên!');
        }

        // Lấy danh sách sinh viên cùng lớp nhưng chưa có trong nhóm
        $availableUsers = [];
        if ($group->class_id) {
            $currentMemberIds = $group->members->pluck('user_id')->push($group->leader_id);

            $availableUsers = \App\Models\User::whereHas('classes', function ($query) use ($group) {
                $query->where('class_sections.class_id', $group->class_id);
            })
                ->whereNotIn('user_id', $currentMemberIds)
                ->get();
        }

        // Lấy danh sách lời mời đang pending
        $pendingInvites = Invites::where('group_id', $groupId)
            ->where('status', 'Pending')
            ->with('member')
            ->get();

        return view('user.invite_member', compact('group', 'availableUsers', 'pendingInvites'));
    }

    /**
     * Gửi lời mời thành viên
     */
    public function sendInvite(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,group_id',
            'member_id' => 'required|exists:users,user_id',
        ]);

        $group = Groups::findOrFail($validated['group_id']);

        // Kiểm tra quyền
        if ($group->leader_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Chỉ trưởng nhóm mới có thể mời thành viên!');
        }

        // Kiểm tra user đã là thành viên chưa
        $isMember = $group->members()->where('group_members.user_id', $validated['member_id'])->exists();
        if ($isMember || $group->leader_id == $validated['member_id']) {
            return redirect()->back()->with('error', 'Người này đã là thành viên của nhóm!');
        }

        // Kiểm tra đã gửi lời mời chưa
        $existingInvite = Invites::where('group_id', $validated['group_id'])
            ->where('member_id', $validated['member_id'])
            ->where('status', 'Pending')
            ->first();

        if ($existingInvite) {
            return redirect()->back()->with('warning', 'Đã gửi lời mời cho người này rồi!');
        }

        // Tạo lời mời
        Invites::create([
            'group_id' => $validated['group_id'],
            'member_id' => $validated['member_id'],
            'invited_by' => Auth::id(),
            'status' => 'Pending',
        ]);

        return redirect()->back()->with('success', 'Đã gửi lời mời!');
    }

    /**
     * Hủy lời mời (dành cho leader)
     */
    public function cancelInvite($inviteId)
    {
        $invite = Invites::findOrFail($inviteId);
        $group = $invite->group;

        // Kiểm tra quyền
        if ($group->leader_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Chỉ trưởng nhóm mới có thể hủy lời mời!');
        }

        // Chỉ hủy được khi Pending
        if ($invite->status !== 'Pending') {
            return redirect()->back()->with('error', 'Chỉ có thể hủy lời mời chưa được xử lý!');
        }

        $invite->delete();

        return redirect()->back()->with('success', 'Đã hủy lời mời!');
    }

    /**
     * Gửi yêu cầu tham gia nhóm
     */
    public function sendJoinRequest(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,group_id',
        ]);

        $user = Auth::user();
        $group = Groups::findOrFail($validated['group_id']);

        // Kiểm tra user đã là thành viên chưa
        $isMember = $group->members()->where('group_members.user_id', $user->user_id)->exists();
        if ($isMember || $group->leader_id == $user->user_id) {
            return redirect()->back()->with('error', 'Bạn đã là thành viên của nhóm này!');
        }

        // Kiểm tra đã gửi yêu cầu chưa
        $existingRequest = Join_Requests::where('group_id', $validated['group_id'])
            ->where('member_id', $user->user_id)
            ->where('status', 'Pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('warning', 'Bạn đã gửi yêu cầu tham gia nhóm này rồi!');
        }

        // Tạo yêu cầu
        Join_Requests::create([
            'group_id' => $validated['group_id'],
            'member_id' => $user->user_id,
            'status' => 'Pending',
        ]);

        return redirect()->back()->with('success', 'Đã gửi yêu cầu tham gia nhóm!');
    }

    /**
     * Xem danh sách yêu cầu tham gia nhóm (dành cho leader)
     */
    public function groupJoinRequests($groupId)
    {
        $group = Groups::with(['members', 'leader'])->findOrFail($groupId);

        // Kiểm tra quyền
        if ($group->leader_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Chỉ trưởng nhóm mới có thể xem yêu cầu tham gia!');
        }

        $requests = Join_Requests::where('group_id', $groupId)
            ->where('status', 'Pending')
            ->with('member')
            ->latest()
            ->paginate(10);

        return view('user.group_join_requests', compact('group', 'requests'));
    }

    /**
     * Chấp nhận yêu cầu tham gia (dành cho leader)
     */
    public function approveJoinRequest($requestId)
    {
        $joinRequest = Join_Requests::findOrFail($requestId);
        $group = $joinRequest->group;

        // Kiểm tra quyền
        if ($group->leader_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Chỉ trưởng nhóm mới có thể chấp nhận yêu cầu!');
        }

        // Kiểm tra user đã là thành viên chưa
        $isMember = $group->members()->where('group_members.user_id', $joinRequest->member_id)->exists();

        if (!$isMember) {
            $group->members()->attach($joinRequest->member_id);
        }

        $joinRequest->update(['status' => 'Approved']);

        return redirect()->back()->with('success', 'Đã chấp nhận yêu cầu tham gia!');
    }

    /**
     * Từ chối yêu cầu tham gia (dành cho leader)
     */
    public function rejectJoinRequest($requestId)
    {
        $joinRequest = Join_Requests::findOrFail($requestId);
        $group = $joinRequest->group;

        // Kiểm tra quyền
        if ($group->leader_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Chỉ trưởng nhóm mới có thể từ chối yêu cầu!');
        }

        $joinRequest->update(['status' => 'Rejected']);

        return redirect()->back()->with('success', 'Đã từ chối yêu cầu tham gia!');
    }

    /**
     * Chi tiết nhóm
     */
    public function groupDetail($id)
    {
        $group = Groups::with([
            'class',
            'class.subject',
            'topic',
            'leader',
            'members',
            'topicRequests'
        ])->findOrFail($id);

        $members = $group->members()->get();
        $memberCount = $members->count();

        return view('user.group_detail', compact('group', 'members', 'memberCount'));
    }

    /**
     * Lời mời - V2 với tabs lọc (Mời từ trưởng nhóm)
     */
    public function invites(Request $request)
    {
        $user = Auth::user();

        $query = Invites::where('member_id', $user->user_id)
            ->with([
                'group',
                'group.class',
                'group.topic',
                'group.leader',
                'group.members',
                'invitedBy'
            ]);

        // Sắp xếp theo ngày mới nhất
        $invites = $query->latest()->paginate(10);

        return view('user.invites', compact('invites'));
    }

    /**
     * Chấp nhận lời mời
     */
    public function acceptInvite($id)
    {
        $invite = Invites::findOrFail($id);

        if ($invite->member_id !== Auth::id()) {
            abort(403);
        }

        // Kiểm tra user đã ở trong nhóm chưa
        $alreadyMember = $invite->group->members()->where('group_members.user_id', Auth::id())->exists();

        if (!$alreadyMember) {
            $invite->group->members()->attach(Auth::id());
        }

        $invite->update(['status' => 'Accepted']);

        return back()->with('success', 'Đã chấp nhận lời mời tham gia nhóm!');
    }

    /**
     * Từ chối lời mời
     */
    public function rejectInvite($id)
    {
        $invite = Invites::findOrFail($id);

        if ($invite->member_id !== Auth::id()) {
            abort(403);
        }

        $invite->update(['status' => 'Rejected']);

        return back()->with('success', 'Đã từ chối lời mời!');
    }

    /**
     * Yêu cầu tham gia - V2 với tabs lọc (Xin vào nhóm)
     */
    public function joinRequests(Request $request)
    {
        $user = Auth::user();

        $query = Join_Requests::where('member_id', $user->user_id)
            ->with([
                'group',
                'group.class',
                'group.topic',
                'group.leader',
                'group.members'
            ]);

        // Sắp xếp theo ngày mới nhất
        $requests = $query->latest()->paginate(10);

        return view('user.join_requests', compact('requests'));
    }

    /**
     * Hủy yêu cầu tham gia
     */
    public function cancelRequest($id)
    {
        $request = Join_Requests::findOrFail($id);

        if ($request->member_id !== Auth::id()) {
            abort(403);
        }

        // Chỉ hủy được khi Pending
        if ($request->status !== 'Pending') {
            return back()->with('error', 'Chỉ có thể hủy yêu cầu chưa được xử lý!');
        }

        $request->delete();

        return back()->with('success', 'Đã hủy yêu cầu!');
    }

    /**
     * Đăng ký đề tài cho nhóm
     */
    public function registerTopic(Topic_requests $topicRequestModel)
    {
        $validated = request()->validate([
            'topic_id' => 'required|exists:topics,topic_id',
            'group_id' => 'required|exists:groups,group_id',
        ]);

        // Kiểm tra group có tồn tại và user là leader
        $group = Groups::findOrFail($validated['group_id']);

        if ($group->leader_id !== Auth::id()) {
            return back()->with('error', 'Chỉ trưởng nhóm mới có thể đăng ký đề tài!');
        }

        // Kiểm tra đề tài đã được đăng ký chưa
        $existingRequest = Topic_requests::where('topic_id', $validated['topic_id'])
            ->where('group_id', $validated['group_id'])
            ->first();

        if ($existingRequest) {
            return back()->with('warning', 'Nhóm đã gửi yêu cầu đề tài này rồi!');
        }

        Topic_requests::create([
            'topic_id' => $validated['topic_id'],
            'group_id' => $validated['group_id'],
            'created_by' => Auth::id(),
            'status' => 'Pending',
        ]);

        return back()->with('success', 'Đã gửi yêu cầu đăng ký đề tài!');
    }

    /**
     * Xem tất cả lớp học
     */
    public function classes()
    {
        $classes = ClassSection::with(['subject', 'subject.lecturer', 'groups'])
            ->paginate(12);

        $userClass = Auth::user()->classSection ?? null;

        return view('user.classes', compact('classes', 'userClass'));
    }

    /**
     * Chi tiết lớp học
     */
    public function classDetail($id)
    {
        $class = ClassSection::with([
            'subject',
            'subject.lecturer',
            'subject.topics',
            'groups',
            'groups.leader',
            'groups.topic',
            'groups.members'
        ])->findOrFail($id);

        return view('user.class_detail', compact('class'));
    }

    /**
     * Xem tất cả môn học
     */
    public function subjects()
    {
        $subjects = Subject::with(['lecturer', 'classes', 'topics'])
            ->paginate(12);

        return view('user.subjects', compact('subjects'));
    }

    /**
     * Chi tiết môn học
     */
    public function subjectDetail($id)
    {
        $subject = Subject::with([
            'lecturer',
            'classes',
            'classes.groups',
            'classes.groups.leader',
            'topics'
        ])->findOrFail($id);

        return view('user.subject_detail', compact('subject'));
    }
}
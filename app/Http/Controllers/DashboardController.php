<?php
namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\Topics;
use App\Models\Groups;
use App\Models\Topic_requests;
use App\Models\Join_requests;
use App\Models\Invites;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return $this->adminDashboard();
        } elseif ($user->role === 'lecturer') {
            return $this->lecturerDashboard($request);
        } else {
            return $this->studentDashboard();
        }
    }

    private function adminDashboard()
    {
        $stats = [
            'total_users' => Users::count(),
            'total_topics' => Topics::count(),
            'total_groups' => Groups::count(),
            'pending_requests' => Topic_requests::where('status', 'Pending')->count(),
        ];

        $recentUsers = Users::latest()->take(5)->get();
        $recentTopics = Topics::latest()->take(5)->get();
        $recentGroups = Groups::with('leader')->latest()->take(5)->get();
        $pendingTopicRequests = Topic_requests::where('status', 'Pending')
            ->with('topic', 'group.leader')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.admin', compact('stats', 'recentUsers', 'recentTopics', 'recentGroups', 'pendingTopicRequests'));
    }

    private function lecturerDashboard(Request $request)
    {
        $user = Auth::user();

        // Lấy tất cả các lớp có topics mà giảng viên phụ trách
        $lecturerClasses = ClassSection::whereHas('topics', function ($query) use ($user) {
            $query->where('lecturer', $user->name);
        })->get();

        // Tính toán statistics cho từng lớp
        $classStats = [];
        foreach ($lecturerClasses as $class) {
            $classTopics = Topics::where('lecturer', $user->name)
                ->where('class_id', $class->class_id);

            $classStats[$class->class_id] = [
                'topics' => (clone $classTopics)->count(),
                'pending' => Topic_requests::whereHas('topic', function ($q) use ($user, $class) {
                    $q->where('lecturer', $user->name)->where('class_id', $class->class_id);
                })->where('status', 'Pending')->count(),
                'approved' => Topic_requests::whereHas('topic', function ($q) use ($user, $class) {
                    $q->where('lecturer', $user->name)->where('class_id', $class->class_id);
                })->where('status', 'Accepted')->count(),
                'students' => Users::whereHas('classes', function ($q) use ($class) {
                    $q->where('classes.class_id', $class->class_id);
                })->where('role', 'student')->count(),
                'groups' => Groups::whereHas('members.classes', function ($q) use ($class) {
                    $q->where('classes.class_id', $class->class_id);
                })->count(),
            ];
        }

        // Overall statistics
        $stats = [
            'total_classes' => $lecturerClasses->count(),
            'assigned_topics' => Topics::where('lecturer', $user->name)->count(),
            'pending_requests' => Topic_requests::whereHas('topic', function ($query) use ($user) {
                $query->where('lecturer', $user->name);
            })->where('status', 'Pending')->count(),
            'approved_topics' => Topic_requests::whereHas('topic', function ($query) use ($user) {
                $query->where('lecturer', $user->name);
            })->where('status', 'Accepted')->count(),
        ];

        // Recent requests
        $recentRequests = Topic_requests::whereHas('topic', function ($query) use ($user) {
            $query->where('lecturer', $user->name);
        })->with(['topic.class', 'group'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.lecturer', compact(
            'stats',
            'lecturerClasses',
            'classStats',
            'recentRequests'
        ));
    }

    // Thêm method để xem chi tiết lớp
    public function classDetail($classId)
    {
        $user = Auth::user();
        $class = ClassSection::findOrFail($classId);

        // Kiểm tra quyền truy cập
        if ($user->role === 'lecturer') {
            $hasAccess = Topics::where('lecturer', $user->name)
                ->where('class_id', $classId)
                ->exists();
            if (!$hasAccess) {
                abort(403);
            }
        }

        // Lấy thông tin chi tiết
        $topics = Topics::where('class_id', $classId)
            ->with(['topic_requests.group'])
            ->get();

        $groups = Groups::whereHas('members.classes', function ($q) use ($classId) {
            $q->where('classes.class_id', $classId);
        })->with(['leader', 'members'])->get();

        $students = Users::whereHas('classes', function ($q) use ($classId) {
            $q->where('classes.class_id', $classId);
        })->where('role', 'student')->get();

        $topicRequests = Topic_requests::whereHas('topic', function ($q) use ($classId) {
            $q->where('class_id', $classId);
        })->with(['topic', 'group'])->latest()->get();

        return view('dashboard.class-detail', compact('class', 'topics', 'groups', 'students', 'topicRequests'));
    }

    private function studentDashboard()
    {
        $user = Auth::user();

        // Lấy các lớp của sinh viên từ bảng user_classes
        $studentClasses = ClassSection::whereHas('users', function ($query) use ($user) {
            $query->where('users.user_id', $user->user_id);
        })->get();

        // Hoặc nếu sinh viên chỉ thuộc 1 lớp, có thể lấy từ field class_id trong users table
        // $studentClass = Classes::where('class_id', $user->class_id)->first();

        $userGroup = Groups::whereHas('members', function ($query) use ($user) {
            $query->where('group_members.user_id', $user->user_id);
        })->first();

        $stats = [
            'my_group' => $userGroup ? 1 : 0,
            'my_classes' => $studentClasses->count(),
            'pending_invites' => Invites::where('member_id', $user->user_id)
                ->where('status', 'Pending')->count(),
            'pending_join_requests' => Join_requests::where('member_id', $user->user_id)
                ->where('status', 'Pending')->count(),
        ];

        $invites = Invites::where('member_id', $user->user_id)
            ->where('status', 'Pending')
            ->with('group.leader', 'leader')
            ->latest()
            ->take(5)
            ->get();

        $joinRequests = Join_requests::where('member_id', $user->user_id)
            ->where('status', 'Pending')
            ->with('group.leader')
            ->latest()
            ->take(5)
            ->get();

        // Lấy các topics available trong lớp của sinh viên
        // Topics chưa được nhóm nào của sinh viên đăng ký
        $availableTopicsQuery = Topics::whereNotIn('topic_id', function ($query) use ($user) {
            $query->select('topic_id')
                ->from('groups as g')
                ->join('group_members as gm', 'g.group_id', '=', 'gm.group_id')
                ->where('gm.user_id', $user->user_id)
                ->whereNotNull('g.topic_id');
        });

        // Chỉ lấy topics trong các lớp của sinh viên
        if ($studentClasses->isNotEmpty()) {
            $classIds = $studentClasses->pluck('class_id');
            $availableTopicsQuery->whereIn('class_id', $classIds);
        }

        $availableTopics = $availableTopicsQuery->with('class')->take(10)->get();

        return view('dashboard.student', compact(
            'stats',
            'invites',
            'joinRequests',
            'availableTopics',
            'userGroup',
            'studentClasses'
        ));
    }
}
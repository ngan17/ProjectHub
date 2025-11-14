<?php
namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\Topics;
use App\Models\Groups;
use App\Models\Topic_requests;
use App\Models\Join_requests;
use App\Models\Invites;
use App\Models\ClassSection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();


        if ($user->role === 'lecturer') {
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
                'students' => 0, // Tạm thời set 0 nếu không có quan hệ
                'groups' => 0,   // Tạm thời set 0 nếu không có quan hệ
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

        $groups = Groups::whereHas('class', function ($q) use ($classId) {
            $q->where('class_sections.class_id', $classId);
        })->with(['leader', 'members'])->get();

        $students =User::whereHas('classes', function ($q) use ($classId) {
            $q->where('class_sections.class_id', $classId);
        })->where('role', 'student')->get();

        $topicRequests = Topic_requests::whereHas('topic', function ($q) use ($classId) {
            $q->where('class_id', $classId);
        })->with(['topic', 'group'])->latest()->get();

        return view('dashboard.class-detail', compact('class', 'topics', 'groups', 'students', 'topicRequests'));
    }

    private function studentDashboard()
    {
        $user = Auth::user();

        // Lấy các nhóm mà user tham gia
        $myGroups = $this->getUserGroups($user);

        // Đếm số lượng thông báo chưa xử lý
        $pendingInvites = $this->countPendingInvites($user);
        $pendingRequests = $this->countPendingJoinRequests($user);

        // Lấy các đề tài của nhóm
        $myTopics = $this->getGroupTopics($myGroups);

        // Thông tin lớp và môn học
        $userClasses = $user->classes;
        $userSubjects = $this->getUserSubjects($userClasses);

        // Đề tài gợi ý
        $suggestedTopics = Topics::with('subject')
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('user.dashboard', compact(
            'myGroups',
            'pendingInvites',
            'pendingRequests',
            'myTopics',
            'userClasses',
            'userSubjects',
            'suggestedTopics'
        ));
    }
}
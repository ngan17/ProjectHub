<?php
namespace App\Http\Controllers;

use App\Models\User;
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


        if ($user->role === 'lecturer') {
            return $this->lecturerDashboard($request);
        } else {
            return $this->studentDashboard();
        }
    }

    private function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_topics' => Topics::count(),
            'total_groups' => Groups::count(),
            'pending_requests' => Topic_requests::where('status', 'Pending')->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
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

    // Lấy các lớp mà lecturer đang dạy
    $lecturerClasses = $user->classes;

    // Tính toán statistics cho từng lớp
    $classStats = [];
    foreach ($lecturerClasses as $class) {
        $topicsCount = Topics::where('class_id', $class->class_id)->count();
        
        $pendingCount = Topic_requests::whereHas('topic', function ($q) use ($class) {
            $q->where('class_id', $class->class_id);
        })->where('status', 'Pending')->count();
        
        $approvedCount = Topic_requests::whereHas('topic', function ($q) use ($class) {
            $q->where('class_id', $class->class_id);
        })->where('status', 'Accepted')->count();
        
        $groupsCount = Groups::where('class_id', $class->class_id)->count();
        
        $studentsCount = $class->users->where('role', 'student')->count();

        $classStats[$class->class_id] = [
            'class_name' => $class->class_name,
            'subject_name' => $class->subject->subject_name ?? 'N/A',
            'topics' => $topicsCount,
            'pending' => $pendingCount,
            'approved' => $approvedCount,
            'students' => $studentsCount,
            'groups' => $groupsCount,
        ];
    }

    // Overall statistics
    $lecturerClassIds = $lecturerClasses->pluck('class_id');
    
    $stats = [
        'total_classes' => $lecturerClasses->count(),
        'assigned_topics' => Topics::whereIn('class_id', $lecturerClassIds)->count(),
        'pending_requests' => Topic_requests::whereHas('topic', function ($query) use ($lecturerClassIds) {
            $query->whereIn('class_id', $lecturerClassIds);
        })->where('status', 'Pending')->count(),
        'approved_topics' => Topic_requests::whereHas('topic', function ($query) use ($lecturerClassIds) {
            $query->whereIn('class_id', $lecturerClassIds);
        })->where('status', 'Accepted')->count(),
    ];

    // Recent requests
    $recentRequests = Topic_requests::whereHas('topic', function ($query) use ($lecturerClassIds) {
        $query->whereIn('class_id', $lecturerClassIds);
    })->with(['topic.class', 'group.leader'])
        ->orderBy('created_at', 'desc')
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

        $students = User::whereHas('classes', function ($q) use ($classId) {
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
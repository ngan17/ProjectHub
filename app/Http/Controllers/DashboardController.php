<?php
namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\Topics;
use App\Models\Groups;
use App\Models\Topic_requests;
use App\Models\Join_requests;
use App\Models\Invites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return $this->adminDashboard();
        } elseif ($user->role === 'lecturer') {
            return $this->lecturerDashboard();
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

    private function lecturerDashboard()
    {
        $user = Auth::user();

        $stats = [
            'assigned_topics' => Topics::where('lecturer', $user->name)->count(),
            'pending_requests' => Topic_requests::where('status', 'Pending')->count(),
            'approved_topics' => Topic_requests::where('status', 'Accepted')->count(),
        ];

        $topics = Topics::where('lecturer', $user->name)->with('Topic_Requests.group.leader')->get();
        $topicRequests = Topic_requests::whereHas('topic', function ($query) use ($user) {
            $query->where('lecturer', $user->name);
        })->with('topic', 'group.leader')->paginate(10);

        return view('dashboard.lecturer', compact('stats', 'topics', 'topicRequests'));
    }

    private function studentDashboard()
    {
        $user = Auth::user();

        $userGroup = Groups::whereHas('members', function ($query) use ($user) {
            $query->where('group_members.user_id', $user->user_id);
        })->first();


        $stats = [
            'my_group' => $userGroup ? 1 : 0,
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

   $availableTopics = Topics::whereNotIn('topic_id', function ($query) use ($user) {
    $query->select('topic_id')
        ->from('groups as g')
        ->join('group_members as gm', 'g.group_id', '=', 'gm.group_id')
        ->where('gm.user_id', $user->user_id)
        ->whereNotNull('g.topic_id');
})->take(10)->get();


        return view('dashboard.student', compact('stats', 'invites', 'joinRequests', 'availableTopics', 'userGroup'));
    }
}
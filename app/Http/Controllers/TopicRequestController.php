<?php

namespace App\Http\Controllers;

use App\Models\Topic_requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topicRequests = Topic_requests::with(['topic', 'group', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('topic_requests.index', compact('topicRequests'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,topic_id',
            'group_id' => 'required|exists:groups,group_id',
        ]);

        Topic_requests::create([
            'topic_id' => $validated['topic_id'],
            'group_id' => $validated['group_id'],
            'created_by' => Auth::id(),
            'status' => 'Pending',
        ]);

        return redirect()->back()->with('success', 'Yêu cầu đã được gửi');
    }

    /**
     * Approve the specified topic request.
     */
    public function approve(Topic_requests $topic_request)
    {
        // Kiểm tra quyền: chỉ lecturer mới được phép duyệt
         $userRole = Auth::user()->role;
        if ( $userRole != 'lecturer') {
            abort(403, 'Không có quyên try cập '.$userRole);
        }

        $topic_request->update(['status' => 'Accepted']);
        $topic_request->group->update(['topic_id' => $topic_request->topic_id]);

        return back()->with('success', 'Yêu cầu đã được phê duyệt');
    }

    /**
     * Reject the specified topic request.
     */
    public function reject(Topic_requests $topic_request)
    {
         $userRole = Auth::user()->role;
        
        if ($userRole!='lecturer') {
            abort(403, 'Unauthorized action.');
        }

        $topic_request->update(['status' => 'Rejected']);
        return back()->with('success', 'Yêu cầu đã bị từ chối');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic_requests $topic_request)
    {
        // Kiểm tra quyền: chỉ người tạo hoặc admin mới được xóa
        $userRole=Auth::user()->role;
        if (Auth::id() !== $topic_request->created_by &&  $userRole=='admin') {
            abort(403, 'Unauthorized action.');
        }

        $topic_request->delete();
        return back()->with('success', 'Yêu cầu đã được xóa');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Topics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class TopicController extends Controller
{
    /**
     * Display a listing of the topics.
     */
    public function index()
    {
        $topics = Topics::paginate(10);
        return view('topics.index', compact('topics'));
    }

    /**
     * Show the form for creating a new topic.
     */
    public function create()
    {
        return view('topics.create');
    }

    /**
     * Store a newly created topic in storage.
     */

    /**
     * Display the specified topic.
     */
    public function show(Topics $topic)
    {
        return view('topics.show', compact('topic'));
    }

    /**
     * Show the form for editing the specified topic.
     */
    public function edit(Topics $topic)
    {
        return view('topics.edit', compact('topic'));
    }

    /**
     * Update the specified topic in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:topics',
        'description' => 'required|string|min:10',
        'goal' => 'nullable|string',
        'requirements' => 'nullable|string',
    ]);

    // Thêm lecturer tự động từ user đăng nhập
    $validated['lecturer'] = Auth::user()->name;

    Topics::create($validated);
    return redirect()->route('topics.index')->with('success', 'Thêm đề tài thành công!');
}

public function update(Request $request, Topics $topic)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:topics,name,' . $topic->topic_id . ',topic_id',
        'description' => 'required|string|min:10',
        'goal' => 'nullable|string',
        'requirements' => 'nullable|string',
    ]);

    // Lecturer không thay đổi
    $validated['lecturer'] = $topic->lecturer;

    $topic->update($validated);
    return redirect()->route('topics.index')->with('success', 'Cập nhật đề tài thành công!');
}
    /**
     * Remove the specified topic from storage.
     */
    public function destroy(Topics $topic)
    {
        $topic->delete();
        return redirect()->route('topics.index')->with('success', 'Xóa đề tài thành công!');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Groups;
use App\Models\User;
use App\Models\GroupMember;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    // Hiển thị danh sách nhóm
public function index()
{
    $groups = Groups::with(['leader', 'topic', 'members'])
        ->paginate(9);  // Phân trang 9 nhóm mỗi trang

    return view('groups.index', compact('groups'));
}
    // Form tạo nhóm
    public function create()
    {
        $leaders = User::where('role', 'leader')->get();
        return view('groups.create', compact('leaders'));
    }

    // Lưu nhóm mới
    public function store(Request $request)
    {
        $request->validate([
            'group_name' => 'required',
            'leader_id' => 'required|exists:users,id',
        ]);

        Groups::create($request->only('group_name', 'leader_id'));
        return redirect()->route('groups.index')->with('success', 'Tạo nhóm thành công!');
    }

    // Xem chi tiết nhóm
    public function show($id)
    {
        $group = Groups::with('leader', 'members', 'topic')->findOrFail($id);
        return view('groups.show', compact('group'));
    }
}

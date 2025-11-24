<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:student,leader,lecturer,admin',
        ]);

        // Password sẽ tự động hash trong model boot()
        User::create($validated);
        
        return redirect()->route('users.index')
            ->with('success', 'Người dùng đã được tạo thành công');
    }

    public function show(User $user)
    {
        // Load relationships theo tên đúng trong model
        $user->load([
            'groupsLed',        // Nhóm quản lý
            'groupsJoined',     // Nhóm tham gia
            'joinRequests',     // Yêu cầu tham gia
            'invites',          // Lời mời nhận
            'sentInvites',      // Lời mời đã gửi
            'classes'           // Lớp học
        ]);
        
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'role' => 'required|in:student,leader,lecturer,admin',
        ]);

        $user->update($validated);
        
        return redirect()->route('users.show', $user)
            ->with('success', 'Thông tin người dùng đã được cập nhật');
    }

    public function destroy(User $user)
    {
        $user->delete();
        
        return redirect()->route('users.index')
            ->with('success', 'Người dùng đã được xóa');
    }

    public function profile()
    {
        $user = Auth::user();
        
    
        
        if ($user->role == 'student') {
            return view('users.profile', ['user' => $user]);
        }
        
        return view('users.profile-admin', ['user' => $user]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        // Kiểm tra mật khẩu nếu muốn đổi
        if ($request->filled('new_password')) {
            if (!$request->filled('current_password')) {
                return redirect()->back()
                    ->with('error', 'Vui lòng nhập mật khẩu hiện tại để đổi mật khẩu mới');
            }

            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->with('error', 'Mật khẩu hiện tại không chính xác');
            }
            
            // Password sẽ tự động hash trong model boot()
            $validated['password'] = $request->new_password;
        }

        // Xóa các field không cần thiết
        unset($validated['current_password'], $validated['new_password']);

        /** @var User $user */
        $user->update($validated);
        
        return redirect()->route('users.profile')
            ->with('success', 'Hồ sơ đã được cập nhật thành công!');
    }
}
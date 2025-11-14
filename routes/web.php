<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', function () {
    return redirect()->route('groups.index');
});

Route::resource('groups', GroupController::class);
Route::resource('topics', TopicController::class);

// Lời mời
Route::get('invites', [InviteController::class, 'index'])->name('invites.index');
Route::get('invites/{id}/approve', [InviteController::class, 'approve'])->name('invites.approve');
Route::get('invites/{id}/reject', [InviteController::class, 'reject'])->name('invites.reject');






Route::prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin', [DashboardController::class, 'adminDashboard'])->name('dashboard.admin');
    Route::get('/lecturer', [DashboardController::class, 'lecturerDashboard'])->name('dashboard.lecturer');
    Route::get('/student', [DashboardController::class, 'studentDashboard'])->name('dashboard.student');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/class/{classId}', [DashboardController::class, 'classDetail'])->name('dashboard.class.detail');
});

Route::get('/requests', function () {
    return view('requests'); // hoặc view nào m muốn
})->name('requests');



Route::get('/', fn() => redirect('/login'));

//  Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::get('/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('users.updateProfile');
    Route::get('/profile-admin', [UserController::class, 'profile'])->name('users.profile-admin');
});
use Illuminate\Support\Facades\Auth;

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');
use App\Http\Controllers\TopicRequestController;

Route::controller(TopicRequestController::class)->middleware('auth')->group(function () {
    Route::get('/topic-requests', 'index')->name('topic_requests.index');


    Route::patch('/topic-requests/{topic_request}/approve', 'approve')->name('topic_requests.approve');
    Route::patch('/topic-requests/{topic_request}/reject', 'reject')->name('topic_requests.reject');
    Route::delete('/topic-requests/{topic_request}', 'destroy')->name('topic_requests.destroy');
});


Route::middleware(['auth'])->group(function () {
    // Student routes
    Route::resource('students', StudentController::class);
    
    // Import/Export routes
    Route::get('students-import/form', [StudentController::class, 'importForm'])->name('students.import.form');
    Route::post('students-import', [StudentController::class, 'import'])->name('students.import');
    Route::get('students-export', [StudentController::class, 'export'])->name('students.export');
    Route::get('students-template/download', [StudentController::class, 'downloadTemplate'])->name('students.download-template');
});


use App\Http\Controllers\UserDashboardController;

Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {

    
    // ============================================================
    // DASHBOARD
    // ============================================================
    Route::get('/dashboard', [UserDashboardController::class, 'index'])
        ->name('dashboard');
    
     Route::get('/groups/create', [UserDashboardController::class, 'createGroupForm'])
        ->name('create_group');
      Route::post('/groups/store', [UserDashboardController::class, 'storeGroup'])
     ->name('store_group');
        
    // ============================================================
    // TOPICS (Đề tài)
    // ============================================================
    
    // Danh sách đề tài với filter
    Route::get('/topics', [UserDashboardController::class, 'topics'])
        ->name('topics');
    
    // Chi tiết đề tài
    Route::get('/topics/{id}', [UserDashboardController::class, 'topicDetail'])
        ->name('topic_detail');
    
    // Đăng ký đề tài cho nhóm
    Route::post('/topics/register', [UserDashboardController::class, 'registerTopic'])
        ->name('register_topic');
    
    // Hủy đăng ký đề tài
    Route::delete('/topics/cancel/{requestId}', [UserDashboardController::class, 'cancelTopicRequest'])
        ->name('cancel-topic-request');
    
    // Đề tài của tôi
    Route::get('/my-topics', [UserDashboardController::class, 'myTopics'])
        ->name('my_topics');
    
    
    // ============================================================
    // GROUPS (Nhóm)
    // ============================================================
    
    // Danh sách nhóm của tôi
    Route::get('/groups', [UserDashboardController::class, 'myGroups'])
        ->name('my_groups');
    
    // Chi tiết nhóm
    Route::get('/groups/{id}', [UserDashboardController::class, 'groupDetail'])
        ->name('group_detail');
    
    
    // ============================================================
    // INVITATIONS (Lời mời)
    // ============================================================
    
    // Form mời thành viên vào nhóm (chỉ leader)
    Route::get('/groups/{groupId}/invite', [UserDashboardController::class, 'inviteMemberForm'])
        ->name('invite-member');
    
    // Gửi lời mời thành viên
    Route::post('/invites/send', [UserDashboardController::class, 'sendInvite'])
        ->name('send-invite');
    
    // Hủy lời mời (chỉ leader)
    Route::delete('/invites/{inviteId}', [UserDashboardController::class, 'cancelInvite'])
        ->name('cancel-invite');
    
    // Danh sách lời mời nhận được
    Route::get('/invites', [UserDashboardController::class, 'invites'])
        ->name('invites');
    
    // Chấp nhận lời mời
    Route::post('/invites/{id}/accept', [UserDashboardController::class, 'acceptInvite'])
        ->name('accept-invite');
    
    // Từ chối lời mời
    Route::post('/invites/{id}/reject', [UserDashboardController::class, 'rejectInvite'])
        ->name('reject-invite');
    
    
    // ============================================================
    // JOIN REQUESTS (Yêu cầu tham gia)
    // ============================================================
    
    // Gửi yêu cầu tham gia nhóm
    Route::post('/join_requests/send', [UserDashboardController::class, 'sendJoinRequest'])
        ->name('send-join-request');
    
    // Danh sách yêu cầu tham gia đã gửi
    Route::get('/join_requests', [UserDashboardController::class, 'joinRequests'])
        ->name('join-requests');
    
    // Hủy yêu cầu tham gia
    Route::delete('/join_requests/{id}', [UserDashboardController::class, 'cancelRequest'])
        ->name('cancel-request');
    
    // Danh sách yêu cầu tham gia nhóm (cho leader xem)
    Route::get('/groups/{groupId}/join-requests', [UserDashboardController::class, 'groupJoinRequests'])
        ->name('group-join-requests');
    
    // Chấp nhận yêu cầu tham gia (chỉ leader)
    Route::post('/join-requests/{requestId}/approve', [UserDashboardController::class, 'approveJoinRequest'])
        ->name('approve-join-request');
    
    // Từ chối yêu cầu tham gia (chỉ leader)
    Route::post('/join-requests/{requestId}/reject', [UserDashboardController::class, 'rejectJoinRequest'])
        ->name('reject-join-request');
    
    
    // ============================================================
    // CLASSES (Lớp học)
    // ============================================================
    
    // Danh sách lớp học
    Route::get('/classes', [UserDashboardController::class, 'classes'])
        ->name('classes');
    
    // Chi tiết lớp học
    Route::get('/classes/{id}', [UserDashboardController::class, 'classDetail'])
        ->name('class_detail');
    
    
    // ============================================================
    // SUBJECTS (Môn học)
    // ============================================================
    
    // Danh sách môn học
    Route::get('/subjects', [UserDashboardController::class, 'subjects'])
        ->name('subjects');
    
    // Chi tiết môn học
    Route::get('/subjects/{id}', [UserDashboardController::class, 'subjectDetail'])
        ->name('subject_detail');
});

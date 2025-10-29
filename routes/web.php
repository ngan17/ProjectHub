<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
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
});

Route::get('/requests', function () {
    return view('requests'); // hoặc view nào m muốn
})->name('requests');



Route::get('/', fn() => redirect('/login'));

// 🔐 Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::get('/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('users.updateProfile');
});
use Illuminate\Support\Facades\Auth;

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');
use App\Http\Controllers\TopicRequestController;

Route::controller(TopicRequestController::class)->middleware('auth')->group(function () {
    Route::get('/topic-requests', 'index')->name('topic_requests.index');

    // Sửa lỗi cú pháp: name(name: ...) → name(...)
    Route::patch('/topic-requests/{topic_request}/approve', 'approve')->name('topic_requests.approve');
    Route::patch('/topic-requests/{topic_request}/reject', 'reject')->name('topic_requests.reject');
    Route::delete('/topic-requests/{topic_request}', 'destroy')->name('topic_requests.destroy');
});
use App\Http\Controllers\UserDashboardController;

Route::prefix('user')->middleware('auth')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/topics', [UserDashboardController::class, 'topics'])->name('user.topics');
    Route::get('/topics/{id}', [UserDashboardController::class, 'topicDetail'])->name('user.topic-detail');
    Route::get('/my_groups', [UserDashboardController::class, 'myGroups'])->name('user.my_groups');
    Route::get('/groups/{id}', [UserDashboardController::class, 'groupDetail'])->name('user.group-detail');
    Route::get('/invites', [UserDashboardController::class, 'invites'])->name('user.invites');
    Route::post('/invites/{id}/accept', [UserDashboardController::class, 'acceptInvite'])->name('user.invite-accept');
    Route::get('/invites', [UserDashboardController::class, 'invites'])->name('user.invites');
    Route::post('/invites/{invite}/accept', [UserDashboardController::class, 'acceptInvite'])->name('user.accept-invite');
    Route::post('/invites/{invite}/reject', [UserDashboardController::class, 'rejectInvite'])->name('user.reject-invite');
    Route::post('/invites/{id}/reject', [UserDashboardController::class, 'rejectInvite'])->name('user.invite-reject');
    Route::get('/join-requests', [UserDashboardController::class, 'joinRequests'])->name('user.join-requests');
    Route::delete('/join-requests/{id}', [UserDashboardController::class, 'cancelRequest'])->name('user.request-cancel');
    Route::post('/topics/register', [UserDashboardController::class, 'registerTopic'])->name('user.topic-register');
    Route::get('/classes', [UserDashboardController::class, 'classes'])->name('user.classes');
    Route::get('/classes/{id}', [UserDashboardController::class, 'classDetail'])->name('user.class-detail');
    Route::get('/my_topics', [UserDashboardController::class, 'myTopics'])->name('my_topics');
    Route::get('/subjects', [UserDashboardController::class, 'subjects'])->name('user.subjects');
    Route::get('/groups/{group}/invite', [UserDashboardController::class, 'inviteMemberForm'])
        ->name('user.invite-member-form');
    Route::post('/invites/send', [UserDashboardController::class, 'sendInvite'])
        ->name('user.send-invite');
    Route::delete('/invites/{invite}/cancel', [UserDashboardController::class, 'cancelInvite'])
        ->name('user.cancel-invite');

    // Gửi yêu cầu tham gia nhóm
    Route::post('/join-requests/send', [UserDashboardController::class, 'sendJoinRequest'])
        ->name('user.send-join-request');

    // Quản lý yêu cầu tham gia nhóm (dành cho leader)
    Route::get('/groups/{group}/join-requests', [UserDashboardController::class, 'groupJoinRequests'])
        ->name('user.group-join-requests');
    Route::post('/join-requests/{request}/approve', [UserDashboardController::class, 'approveJoinRequest'])
        ->name('user.approve-join-request');
    Route::post('/join-requests/{request}/reject', [UserDashboardController::class, 'rejectJoinRequest'])
        ->name('user.reject-join-request');
    Route::get('/subjects/{id}', [UserDashboardController::class, 'subjectDetail'])->name('user.subject-detail');
});
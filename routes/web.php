<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\NoticeBoardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/boards', [NoticeBoardController::class, 'index'])->name('boards.index');
    Route::get('/boards/create', [NoticeBoardController::class, 'create'])->name('boards.create');
    Route::post('/boards', [NoticeBoardController::class, 'store'])->name('boards.store');
    Route::get('/boards/{board}', [NoticeBoardController::class, 'show'])->name('boards.show');
    Route::post('/boards/{board}/join', [NoticeBoardController::class, 'join'])->name('boards.join');

    Route::get('/boards/{board}/submit', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/boards/{board}/submit', [SubmissionController::class, 'store'])->name('submissions.store');

    Route::delete('/boards/{board}/leave', [NoticeBoardController::class, 'leave'])
        ->name('boards.leave');

    Route::patch('/boards/{board}/members/{user}/promote', [NoticeBoardController::class, 'promoteMember'])
        ->name('boards.members.promote');

    Route::patch('/boards/{board}/members/{user}/demote', [NoticeBoardController::class, 'demoteMember'])
        ->name('boards.members.demote');

    Route::delete('/boards/{board}/members/{user}', [NoticeBoardController::class, 'removeMember'])
        ->name('boards.members.remove');

    Route::patch('/boards/{board}/transfer-ownership/{user}', [NoticeBoardController::class, 'transferOwnership'])
        ->name('boards.transferOwnership');

    Route::post('/boards/{board}/invite', [NoticeBoardController::class, 'inviteMember'])
        ->name('boards.invite');

    Route::post('/invitations/{invitation}/accept', [NoticeBoardController::class, 'acceptInvitation'])
        ->name('boards.invitations.accept');

    Route::post('/invitations/{invitation}/decline', [NoticeBoardController::class, 'declineInvitation'])
        ->name('boards.invitations.decline');

    Route::get('/my-boards', [NoticeBoardController::class, 'myBoards'])
        ->name('boards.my');

    Route::get('/invitations', [InvitationController::class, 'index'])
        ->name('invitations.index');

    Route::get('/recommended', [NoticeBoardController::class, 'recommended'])
        ->name('boards.recommended');

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');

    Route::get('/trending', [NoticeBoardController::class, 'trending'])
        ->name('boards.trending');

    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::patch('/admin/submissions/{submission}/approve', [AdminController::class, 'approve'])->name('admin.approve');
    Route::patch('/admin/submissions/{submission}/reject', [AdminController::class, 'reject'])->name('admin.reject');
    Route::delete('/admin/submissions/{submission}', [AdminController::class, 'destroy'])->name('admin.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\NoticeBoardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionController;
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

    Route::middleware('admin')->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
        Route::patch('/admin/submissions/{submission}/approve', [AdminController::class, 'approve'])->name('admin.approve');
        Route::patch('/admin/submissions/{submission}/reject', [AdminController::class, 'reject'])->name('admin.reject');
        Route::delete('/admin/submissions/{submission}', [AdminController::class, 'destroy'])->name('admin.destroy');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

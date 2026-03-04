<?php
use App\Http\Controllers\NoticeBoardController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use inertia\Inertia;

Route::get('/', fn() => Inertia::render('Welcome'))->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn() => Inertia::render('Dashboard'))->name('dashboard');

    // Notice Boards
    Route::get('/boards', [NoticeBoardController::class, 'index'])->name('boards.index');
    Route::get('/boards/create', [NoticeBoardController::class, 'create'])->name('boards.create');
    Route::post('/boards', [NoticeBoardController::class, 'store'])->name('boards.store');
    Route::get('/boards/{board}', [NoticeBoardController::class, 'show'])->name('boards.show');
    Route::post('/boards/{board}/join', [NoticeBoardController::class, 'join'])->name('boards.join');

    // Submissions
    Route::get('/boards/{board}/submit', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/boards/{board}/submit', [SubmissionController::class, 'store'])->name('submissions.store');

    // Admin routes (protected by admin middleware)
    Route::middleware('admin')->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
        Route::patch('/admin/submissions/{submission}/approve', [AdminController::class, 'approve'])->name('admin.approve');
        Route::patch('/admin/submissions/{submission}/reject', [AdminController::class, 'reject'])->name('admin.reject');
        Route::delete('/admin/submissions/{submission}', [AdminController::class, 'destroy'])->name('admin.destroy');
    });
});

require __DIR__.'/auth.php';

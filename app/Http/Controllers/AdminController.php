<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Notifications\SubmissionApprovedNotification;
use App\Notifications\SubmissionRejectedNotification;
use Inertia\Inertia;

class AdminController extends Controller
{
    protected function canAccessAdminPanel($user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->memberships()
            ->where('role', 'admin')
            ->exists();
    }

    public function index()
    {
        $user = auth()->user();

        abort_unless($this->canAccessAdminPanel($user), 403, 'Unauthorized.');

        $isGlobalAdmin = $user->role === 'admin';

        $query = Submission::with(['board', 'user'])->latest();

        if (! $isGlobalAdmin) {
            $query->whereHas('board.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('role', 'admin');
            });
        }

        $pendingSubmissions = (clone $query)
            ->where('status', 'pending')
            ->get();

        $flaggedSubmissions = (clone $query)
            ->where('status', 'flagged')
            ->get();

        $rejectedSubmissions = (clone $query)
            ->where('status', 'rejected')
            ->get();

        return Inertia::render('Admin/Index', [
            'pendingSubmissions' => $pendingSubmissions,
            'flaggedSubmissions' => $flaggedSubmissions,
            'rejectedSubmissions' => $rejectedSubmissions,
        ]);
    }

    protected function authorizeBoardAdmin(Submission $submission): void
    {
        $user = auth()->user();

        $isGlobalAdmin = $user->role === 'admin';

        $isBoardAdmin = $submission->board->members()
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->exists();

        abort_unless($isGlobalAdmin || $isBoardAdmin, 403);
    }

    public function approve(Submission $submission)
    {
        $this->authorizeBoardAdmin($submission);

        $submission->update([
            'status' => 'approved',
            'moderation_reason' => null,
        ]);

        $submission->user->notify(
            new SubmissionApprovedNotification($submission, $submission->board)
        );

        return back()->with('success', 'Submission approved.');
    }

    public function reject(Submission $submission)
    {
        $this->authorizeBoardAdmin($submission);

        $submission->update([
            'status' => 'rejected',
        ]);

        $submission->user->notify(
            new SubmissionRejectedNotification($submission, $submission->board)
        );

        return back()->with('success', 'Submission rejected.');
    }

    public function destroy(Submission $submission)
    {
        $this->authorizeBoardAdmin($submission);

        if ($submission->file_path) {
            \Storage::disk('public')->delete($submission->file_path);
        }

        $submission->delete();

        return back()->with('success', 'Submission deleted.');
    }
}

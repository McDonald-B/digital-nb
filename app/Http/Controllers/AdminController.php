<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $isGlobalAdmin = $user->role === 'admin';

        $query = Submission::with(['board', 'user'])
            ->latest();

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

        return back()->with('success', 'Submission approved.');
    }

    public function reject(Submission $submission)
    {
        $this->authorizeBoardAdmin($submission);

        $submission->update([
            'status' => 'rejected',
        ]);

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

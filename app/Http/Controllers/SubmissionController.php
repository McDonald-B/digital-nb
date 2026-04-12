<?php

namespace App\Http\Controllers;

use App\Models\NoticeBoard;
use App\Models\Submission;
use App\Services\SubmissionModerationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubmissionController extends Controller
{
    public function create(NoticeBoard $board)
    {
        $user = auth()->user();

        $isMember = $board->members()
            ->where('user_id', $user->id)
            ->exists();

        if (! $isMember) {
            abort(403, 'You must join this board before submitting.');
        }

        return Inertia::render('Submissions/Create', [
            'board' => $board,
        ]);
    }

    public function store(Request $request, NoticeBoard $board, SubmissionModerationService $moderationService)
    {
        $user = $request->user();

        $isMember = $board->members()
            ->where('user_id', $user->id)
            ->exists();

        if (! $isMember) {
            abort(403, 'You must join this board before submitting.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:flyer,poster'],
            'content' => ['required_if:type,flyer', 'nullable', 'string'],
            'file' => ['required_if:type,poster', 'nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('submissions', 'public');
        }

        $moderation = $moderationService->moderate(
            $validated['title'],
            $validated['content'] ?? null
        );

        Submission::create([
            'notice_board_id' => $board->id,
            'user_id' => $user->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'file_path' => $filePath,
            'status' => $moderation['status'],
            'moderation_reason' => $moderation['reason'],
            'expires_at' => now()->addWeek(),
        ]);

        $message = $moderation['status'] === 'flagged'
            ? 'Submission created and automatically flagged for admin review.'
            : 'Submission sent for approval.';

        return redirect()
            ->route('boards.show', $board->id)
            ->with('success', $message);
    }
}

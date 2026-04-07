<?php

namespace App\Http\Controllers;

use App\Models\NoticeBoard;
use App\Models\Submission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubmissionController extends Controller
{
    public function create(NoticeBoard $board)
    {
        return Inertia::render('Submissions/Create', [
            'board' => [
                'id' => $board->id,
                'name' => $board->name,
            ],
        ]);
    }

    public function store(Request $request, NoticeBoard $board)
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

        Submission::create([
            'notice_board_id' => $board->id,
            'user_id' => $user->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'file_path' => $filePath,
            'status' => 'pending',
            'expires_at' => now()->addWeek(),
        ]);

        return redirect()
            ->route('boards.show', $board->id)
            ->with('success', 'Submission sent for approval!');
    }
}

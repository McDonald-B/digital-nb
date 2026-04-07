<?php

namespace App\Http\Controllers;

use App\Models\BoardMembership;
use App\Models\NoticeBoard;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoticeBoardController extends Controller
{
    public function index(Request $request)
    {
        $query = NoticeBoard::with('owner')
            ->where('is_private', false);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $boards = $query->latest()->get()->map(function ($board) use ($request) {
            $isMember = false;

            if ($request->user()) {
                $isMember = $board->members()
                    ->where('user_id', $request->user()->id)
                    ->exists();
            }

            return [
                'id' => $board->id,
                'name' => $board->name,
                'description' => $board->description,
                'is_private' => $board->is_private,
                'owner' => $board->owner
                    ? [
                        'id' => $board->owner->id,
                        'name' => $board->owner->name,
                    ]
                    : null,
                'is_member' => $isMember,
            ];
        });

        return Inertia::render('Boards/Index', [
            'boards' => $boards,
            'filters' => [
                'search' => $request->search ?? '',
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Boards/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_private' => ['required', 'boolean'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $board = $user->boards()->create($validated);

        BoardMembership::create([
            'user_id' => $user->id,
            'notice_board_id' => $board->id,
            'role' => 'admin',
        ]);

        return redirect()
            ->route('boards.show', $board->id)
            ->with('success', 'Board created successfully!');
    }

    public function show(NoticeBoard $board, Request $request)
    {
        $board->load('owner');

        $user = $request->user();

        $isMember = $board->members()
            ->where('user_id', $user->id)
            ->exists();

        if ($board->is_private && ! $isMember && ! $user->isAdmin()) {
            abort(403, 'This board is private.');
        }

        $submissions = $board->submissions()
            ->with('user')
            ->when(! $user->isAdmin(), function ($query) {
                $query->where('status', 'approved')
                    ->where('expires_at', '>', now());
            })
            ->latest()
            ->get();

        return Inertia::render('Boards/Show', [
            'board' => [
                'id' => $board->id,
                'name' => $board->name,
                'description' => $board->description,
                'is_private' => $board->is_private,
                'owner' => $board->owner
                    ? [
                        'id' => $board->owner->id,
                        'name' => $board->owner->name,
                    ]
                    : null,
            ],
            'submissions' => $submissions->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'title' => $submission->title,
                    'type' => $submission->type,
                    'content' => $submission->content,
                    'file_path' => $submission->file_path,
                    'status' => $submission->status,
                    'expires_at' => optional($submission->expires_at)?->toDateTimeString(),
                    'user' => $submission->user
                        ? [
                            'id' => $submission->user->id,
                            'name' => $submission->user->name,
                        ]
                        : null,
                ];
            }),
            'isMember' => $isMember,
        ]);
    }

    public function join(NoticeBoard $board, Request $request)
    {
        $user = $request->user();

        $alreadyMember = $board->members()
            ->where('user_id', $user->id)
            ->exists();

        if (! $alreadyMember) {
            BoardMembership::create([
                'user_id' => $user->id,
                'notice_board_id' => $board->id,
                'role' => 'member',
            ]);
        }

        return redirect()
            ->route('boards.show', $board->id)
            ->with('success', 'You joined the board.');
    }
}

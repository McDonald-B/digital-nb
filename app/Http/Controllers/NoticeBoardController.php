<?php

namespace App\Http\Controllers;

use App\Notifications\BoardInvitationNotification;
use App\Notifications\BoardAdminPromotedNotification;
use App\Models\BoardMembership;
use App\Models\NoticeBoard;
use App\Models\User;
use App\Models\BoardInvitation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoticeBoardController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\NoticeBoard::with('owner')
            ->where('is_private', false);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $boards = $query->latest()->get()->map(function ($board) {
            return [
                'id' => $board->id,
                'name' => $board->name,
                'description' => $board->description,
                'category' => $board->category,
                'is_private' => $board->is_private,
                'owner' => [
                    'name' => $board->owner?->name,
                ],
                'member_count' => $board->members()->count(),
            ];
        })->values();

        $categories = [
            'General',
            'Sports',
            'University',
            'Events',
            'Housing',
            'Jobs',
            'Society',
            'Marketplace',
        ];

        return \Inertia\Inertia::render('Boards/Index', [
            'boards' => $boards,
            'filters' => [
                'search' => $request->search ?? '',
                'category' => $request->category ?? '',
            ],
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return Inertia::render('Boards/Create');
    }

    public function store(Request $request, \App\Services\SubmissionModerationService $moderationService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'is_private' => 'boolean',
        ]);

        $moderation = $moderationService->moderate(
            $validated['name'],
            $validated['description'] ?? null
        );

        if ($moderation['status'] === 'flagged') {
            return back()->withErrors([
                'name' => $moderation['reason'],
            ])->withInput();
        }

        $board = auth()->user()->boards()->create($validated);

        BoardMembership::create([
            'user_id' => auth()->id(),
            'notice_board_id' => $board->id,
            'role' => 'admin',
        ]);

        return redirect()
            ->route('boards.show', $board->id)
            ->with('success', 'Board created successfully.');
    }

    public function show(NoticeBoard $board)
    {
        $board->load('owner');

        if ($board->is_private) {
            $isMemberOrOwner = $board->owner_id === auth()->id()
                || $board->members()
                ->where('user_id', auth()->id())
                ->exists();

            abort_unless($isMemberOrOwner, 403, 'This private board is only visible to members.');
        }

        $submissions = $board->submissions()
            ->with('user')
            ->where('status', 'approved')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->get();

        $membership = null;
        $isMember = false;
        $isBoardAdmin = false;
        $isOwner = false;

        if (auth()->check()) {
            $membership = $board->members()
                ->where('user_id', auth()->id())
                ->first();

            $isMember = (bool) $membership;
            $isBoardAdmin = $membership && $membership->role === 'admin';
            $isOwner = $board->owner_id === auth()->id();
        }

        $memberCount = $board->members()->count();

        $members = $board->members()
            ->with('user')
            ->get()
            ->map(function ($membership) use ($board) {
                return [
                    'id' => $membership->user->id,
                    'name' => $membership->user->name,
                    'email' => $membership->user->email,
                    'role' => $membership->role,
                    'is_owner' => $board->owner_id === $membership->user->id,
                ];
            })
            ->values();

        $pendingInvitations = $board->invitations()
            ->with('invitedUser')
            ->where('status', 'pending')
            ->get()
            ->map(function ($invitation) {
                return [
                    'id' => $invitation->id,
                    'email' => $invitation->invitedUser?->email,
                    'name' => $invitation->invitedUser?->name,
                ];
            })
            ->values();

        return \Inertia\Inertia::render('Boards/Show', [
            'board' => $board,
            'submissions' => $submissions,
            'isMember' => $isMember,
            'isBoardAdmin' => $isBoardAdmin,
            'isOwner' => $isOwner,
            'memberCount' => $memberCount,
            'members' => $members,
            'pendingInvitations' => $pendingInvitations,
        ]);
    }

    public function join(NoticeBoard $board)
    {
        if ($board->is_private) {
            return back()->with('error', 'This is a private board. You need an invitation to join.');
        }

        if (! $board->members()->where('user_id', auth()->id())->exists()) {
            BoardMembership::create([
                'user_id' => auth()->id(),
                'notice_board_id' => $board->id,
                'role' => 'member',
            ]);
        }

        return redirect()->route('boards.show', $board);
    }

    public function leave(NoticeBoard $board)
    {
        $user = auth()->user();

        $membership = $board->members()
            ->where('user_id', $user->id)
            ->first();

        if (! $membership) {
            return back()->with('error', 'You are not a member of this board.');
        }

        // Prevent the owner from leaving their own board
        if ($board->owner_id === $user->id) {
            return back()->with('error', 'Board owners cannot leave their own board.');
        }

        // If board admin, make sure at least one other admin remains
        if ($membership->role === 'admin') {
            $otherAdminsCount = $board->members()
                ->where('role', 'admin')
                ->where('user_id', '!=', $user->id)
                ->count();

            if ($otherAdminsCount === 0) {
                return back()->with('error', 'You cannot leave because this board would have no admin.');
            }
        }

        $membership->delete();

        return redirect()
            ->route('boards.index')
            ->with('success', 'You have left the board.');
    }

    public function promoteMember(\App\Models\NoticeBoard $board, \App\Models\User $user)
    {
        if ($board->owner_id !== auth()->id()) {
            return back()->with('error', 'Only the board owner can promote members.');
        }

        $membership = $board->members()
            ->where('user_id', $user->id)
            ->first();

        if (! $membership) {
            return back()->with('error', 'That user is not a member of this board.');
        }

        if ($board->owner_id === $user->id) {
            return back()->with('error', 'The owner does not need to be promoted.');
        }

        $membership->update([
            'role' => 'admin',
        ]);

        $user->notify(new BoardAdminPromotedNotification($board, auth()->user()));

        return back()->with('success', "{$user->name} has been promoted to board admin.");
    }

    public function demoteMember(NoticeBoard $board, User $user)
    {
        if ($board->owner_id !== auth()->id()) {
            return back()->with('error', 'Only the board owner can demote admins.');
        }

        $membership = $board->members()
            ->where('user_id', $user->id)
            ->first();

        if (! $membership) {
            return back()->with('error', 'That user is not a member of this board.');
        }

        if ($board->owner_id === $user->id) {
            return back()->with('error', 'You cannot demote the board owner.');
        }

        $membership->update([
            'role' => 'member',
        ]);

        return back()->with('success', "{$user->name} has been changed back to member.");
    }

    public function removeMember(NoticeBoard $board, User $user)
    {
        if ($board->owner_id !== auth()->id()) {
            return back()->with('error', 'Only the board owner can remove members.');
        }

        if ($board->owner_id === $user->id) {
            return back()->with('error', 'The board owner cannot be removed.');
        }

        $membership = $board->members()
            ->where('user_id', $user->id)
            ->first();

        if (! $membership) {
            return back()->with('error', 'That user is not a member of this board.');
        }

        $membership->delete();

        return back()->with('success', "{$user->name} has been removed from the board.");
    }

    public function transferOwnership(NoticeBoard $board, User $user)
    {
        if ($board->owner_id !== auth()->id()) {
            return back()->with('error', 'Only the board owner can transfer ownership.');
        }

        if ($board->owner_id === $user->id) {
            return back()->with('error', 'This user already owns the board.');
        }

        $newOwnerMembership = $board->members()
            ->where('user_id', $user->id)
            ->first();

        if (! $newOwnerMembership) {
            return back()->with('error', 'The new owner must already be a member of the board.');
        }

        $oldOwnerMembership = $board->members()
            ->where('user_id', auth()->id())
            ->first();

        $board->update([
            'owner_id' => $user->id,
        ]);

        // Ensure new owner is admin
        $newOwnerMembership->update([
            'role' => 'admin',
        ]);

        // Keep old owner as admin rather than owner
        if ($oldOwnerMembership) {
            $oldOwnerMembership->update([
                'role' => 'admin',
            ]);
        }

        return back()->with('success', "{$user->name} is now the board owner.");
    }

    public function inviteMember(\Illuminate\Http\Request $request, \App\Models\NoticeBoard $board)
    {
        if ($board->owner_id !== auth()->id()) {
            return back()->with('error', 'Only the board owner can invite users.');
        }

        if (! $board->is_private) {
            return back()->with('error', 'Invitations are only needed for private boards.');
        }

        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = \App\Models\User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()->with('error', 'No user found with that email address.');
        }

        if ($board->members()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'That user is already a member of this board.');
        }

        \App\Models\BoardInvitation::updateOrCreate(
            [
                'notice_board_id' => $board->id,
                'invited_user_id' => $user->id,
            ],
            [
                'invited_by' => auth()->id(),
                'status' => 'pending',
            ]
        );

        $user->notify(new BoardInvitationNotification($board, auth()->user()));

        return back()->with('success', "Invitation sent to {$user->email}.");
    }

    public function acceptInvitation(BoardInvitation $invitation)
    {
        if ($invitation->invited_user_id !== auth()->id()) {
            return back()->with('error', 'You cannot accept this invitation.');
        }

        if ($invitation->status !== 'pending') {
            return back()->with('error', 'This invitation is no longer pending.');
        }

        BoardMembership::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'notice_board_id' => $invitation->notice_board_id,
            ],
            [
                'role' => 'member',
            ]
        );

        $invitation->update([
            'status' => 'accepted',
        ]);

        return redirect()
            ->route('boards.show', $invitation->notice_board_id)
            ->with('success', 'Invitation accepted.');
    }

    public function declineInvitation(BoardInvitation $invitation)
    {
        if ($invitation->invited_user_id !== auth()->id()) {
            return back()->with('error', 'You cannot decline this invitation.');
        }

        if ($invitation->status !== 'pending') {
            return back()->with('error', 'This invitation is no longer pending.');
        }

        $invitation->update([
            'status' => 'declined',
        ]);

        return back()->with('success', 'Invitation declined.');
    }

    public function myBoards()
    {
        $user = auth()->user();

        $boards = NoticeBoard::with('owner')
            ->whereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->get()
            ->map(function ($board) use ($user) {
                $membership = $board->members()
                    ->where('user_id', $user->id)
                    ->first();

                return [
                    'id' => $board->id,
                    'name' => $board->name,
                    'description' => $board->description,
                    'is_private' => $board->is_private,
                    'owner' => [
                        'name' => $board->owner?->name,
                    ],
                    'member_role' => $membership?->role,
                    'member_count' => $board->members()->count(),
                ];
            })
            ->values();

        return \Inertia\Inertia::render('Boards/MyBoards', [
            'boards' => $boards,
        ]);
    }

    public function recommended()
    {
        $user = auth()->user();

        $joinedBoardIds = \App\Models\BoardMembership::where('user_id', $user->id)
            ->pluck('notice_board_id');

        $joinedCategories = \App\Models\NoticeBoard::whereIn('id', $joinedBoardIds)
            ->pluck('category')
            ->filter()
            ->unique()
            ->values();

        $recommendedBoards = \App\Models\NoticeBoard::with('owner')
            ->whereNotIn('id', $joinedBoardIds)
            ->whereIn('category', $joinedCategories)
            ->where('is_private', false)
            ->latest()
            ->get()
            ->map(function ($board) {
                return [
                    'id' => $board->id,
                    'name' => $board->name,
                    'description' => $board->description,
                    'category' => $board->category,
                    'owner' => [
                        'name' => $board->owner?->name,
                    ],
                    'member_count' => $board->members()->count(),
                ];
            })
            ->values();

        return \Inertia\Inertia::render('Boards/Recommended', [
            'recommendedBoards' => $recommendedBoards,
            'joinedCategories' => $joinedCategories,
        ]);
    }

    public function trending()
    {
        $boards = \App\Models\NoticeBoard::with('owner')
            ->where('is_private', false)
            ->get()
            ->map(function ($board) {
                $memberCount = $board->members()->count();

                $approvedSubmissionCount = $board->submissions()
                    ->where('status', 'approved')
                    ->where(function ($query) {
                        $query->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    })
                    ->count();

                $trendingScore = ($memberCount * 2) + ($approvedSubmissionCount * 3);

                return [
                    'id' => $board->id,
                    'name' => $board->name,
                    'description' => $board->description,
                    'category' => $board->category,
                    'owner' => [
                        'name' => $board->owner?->name,
                    ],
                    'member_count' => $memberCount,
                    'approved_submission_count' => $approvedSubmissionCount,
                    'trending_score' => $trendingScore,
                ];
            })
            ->sortByDesc('trending_score')
            ->take(10)
            ->values();

        return \Inertia\Inertia::render('Boards/Trending', [
            'boards' => $boards,
        ]);
    }
}

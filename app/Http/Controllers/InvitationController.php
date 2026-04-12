<?php

namespace App\Http\Controllers;

use App\Models\BoardInvitation;
use Inertia\Inertia;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = BoardInvitation::with(['board', 'inviter'])
            ->where('invited_user_id', auth()->id())
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(function ($invitation) {
                return [
                    'id' => $invitation->id,
                    'board_name' => $invitation->board?->name,
                    'board_id' => $invitation->notice_board_id,
                    'invited_by' => $invitation->inviter?->name,
                ];
            })
            ->values();

        return Inertia::render('Invitations/Index', [
            'invitations' => $invitations,
        ]);
    }
}
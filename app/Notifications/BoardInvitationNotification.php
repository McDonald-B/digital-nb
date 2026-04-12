<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BoardInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public $board,
        public $inviter
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'board_invitation',
            'title' => 'Board Invitation',
            'message' => "{$this->inviter->name} invited you to join the board \"{$this->board->name}\".",
            'board_id' => $this->board->id,
            'board_name' => $this->board->name,
            'inviter_name' => $this->inviter->name,
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BoardAdminPromotedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public $board,
        public $owner
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'board_admin_promoted',
            'title' => 'Promoted to Board Admin',
            'message' => "{$this->owner->name} promoted you to board admin in \"{$this->board->name}\".",
            'board_id' => $this->board->id,
            'board_name' => $this->board->name,
            'owner_name' => $this->owner->name,
        ];
    }
}

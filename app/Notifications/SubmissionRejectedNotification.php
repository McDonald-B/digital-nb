<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubmissionRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public $submission,
        public $board
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'submission_rejected',
            'title' => 'Submission Rejected',
            'message' => "Your submission \"{$this->submission->title}\" was rejected in \"{$this->board->name}\".",
            'board_id' => $this->board->id,
            'board_name' => $this->board->name,
            'submission_id' => $this->submission->id,
            'submission_title' => $this->submission->title,
        ];
    }
}

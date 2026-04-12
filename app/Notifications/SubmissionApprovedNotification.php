<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubmissionApprovedNotification extends Notification
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
            'type' => 'submission_approved',
            'title' => 'Submission Approved',
            'message' => "Your submission \"{$this->submission->title}\" was approved in \"{$this->board->name}\".",
            'board_id' => $this->board->id,
            'board_name' => $this->board->name,
            'submission_id' => $this->submission->id,
            'submission_title' => $this->submission->title,
        ];
    }
}

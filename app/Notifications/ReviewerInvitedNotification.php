<?php

namespace App\Notifications;

use App\Models\Conference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReviewerInvitedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Conference $conference,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'reviewer_invited',
            'title' => 'Reviewer invitation',
            'message' => "You have been invited to review submissions for {$this->conference->title}.",
            'url' => route('reviewing.index'),

            'conference_id' => $this->conference->id,
            'conference_title' => $this->conference->title,
        ];
    }
}

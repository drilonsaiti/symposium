<?php

namespace App\Notifications;

use App\Models\Conference;
use App\Models\Talk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TalkSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Conference $conference,
        public readonly Talk $talk,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $submitterName = $this->talk->author?->name ?? 'A speaker';

        return [
            'type' => 'talk_submitted',
            'title' => 'New talk submitted',
            'message' => "{$submitterName} submitted \"{$this->talk->title}\" to {$this->conference->title}.",
            'url' => route('talks.show', $this->talk),

            'conference_id' => $this->conference->id,
            'conference_title' => $this->conference->title,
            'talk_id' => $this->talk->id,
            'talk_title' => $this->talk->title,
            'submitter_name' => $submitterName,
        ];
    }
}

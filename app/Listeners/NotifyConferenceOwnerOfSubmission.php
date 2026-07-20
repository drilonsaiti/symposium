<?php

namespace App\Listeners;

use App\Events\TalkWasSubmitted;
use App\Notifications\TalkSubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;

class NotifyConferenceOwnerOfSubmission implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TalkWasSubmitted $event): void
    {
        $event->conference->user->notify(new TalkSubmittedNotification($event->conference,$event->talk));
    }
}

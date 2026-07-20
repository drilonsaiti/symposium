<?php

namespace App\Listeners;

use App\Events\SubmissionStatusChanged;
use App\Notifications\SubmissionStatusChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifySubmitterOfStatusChange implements ShouldQueue
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
    public function handle(SubmissionStatusChanged $event): void
    {
       $event->talk->author->notify(new SubmissionStatusChangedNotification($event->conference,$event->talk,$event->status));
    }
}

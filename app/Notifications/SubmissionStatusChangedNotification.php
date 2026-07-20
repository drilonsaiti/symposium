<?php

namespace App\Notifications;

use App\Enum\TalkSubmissionStatus;
use App\Models\Conference;
use App\Models\Talk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Conference $conference,
        public readonly Talk $talk,
        public readonly TalkSubmissionStatus $status,
    ) {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (in_array($this->status, [
            TalkSubmissionStatus::ACCEPTED,
            TalkSubmissionStatus::REJECTED,
        ], true)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        [$subject, $message] = match ($this->status) {
            TalkSubmissionStatus::ACCEPTED => [
                'Your talk has been accepted!',
                "Your talk \"{$this->talk->title}\" has been accepted for {$this->conference->title}.",
            ],
            TalkSubmissionStatus::REJECTED => [
                'Your talk was not accepted',
                "Your talk \"{$this->talk->title}\" was not accepted for {$this->conference->title}.",
            ],
            default => [
                'Your talk submission has been updated',
                "The status of your talk \"{$this->talk->title}\" for {$this->conference->title} has been updated.",
            ],
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line($message)
            ->action(
                'View Conference',
                route('conferences.show', $this->conference)
            );
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'submission_status_changed',
            'title' => $this->databaseTitle(),
            'message' => $this->databaseMessage(),
            'url' => route('talks.show', $this->talk),

            'conference_id' => $this->conference->id,
            'conference_title' => $this->conference->title,
            'talk_id' => $this->talk->id,
            'talk_title' => $this->talk->title,
            'status' => $this->status->value,
        ];
    }

    private function databaseTitle(): string
    {
        return match ($this->status) {
            TalkSubmissionStatus::ACCEPTED => 'Talk accepted',
            TalkSubmissionStatus::REJECTED => 'Talk not accepted',
            default => 'Submission status updated',
        };
    }

    private function databaseMessage(): string
    {
        return match ($this->status) {
            TalkSubmissionStatus::ACCEPTED =>
            "\"{$this->talk->title}\" has been accepted for {$this->conference->title}.",

            TalkSubmissionStatus::REJECTED =>
            "\"{$this->talk->title}\" was not accepted for {$this->conference->title}.",

            default =>
            "The status of \"{$this->talk->title}\" for {$this->conference->title} is now {$this->status->value}.",
        };
    }
}

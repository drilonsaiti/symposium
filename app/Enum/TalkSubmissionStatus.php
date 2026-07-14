<?php

namespace App\Enum;

enum TalkSubmissionStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ACCEPTED => 'Accepted',
            self::REJECTED => 'Rejected',
        };
    }


    public function canStatusChangeTo(self $newStatus): bool
    {
        return match ($this) {
            self::PENDING => in_array($newStatus, [
                self::ACCEPTED,
                self::REJECTED,
            ], true),
            self::ACCEPTED => in_array($newStatus, [self::REJECTED], true),
            self::REJECTED => in_array($newStatus, [self::PENDING], true),
        };
    }

}

<?php

namespace App\Enum;

enum ConferenceReviewerStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ACCEPTED => 'Approved',
            self::DECLINED => 'Declined',
        };
    }

    public function canTransitionTo(ConferenceReviewerStatus $status): bool
    {
        return match ($this) {
            self::PENDING => $status === self::ACCEPTED,
            self::ACCEPTED => false,
            self::DECLINED => false,
        };
    }
}

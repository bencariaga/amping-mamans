<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case PENDING = 'Pending';
    case APPROVED = 'Approved';
    case REJECTED = 'Rejected';
    case PROCESSING = 'Processing';
    case COMPLETED = 'Completed';
    case CANCELLED = 'Cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending Review',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::PROCESSING => 'blue',
            self::COMPLETED => 'green',
            self::CANCELLED => 'gray',
        };
    }

    public function canTransitionTo(ApplicationStatus $status): bool
    {
        return match($this) {
            self::PENDING => in_array($status, [self::APPROVED, self::REJECTED, self::CANCELLED]),
            self::APPROVED => in_array($status, [self::PROCESSING, self::CANCELLED]),
            self::PROCESSING => in_array($status, [self::COMPLETED, self::CANCELLED]),
            self::REJECTED, self::COMPLETED, self::CANCELLED => false,
        };
    }
}

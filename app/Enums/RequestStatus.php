<?php

namespace App\Enums;

enum RequestStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Declined = 'declined';
    case Withdrawn = 'withdrawn';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Waiting for donor',
            self::Accepted => 'Accepted',
            self::Declined => 'Declined',
            self::Withdrawn => 'Withdrawn',
            self::Completed => 'Completed',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'badge-amber',
            self::Accepted => 'badge-forest',
            self::Declined => 'badge-slate',
            self::Withdrawn => 'badge-slate',
            self::Completed => 'badge-forest',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this, [self::Pending, self::Accepted], true);
    }
}

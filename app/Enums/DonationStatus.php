<?php

namespace App\Enums;

enum DonationStatus: string
{
    case Listed = 'listed';
    case Requested = 'requested';
    case Accepted = 'accepted';
    case Scheduled = 'scheduled';
    case Received = 'received';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Listed => 'Listed',
            self::Requested => 'Requested',
            self::Accepted => 'Accepted',
            self::Scheduled => 'Scheduled',
            self::Received => 'Received',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Listed => 'badge-forest',
            self::Requested => 'badge-amber',
            self::Accepted => 'badge-clay',
            self::Scheduled => 'badge-clay',
            self::Received => 'badge-forest',
            self::Completed => 'badge-forest',
            self::Cancelled => 'badge-slate',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Listed => 'Visible to upcyclers in the feed.',
            self::Requested => 'An upcycler has asked for this.',
            self::Accepted => 'You accepted a request. Arrange the handover.',
            self::Scheduled => 'A pickup or drop-off has a date.',
            self::Received => 'The upcycler has the items in hand.',
            self::Completed => 'Done — this donation is now part of someone’s work.',
            self::Cancelled => 'Withdrawn from the platform.',
        };
    }

    public function isOpen(): bool
    {
        return ! in_array($this, [self::Completed, self::Cancelled], true);
    }

    public function isTerminal(): bool
    {
        return ! $this->isOpen();
    }

    /** The linear happy path used by the progress stepper. */
    public static function journey(): array
    {
        return [self::Listed, self::Requested, self::Accepted, self::Scheduled, self::Received, self::Completed];
    }

    public function stepIndex(): int
    {
        $index = array_search($this, self::journey(), true);

        return $index === false ? 0 : $index;
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $s) => [$s->value => $s->label()])->all();
    }
}

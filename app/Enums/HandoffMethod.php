<?php

namespace App\Enums;

enum HandoffMethod: string
{
    case Pickup = 'pickup';
    case Dropoff = 'dropoff';

    public function label(): string
    {
        return match ($this) {
            self::Pickup => 'Pickup at my place',
            self::Dropoff => 'Drop-off at a partner site',
        };
    }

    public function short(): string
    {
        return match ($this) {
            self::Pickup => 'Pickup',
            self::Dropoff => 'Drop-off',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $m) => [$m->value => $m->label()])->all();
    }
}

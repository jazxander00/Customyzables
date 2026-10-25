<?php

namespace App\Enums;

enum Role: string
{
    case Donor = 'donor';
    case Upcycler = 'upcycler';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Donor => 'Donor',
            self::Upcycler => 'Upcycler',
            self::Admin => 'Administrator',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Donor => 'I have pre-loved clothing to give.',
            self::Upcycler => 'I turn donated textiles into new things.',
            self::Admin => 'I keep the community safe and organised.',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Donor => 'dashboard.donor',
            self::Upcycler => 'dashboard.upcycler',
            self::Admin => 'admin.dashboard',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->reject(fn (self $role) => $role === self::Admin)
            ->mapWithKeys(fn (self $role) => [$role->value => $role->label()])
            ->all();
    }
}

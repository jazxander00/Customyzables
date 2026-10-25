<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'barangay',
        'phone',
        'organization',
        'bio',
        'avatar_path',
        'is_verified',
        'verified_at',
        'is_suspended',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => Role::class,
        'is_verified' => 'boolean',
        'is_suspended' => 'boolean',
    ];

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function donationRequests(): HasMany
    {
        return $this->hasMany(DonationRequest::class, 'upcycler_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function isDonor(): bool
    {
        return $this->role === Role::Donor;
    }

    public function isUpcycler(): bool
    {
        return $this->role === Role::Upcycler;
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function canPostDonations(): bool
    {
        return $this->isDonor() && ! $this->is_suspended;
    }

    public function canRequestDonations(): bool
    {
        return $this->isUpcycler() && $this->is_verified && ! $this->is_suspended;
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');
    }

    /** Total kilograms this donor has successfully handed over. */
    public function donatedWeight(): float
    {
        return (float) $this->donations()
            ->whereIn('status', ['received', 'completed'])
            ->sum('weight_kg');
    }

    /** Total kilograms this upcycler has claimed. */
    public function claimedWeight(): float
    {
        return (float) Donation::query()
            ->whereIn('status', ['received', 'completed'])
            ->whereHas('acceptedRequest', fn ($q) => $q->where('upcycler_id', $this->id))
            ->sum('weight_kg');
    }
}

<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationRequest extends Model
{
    protected $fillable = [
        'donation_id',
        'upcycler_id',
        'message',
        'status',
        'scheduled_at',
        'scheduled_note',
        'responded_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => RequestStatus::class,
        'scheduled_at' => 'datetime',
        'responded_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function upcycler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'upcycler_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'request_id');
    }

    public function isPending(): bool
    {
        return $this->status === RequestStatus::Pending;
    }

    public function isAccepted(): bool
    {
        return $this->status === RequestStatus::Accepted;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['donation_id', 'request_id', 'sender_id', 'body', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(DonationRequest::class, 'request_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function scopeUnreadFor(Builder $query, User $user): Builder
    {
        return $query->whereNull('read_at')->where('sender_id', '!=', $user->id);
    }
}

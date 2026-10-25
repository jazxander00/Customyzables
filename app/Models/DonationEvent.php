<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationEvent extends Model
{
    protected $fillable = ['donation_id', 'actor_id', 'from_status', 'to_status', 'note'];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}

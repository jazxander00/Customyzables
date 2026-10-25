<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationImage extends Model
{
    protected $fillable = ['donation_id', 'path', 'position'];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function url(): string
    {
        return asset('storage/' . $this->path);
    }
}

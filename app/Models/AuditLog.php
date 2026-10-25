<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = ['actor_id', 'action', 'subject_type', 'subject_id', 'meta', 'ip'];

    protected $casts = [
        'meta' => 'array',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Every privileged action writes here. Keep the action name dotted and stable:
     * "user.verified", "donation.removed", "request.accepted".
     */
    public static function record(string $action, ?Model $subject = null, array $meta = [], ?User $actor = null): self
    {
        return static::create([
            'actor_id' => $actor?->id ?? auth()->id(),
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'meta' => $meta ?: null,
            'ip' => request()->ip(),
        ]);
    }
}

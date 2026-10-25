<?php

namespace App\Models;

use App\Enums\DonationStatus;
use App\Enums\HandoffMethod;
use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'condition',
        'weight_kg',
        'item_count',
        'barangay',
        'handoff_method',
        'pickup_address',
        'available_from',
        'status',
    ];

    protected $casts = [
        'status' => DonationStatus::class,
        'handoff_method' => HandoffMethod::class,
        'weight_kg' => 'decimal:2',
        'available_from' => 'date',
    ];

    protected $attributes = [
        'status' => 'listed',
    ];

    public const CATEGORIES = [
        'denim' => 'Denim',
        'cotton' => '100% Cotton',
        'linen' => 'Linen & Canvas',
        'knit' => 'Knitwear',
        'blend' => 'Mixed / Synthetic',
        'other' => 'Other textiles',
    ];

    public const CONDITIONS = [
        'new' => 'New / unworn',
        'gently_used' => 'Gently used / wearable',
        'wearable' => 'Wearable with minor flaws',
        'flawed' => 'Flawed / repairable',
        'scrap' => 'Scrap fabric only',
    ];

    public const BARANGAYS = [
        'Pajo', 'Mactan', 'Gun-ob', 'Marigondon', 'Punta Engaño', 'Basak',
        'Bankal', 'Babag', 'Buaya', 'Canjulao', 'Ibo', 'Looc', 'Mactan',
        'Pusok', 'Sa-ib', 'San Vicente', 'Santa Rosa', 'Suba-basbas', 'Talima', 'Tingo',
    ];

    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(DonationImage::class)->orderBy('position');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(DonationRequest::class);
    }

    public function pendingRequests(): HasMany
    {
        return $this->requests()->where('status', RequestStatus::Pending->value);
    }

    public function acceptedRequest(): HasOne
    {
        return $this->hasOne(DonationRequest::class)->where('status', RequestStatus::Accepted->value);
    }

    public function events(): HasMany
    {
        return $this->hasMany(DonationEvent::class)->latest();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', DonationStatus::Listed->value);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['q'] ?? null, function (Builder $q, string $term) {
                $q->where(function (Builder $inner) use ($term) {
                    $inner->where('title', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->when($filters['category'] ?? null, fn (Builder $q, string $c) => $q->where('category', $c))
            ->when($filters['barangay'] ?? null, fn (Builder $q, string $b) => $q->where('barangay', $b))
            ->when($filters['condition'] ?? null, fn (Builder $q, string $c) => $q->where('condition', $c));
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? Str::title($this->category);
    }

    public function conditionLabel(): string
    {
        return self::CONDITIONS[$this->condition] ?? Str::title($this->condition);
    }

    public function weightLabel(): string
    {
        return rtrim(rtrim(number_format((float) $this->weight_kg, 2), '0'), '.') . ' kg';
    }

    public function displayImage(): ?string
    {
        $image = $this->images->first();

        return $image ? asset('storage/' . $image->path) : null;
    }

    public function progressPercent(): int
    {
        return (int) round(($this->status->stepIndex() / (count(DonationStatus::journey()) - 1)) * 100);
    }

    public function isOpen(): bool
    {
        return $this->status->isOpen();
    }

    public function hasOpenRequestFrom(User $user): bool
    {
        return $this->requests()
            ->where('upcycler_id', $user->id)
            ->whereIn('status', [RequestStatus::Pending->value, RequestStatus::Accepted->value])
            ->exists();
    }
}

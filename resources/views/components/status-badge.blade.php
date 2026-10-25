@props(['status'])

@php
    /** @var \App\Enums\DonationStatus|string $status */
    $enum = $status instanceof \App\Enums\DonationStatus ? $status : \App\Enums\DonationStatus::from($status);
@endphp

<span {{ $attributes->merge(['class' => $enum->badge()]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
    {{ $enum->label() }}
</span>

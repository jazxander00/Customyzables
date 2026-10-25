@props(['class' => 'h-9 w-9'])

{{-- A loop of fabric: circular arrow + stitch ticks. Drawn once, reused everywhere. --}}
<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 40 40" fill="none" role="img" aria-label="Customyzables logo">
    <rect width="40" height="40" rx="12" fill="#FAE6DC"/>
    <path d="M28.5 15.5a9.5 9.5 0 1 0 1.2 7.2" stroke="#C96345" stroke-width="2.4" stroke-linecap="round"/>
    <path d="M29.6 11.4v5.1h-5.1" stroke="#C96345" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
    <circle cx="20" cy="20" r="3.4" stroke="#2F6B4F" stroke-width="2.2"/>
</svg>

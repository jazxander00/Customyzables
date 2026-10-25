@props(['donation', 'href' => null])

@php
    $href = $href ?? route('browse.show', $donation);
    $image = $donation->displayImage();
@endphp

<article class="group card overflow-hidden transition duration-300 ease-soft hover:-translate-y-0.5 hover:shadow-lift">
    <a href="{{ $href }}" class="block">
        <div class="relative aspect-[4/3] overflow-hidden bg-sand-200">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $donation->title }}"
                     class="h-full w-full object-cover transition duration-500 ease-soft group-hover:scale-[1.03]" loading="lazy">
            @else
                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-sand-200 via-sand-100 to-clay-100">
                    <svg class="h-10 w-10 text-sand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="14" rx="2.5"/><circle cx="9" cy="10" r="1.6"/><path d="m4 17 4.5-4.5L13 17l3-3 4 4"/>
                    </svg>
                </div>
            @endif

            <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
                <span class="badge bg-white/95 text-ink shadow-sm">{{ $donation->weightLabel() }}</span>
                <span class="badge bg-white/95 text-ink-muted shadow-sm">{{ $donation->conditionLabel() }}</span>
            </div>

            @if ($donation->status !== \App\Enums\DonationStatus::Listed)
                <div class="absolute right-3 top-3">
                    <x-status-badge :status="$donation->status" class="shadow-sm" />
                </div>
            @endif
        </div>
    </a>

    <div class="p-4">
        <div class="flex items-center justify-between gap-2 text-[11px] text-ink-faint">
            <span class="font-semibold uppercase tracking-[0.12em]">{{ $donation->categoryLabel() }}</span>
            <span>{{ $donation->created_at->diffForHumans(short: true) }}</span>
        </div>

        <h3 class="mt-2 line-clamp-2 text-[15px] font-bold leading-snug">
            <a href="{{ $href }}" class="hover:text-clay-700">{{ $donation->title }}</a>
        </h3>

        <p class="mt-1.5 line-clamp-2 text-xs leading-relaxed text-ink-muted">{{ $donation->description }}</p>

        <div class="mt-3 flex items-center justify-between gap-2 border-t border-sand-200 pt-3">
            <div class="flex min-w-0 items-center gap-1.5 text-[11px] text-ink-muted">
                <svg class="h-3.5 w-3.5 shrink-0 text-clay-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                <span class="truncate">{{ $donation->barangay }}</span>
            </div>

            <a href="{{ $href }}" class="btn-secondary btn-sm shrink-0">View details</a>
        </div>
    </div>
</article>

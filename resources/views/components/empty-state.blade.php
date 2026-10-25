@props(['title', 'description' => null, 'action' => null, 'actionHref' => null])

<div {{ $attributes->merge(['class' => 'card flex flex-col items-center justify-center px-6 py-12 text-center']) }}>
    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sand-200 text-ink-faint">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 7.5A2.5 2.5 0 0 1 5.5 5h13A2.5 2.5 0 0 1 21 7.5v9a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 16.5v-9Z"/>
            <path d="M3 9h18M7 13h6"/>
        </svg>
    </div>

    <h3 class="mt-4 text-base font-bold">{{ $title }}</h3>

    @if ($description)
        <p class="mt-1.5 max-w-sm text-sm text-ink-muted">{{ $description }}</p>
    @endif

    @if ($action && $actionHref)
        <a href="{{ $actionHref }}" class="btn-secondary mt-5">{{ $action }}</a>
    @endif
</div>

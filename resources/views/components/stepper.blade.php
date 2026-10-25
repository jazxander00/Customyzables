@props(['donation'])

@php
    $journey = \App\Enums\DonationStatus::journey();
    $currentIndex = $donation->status->stepIndex();
    $cancelled = $donation->status === \App\Enums\DonationStatus::Cancelled;
@endphp

<div {{ $attributes->merge(['class' => '']) }} data-stepper>
    <div class="mb-5 h-1.5 w-full overflow-hidden rounded-pill bg-sand-200">
        <div class="h-full rounded-pill bg-forest-600" data-stepper-bar data-progress="{{ $cancelled ? 0 : $donation->progressPercent() }}"></div>
    </div>

    <ol class="grid grid-cols-3 gap-3 sm:grid-cols-6">
        @foreach ($journey as $index => $step)
            @php
                $state = $cancelled ? 'cancelled' : ($index < $currentIndex ? 'done' : ($index === $currentIndex ? 'current' : 'todo'));
            @endphp

            <li data-step data-step-state="{{ $state }}" class="flex flex-col items-center gap-2 text-center">
                <span @class([
                    'flex h-8 w-8 items-center justify-center rounded-full border text-[11px] font-bold transition-colors',
                    'border-forest-600 bg-forest-600 text-white' => $state === 'done',
                    'border-clay-500 bg-clay-50 text-clay-700 ring-4 ring-clay-500/10' => $state === 'current',
                    'border-sand-300 bg-white text-ink-faint' => $state === 'todo' || $state === 'cancelled',
                ])>
                    @if ($state === 'done')
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                    @else
                        {{ $index + 1 }}
                    @endif
                </span>

                <span @class([
                    'text-[11px] font-semibold leading-tight',
                    'text-ink' => $state === 'current',
                    'text-ink-soft' => $state === 'done',
                    'text-ink-faint' => $state === 'todo' || $state === 'cancelled',
                ])>{{ $step->label() }}</span>
            </li>
        @endforeach
    </ol>

    <p class="mt-4 text-sm text-ink-muted">{{ $donation->status->description() }}</p>
</div>

@extends('layouts.app')

@section('title', 'My impact · Customyzables')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4" data-reveal>
            <div>
                <span class="eyebrow">
                    <span class="h-1.5 w-1.5 rounded-full bg-forest-500"></span>
                    {{ $user->isDonor() ? 'Donor record' : 'Upcycler record' }}
                </span>
                <h1 class="mt-3 text-3xl font-extrabold tracking-tight">Your impact</h1>
                <p class="mt-2 max-w-xl text-sm text-ink-muted">
                    {{ $user->isDonor()
                        ? 'Every kilogram here left your closet and went into someone’s work.'
                        : 'Textiles you have claimed and kept out of the waste stream.' }}
                </p>
            </div>

            <div class="text-right">
                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-ink-faint">Community total</p>
                <p class="text-2xl font-extrabold text-forest-700">{{ number_format($communityKg, 0) }} kg</p>
            </div>
        </div>

        <div class="mt-8 grid gap-5 sm:grid-cols-3" data-stagger>
            <x-stat-card label="Your textiles" :count-to="round($totalKg, 1)" :decimals="1" suffix=" kg" tone="forest" hint="Received or completed" />
            <x-stat-card label="Completed handovers" :count-to="$completed->count()" hint="Closed donations" />
            <x-stat-card label="Share of community" :count-to="$communityKg > 0 ? round(($totalKg / $communityKg) * 100, 1) : 0" :decimals="1" suffix="%" tone="clay" hint="Of all textiles moved" />
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1.4fr_1fr]">
            <section class="card p-6" data-reveal>
                <h2 class="text-sm font-bold">Month by month</h2>
                <p class="text-xs text-ink-muted">Kilograms received or completed, by month posted.</p>

                @if ($monthly->isEmpty())
                    <p class="mt-6 text-xs leading-relaxed text-ink-muted">
                        Nothing completed yet. Once a handover is marked received, it appears here.
                    </p>
                @else
                    @php $max = max(1, (float) $monthly->max('kg')); @endphp
                    <div class="mt-6 space-y-3">
                        @foreach ($monthly as $row)
                            <div>
                                <div class="flex items-center justify-between text-[11px] text-ink-muted">
                                    <span class="font-semibold">{{ \Illuminate\Support\Carbon::parse($row->month . '-01')->format('M Y') }}</span>
                                    <span>{{ rtrim(rtrim(number_format((float) $row->kg, 1), '0'), '.') }} kg</span>
                                </div>
                                <div class="mt-1 h-2 w-full overflow-hidden rounded-pill bg-sand-200">
                                    <div class="h-full rounded-pill bg-forest-500" data-bar="{{ round(((float) $row->kg / $max) * 100) }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="card overflow-hidden" data-reveal>
                <div class="border-b border-sand-200 px-5 py-4">
                    <h2 class="text-sm font-bold">Completed exchanges</h2>
                    <p class="text-xs text-ink-muted">{{ $completed->count() }} {{ Str::plural('donation', $completed->count()) }}</p>
                </div>

                @if ($completed->isEmpty())
                    <p class="px-5 py-6 text-xs leading-relaxed text-ink-muted">
                        Nothing here yet — this list fills up as handovers complete.
                    </p>
                @else
                    <ul class="divide-y divide-sand-200">
                        @foreach ($completed->take(6) as $donation)
                            <li class="flex items-center gap-3 px-5 py-3.5">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-forest-100 text-forest-700">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-bold">{{ $donation->title }}</p>
                                    <p class="text-[11px] text-ink-muted">{{ $donation->weightLabel() }} · {{ $donation->barangay }}</p>
                                </div>
                                <span class="shrink-0 text-[10px] uppercase tracking-wide text-ink-faint">{{ $donation->created_at->format('M j') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </div>
@endsection

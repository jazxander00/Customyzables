@extends('layouts.app')

@section('title', 'Customyzables — Give your clothes a second life in Lapu-Lapu')

@section('content')
    {{-- Hero: type-led. No gradient blob, no mocked "live" card — the feed below shows real ones. --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 sm:pt-20 lg:px-8">
        <div class="max-w-3xl">
            <span class="eyebrow" data-reveal>Lapu-Lapu circular hub</span>

            <h1 class="mt-6 text-balance font-display text-[2.7rem] font-medium leading-[1.02] tracking-[-0.03em] sm:text-6xl lg:text-[4.75rem]" data-reveal>
                Give your clothes a second life — with someone who will
                <em class="text-clay-700">actually use them.</em>
            </h1>

            <p class="mt-7 max-w-xl text-base leading-relaxed text-ink-muted" data-reveal>
                Customyzables connects households in Lapu-Lapu with local upcyclers who turn pre-loved fabric
                into bags, rugs, and new garments. Post what you have, see who wants it, and follow it all the
                way to its second life.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3" data-reveal>
                <a href="{{ route('register') }}" class="btn-primary px-5 py-3 text-[15px]" data-press>
                    Donate clothes
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
                <a href="{{ route('browse.index') }}" class="btn-secondary px-5 py-3 text-[15px]">
                    Browse donations
                </a>
            </div>

            <p class="mt-6 flex items-center gap-2 text-xs text-ink-muted" data-reveal>
                <svg class="h-3.5 w-3.5 text-forest-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                Free for donors and upcyclers. No fees, no shipping, everything stays local.
            </p>
        </div>
    </section>

    {{-- Impact strip --}}
    <section class="border-y border-sand-300">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 py-8 sm:px-6 lg:grid-cols-4 lg:px-8">
            @foreach ([
                ['label' => 'Textiles diverted', 'value' => number_format($stats['kg'], 0) . ' kg', 'count' => round($stats['kg']), 'suffix' => ' kg'],
                ['label' => 'Donations completed', 'value' => $stats['completed'], 'count' => $stats['completed'], 'suffix' => ''],
                ['label' => 'Verified upcyclers', 'value' => $stats['upcyclers'], 'count' => $stats['upcyclers'], 'suffix' => ''],
                ['label' => 'Barangays reached', 'value' => $stats['barangays'], 'count' => $stats['barangays'], 'suffix' => ''],
            ] as $item)
                <div data-reveal>
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-ink-faint">{{ $item['label'] }}</p>
                    <p class="mt-1 text-2xl font-extrabold tracking-tight text-ink"
                       data-count-to="{{ $item['count'] }}" data-count-suffix="{{ $item['suffix'] }}">
                        {{ $item['value'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <span class="eyebrow" data-reveal>How it works</span>
            <h2 class="mt-4 font-display text-3xl font-medium leading-[1.1] tracking-[-0.02em] sm:text-4xl" data-reveal>
                Two people, one bag of fabric, six honest steps.
            </h2>
            <p class="mt-3 text-base text-ink-muted" data-reveal>
                Every donation has a status you can see — no posting into a group chat and hoping someone replies.
            </p>
        </div>

        <div class="mt-12 grid gap-6 lg:grid-cols-2">
            <div class="card p-6" data-reveal>
                <div class="flex items-center gap-2">
                    <span class="badge badge-clay">For donors</span>
                    <span class="text-xs text-ink-faint">5 minutes to post</span>
                </div>

                <ol class="mt-5 space-y-4">
                    @foreach ([
                        ['Create your donation', 'Describe the fabric, add photos, and set a pickup or drop-off.'],
                        ['Review requests', 'Upcyclers explain what they will make. Accept the one that fits.'],
                        ['Agree on a handover', 'Pick a date together and chat inside the app.'],
                        ['See its second life', 'Mark it received, then completed — and keep the impact on your profile.'],
                    ] as $index => $step)
                        <li class="flex gap-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-clay-100 text-[11px] font-bold text-clay-700">{{ $index + 1 }}</span>
                            <div>
                                <p class="text-sm font-bold">{{ $step[0] }}</p>
                                <p class="mt-0.5 text-xs leading-relaxed text-ink-muted">{{ $step[1] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="card p-6" data-reveal>
                <div class="flex items-center gap-2">
                    <span class="badge badge-forest">For upcyclers</span>
                    <span class="text-xs text-ink-faint">Verified in a day</span>
                </div>

                <ol class="mt-5 space-y-4">
                    @foreach ([
                        ['Get verified', 'Tell us what you make. An admin confirms you are a real maker.'],
                        ['Browse the feed', 'Filter by fibre, weight, and barangay — Mactan is 1.2 km away.'],
                        ['Request a bundle', 'Say what you will build with it. Donors like specifics.'],
                        ['Collect and make', 'Schedule the handover, mark it received, and show the finished piece.'],
                    ] as $index => $step)
                        <li class="flex gap-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-forest-100 text-[11px] font-bold text-forest-700">{{ $index + 1 }}</span>
                            <div>
                                <p class="text-sm font-bold">{{ $step[0] }}</p>
                                <p class="mt-0.5 text-xs leading-relaxed text-ink-muted">{{ $step[1] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    {{-- Live feed preview --}}
    <section class="mx-auto max-w-7xl px-4 pb-8 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="font-display text-2xl font-medium tracking-[-0.02em] sm:text-3xl" data-reveal>Available right now</h2>
                <p class="mt-2 text-sm text-ink-muted" data-reveal>Textiles waiting for a maker in Lapu-Lapu City.</p>
            </div>
            <a href="{{ route('browse.index') }}" class="btn-secondary">See the full feed</a>
        </div>

        @if ($recent->isEmpty())
            <div class="mt-8">
                <x-empty-state
                    title="No donations listed yet"
                    description="Be the first: post a bag of fabric and an upcycler nearby will find it."
                    action="Post a donation"
                    :action-href="route('register')"
                />
            </div>
        @else
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
                @foreach ($recent as $donation)
                    <x-donation-card :donation="$donation" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- Closing CTA --}}
    <section class="mx-auto mt-16 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl border border-forest-200 bg-forest-600 p-8 sm:p-12" data-reveal>
            <div class="grid items-center gap-8 lg:grid-cols-[1.4fr_1fr]">
                <div>
                    <h2 class="font-display text-2xl font-medium leading-[1.15] tracking-[-0.02em] text-white sm:text-3xl">
                        2.7 million tonnes of textile waste a year. Lapu-Lapu can keep some of it.
                    </h2>
                    <p class="mt-3 max-w-xl text-sm leading-relaxed text-forest-50/90">
                        Every bundle you post stays in the city — given to a maker, turned into something new, and
                        tracked from listed to completed.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3 lg:justify-end">
                    <a href="{{ route('register') }}" class="btn bg-white px-5 py-3 text-forest-700 hover:bg-sand-100" data-press>
                        Join the hub
                    </a>
                    <a href="{{ route('browse.index') }}" class="btn border border-white/30 px-5 py-3 text-white hover:bg-white/10">
                        See what's available
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

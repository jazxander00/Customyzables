@extends('layouts.app')

@section('title', 'Donor dashboard · Customyzables')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div data-reveal>
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-ink-faint">{{ now()->format('l, F j') }}</p>
                <h1 class="mt-1.5 text-3xl font-extrabold tracking-tight">Good to see you, {{ Str::of($user->name)->explode(' ')->first() }}.</h1>
                <p class="mt-2 max-w-xl text-sm text-ink-muted">
                    Everything you have listed, everything waiting on your answer, and the weight you have kept out of landfill.
                </p>
            </div>

            <div class="flex gap-2" data-reveal>
                <a href="{{ route('donations.create') }}" class="btn-primary px-4 py-2.5" data-press>
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                    New donation
                </a>
                <a href="{{ route('donations.index') }}" class="btn-secondary">My donations</a>
            </div>
        </div>

        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4" data-stagger>
            <x-stat-card label="Open listings" :count-to="$stats['open']" hint="Listed, requested, or in handoff" />
            <x-stat-card label="Waiting on you" :count-to="$stats['waiting']" tone="clay" hint="Donations with new requests" />
            <x-stat-card label="Completed" :count-to="$stats['completed']" tone="forest" hint="Handed over and closed" />
            <x-stat-card label="Textiles given" :count-to="round($stats['kg'], 1)" :decimals="1" suffix=" kg" tone="forest" hint="Total weight moved to upcyclers" />
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1.5fr_1fr]">
            {{-- My donations --}}
            <section class="card overflow-hidden" data-reveal>
                <div class="flex items-center justify-between gap-3 border-b border-sand-200 px-5 py-4">
                    <div>
                        <h2 class="text-sm font-bold">Your latest donations</h2>
                        <p class="text-xs text-ink-muted">Newest first</p>
                    </div>
                    <a href="{{ route('donations.index') }}" class="text-xs font-semibold text-clay-700 hover:underline">View all</a>
                </div>

                @if ($donations->isEmpty())
                    <div class="p-5">
                        <x-empty-state
                            title="Nothing listed yet"
                            description="A single bag of fabric is enough to start. Upcyclers nearby check the feed daily."
                            action="Post your first donation"
                            :action-href="route('donations.create')"
                        />
                    </div>
                @else
                    <ul class="divide-y divide-sand-200">
                        @foreach ($donations as $donation)
                            <li class="flex items-center gap-4 px-5 py-4 transition hover:bg-sand-50/70">
                                <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-sand-200">
                                    @if ($donation->displayImage())
                                        <img src="{{ $donation->displayImage() }}" alt="" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-sand-200 to-clay-100">
                                            <svg class="h-5 w-5 text-sand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="5" width="18" height="14" rx="2.5"/><circle cx="9" cy="10" r="1.6"/><path d="m4 17 4.5-4.5L13 17l3-3 4 4"/></svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('donations.show', $donation) }}" class="truncate text-sm font-bold hover:text-clay-700">{{ $donation->title }}</a>
                                        <x-status-badge :status="$donation->status" />
                                        @if ($donation->pending_requests_count > 0)
                                            <span class="badge badge-clay">
                                                {{ $donation->pending_requests_count }} {{ Str::plural('request', $donation->pending_requests_count) }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-1 truncate text-xs text-ink-muted">
                                        {{ $donation->categoryLabel() }} · {{ $donation->weightLabel() }} · {{ $donation->barangay }}
                                        @if ($donation->acceptedRequest?->upcycler)
                                            · accepted by {{ $donation->acceptedRequest->upcycler->name }}
                                        @endif
                                    </p>
                                </div>

                                <a href="{{ route('donations.show', $donation) }}" class="btn-secondary btn-sm shrink-0">Open</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- Activity --}}
            <aside class="space-y-6">
                <section class="card p-5" data-reveal>
                    <h2 class="text-sm font-bold">Recent activity</h2>
                    <p class="text-xs text-ink-muted">Status changes across your donations</p>

                    @if ($activity->isEmpty())
                        <p class="mt-4 text-xs leading-relaxed text-ink-muted">
                            No activity yet. Once an upcycler requests something, it shows up here.
                        </p>
                    @else
                        <ol class="mt-4 space-y-4">
                            @foreach ($activity as $donation)
                                @foreach ($donation->events->take(2) as $event)
                                    <li class="flex gap-3">
                                        <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-clay-400"></span>
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold leading-snug">
                                                {{ \App\Enums\DonationStatus::from($event->to_status)->label() }}
                                                <span class="font-normal text-ink-faint">· {{ Str::limit($donation->title, 32) }}</span>
                                            </p>
                                            <p class="mt-0.5 text-[11px] text-ink-muted">{{ $event->note }}</p>
                                            <p class="mt-0.5 text-[10px] uppercase tracking-wide text-ink-faint">{{ $event->created_at->diffForHumans() }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            @endforeach
                        </ol>
                    @endif
                </section>

                <section class="card-flat p-5" data-reveal>
                    <h2 class="text-sm font-bold">Keep it moving</h2>
                    <p class="mt-1.5 text-xs leading-relaxed text-ink-muted">
                        Donations left in <span class="font-semibold text-ink-soft">Requested</span> for more than a week
                        tend to go cold. Answer within a day or two and both sides stay interested.
                    </p>
                    <a href="{{ route('donations.index', ['status' => 'requested']) }}" class="btn-secondary btn-sm mt-4">
                        Review pending requests
                    </a>
                </section>
            </aside>
        </div>
    </div>
@endsection

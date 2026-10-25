@extends('layouts.app')

@section('title', 'Upcycler dashboard · Customyzables')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div data-reveal>
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-ink-faint">
                    {{ $user->barangay ? $user->barangay . ' · ' : '' }}Lapu-Lapu City
                </p>
                <h1 class="mt-1.5 text-3xl font-extrabold tracking-tight">{{ $user->organization ?: Str::of($user->name)->explode(' ')->first() . '’s studio feed' }}</h1>
                <p class="mt-2 max-w-xl text-sm text-ink-muted">
                    Textile donations posted by households and businesses across the island, newest first.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2" data-reveal>
                @if ($user->is_verified)
                    <span class="badge badge-forest">
                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                        Verified upcycler
                    </span>
                @else
                    <span class="badge badge-amber">Verification pending</span>
                @endif
                <a href="{{ route('browse.index') }}" class="btn-primary" data-press>Browse the feed</a>
            </div>
        </div>

        @unless ($user->is_verified)
            <div class="mt-6 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50/70 px-4 py-3.5" data-reveal>
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>
                <div>
                    <p class="text-sm font-semibold text-ink">You can browse, but not request yet.</p>
                    <p class="mt-0.5 text-xs leading-relaxed text-ink-muted">
                        An administrator reviews new upcycler accounts — usually within a day. Adding a studio or group
                        name to your profile helps them verify you faster.
                    </p>
                </div>
            </div>
        @endunless

        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4" data-stagger>
            <x-stat-card label="Available now" :count-to="$stats['available']" hint="Bundles in the feed" />
            <x-stat-card label="Your open requests" :count-to="$stats['open']" tone="clay" hint="Waiting or accepted" />
            <x-stat-card label="Textiles claimed" :count-to="round($stats['kg'], 1)" :decimals="1" suffix=" kg" tone="forest" hint="Received and completed" />
            <x-stat-card label="Completed handovers" :count-to="$stats['completed']" tone="forest" hint="Pieces you actually collected" />
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1.55fr_1fr]">
            <section data-reveal>
                <div class="flex items-end justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-extrabold tracking-tight">Fresh in the feed</h2>
                        <p class="text-xs text-ink-muted">The newest six bundles across the island.</p>
                    </div>
                    <a href="{{ route('browse.index') }}" class="text-xs font-semibold text-clay-700 hover:underline">See all</a>
                </div>

                @if ($available->isEmpty())
                    <div class="mt-4">
                        <x-empty-state
                            title="The feed is quiet right now"
                            description="No open donations at the moment. New bundles usually appear in the evening."
                            action="Refresh the feed"
                            :action-href="route('dashboard.upcycler')"
                        />
                    </div>
                @else
                    <div class="mt-4 grid gap-5 sm:grid-cols-2" data-stagger>
                        @foreach ($available as $donation)
                            <x-donation-card :donation="$donation" />
                        @endforeach
                    </div>
                @endif
            </section>

            <aside class="space-y-6">
                <section class="card overflow-hidden" data-reveal>
                    <div class="flex items-center justify-between gap-3 border-b border-sand-200 px-5 py-4">
                        <div>
                            <h2 class="text-sm font-bold">Your requests</h2>
                            <p class="text-xs text-ink-muted">Newest first</p>
                        </div>
                        <a href="{{ route('requests.index') }}" class="text-xs font-semibold text-clay-700 hover:underline">All</a>
                    </div>

                    @if ($requests->isEmpty())
                        <p class="px-5 py-6 text-xs leading-relaxed text-ink-muted">
                            You have not requested anything yet. Open a donation, read the fabric details, and say what
                            you plan to make — donors respond better to specifics.
                        </p>
                    @else
                        <ul class="divide-y divide-sand-200">
                            @foreach ($requests as $request)
                                <li class="px-5 py-4">
                                    <div class="flex items-start justify-between gap-2">
                                        <a href="{{ route('browse.show', $request->donation) }}" class="text-xs font-bold leading-snug hover:text-clay-700">
                                            {{ Str::limit($request->donation->title, 46) }}
                                        </a>
                                        <span class="{{ $request->status->badge() }} shrink-0">{{ $request->status->label() }}</span>
                                    </div>
                                    <p class="mt-1 text-[11px] text-ink-muted">
                                        {{ $request->donation->donor->name }} · {{ $request->donation->barangay }}
                                        · {{ $request->created_at->diffForHumans(short: true) }}
                                    </p>

                                    @if ($request->isAccepted())
                                        <a href="{{ route('chat.show', $request->donation) }}" class="btn-secondary btn-sm mt-2.5 w-full">Message {{ Str::of($request->donation->donor->name)->explode(' ')->first() }}</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                <section class="card-flat p-5" data-reveal>
                    <h2 class="text-sm font-bold">What donors look for</h2>
                    <ul class="mt-3 space-y-2 text-xs leading-relaxed text-ink-muted">
                        <li class="flex gap-2"><span class="text-forest-600">·</span> A specific plan — “15 tote bags”, not “crafts”.</li>
                        <li class="flex gap-2"><span class="text-forest-600">·</span> How you will collect, and when.</li>
                        <li class="flex gap-2"><span class="text-forest-600">·</span> A link or photo of previous work, if you have one.</li>
                    </ul>
                </section>
            </aside>
        </div>
    </div>
@endsection

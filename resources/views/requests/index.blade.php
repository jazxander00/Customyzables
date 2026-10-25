@extends('layouts.app')

@section('title', 'My requests · Customyzables')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4" data-reveal>
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">My requests</h1>
                <p class="mt-2 text-sm text-ink-muted">Everything you have asked for, and what each donor decided.</p>
            </div>
            <a href="{{ route('browse.index') }}" class="btn-secondary">Find more bundles</a>
        </div>

        @if ($requests->isEmpty())
            <div class="mt-8">
                <x-empty-state
                    title="No requests yet"
                    description="Browse the feed and request a bundle. Say what you will make — donors respond to specifics."
                    action="Browse donations"
                    :action-href="route('browse.index')"
                />
            </div>
        @else
            <div class="mt-8 space-y-4" data-stagger>
                @foreach ($requests as $request)
                    <article class="card overflow-hidden">
                        <div class="flex flex-col gap-4 p-5 sm:flex-row">
                            <div class="h-24 w-full shrink-0 overflow-hidden rounded-xl bg-sand-200 sm:h-24 sm:w-32">
                                @if ($request->donation->displayImage())
                                    <img src="{{ $request->donation->displayImage() }}" alt="" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-sand-200 to-clay-100">
                                        <svg class="h-7 w-7 text-sand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="5" width="18" height="14" rx="2.5"/><circle cx="9" cy="10" r="1.6"/><path d="m4 17 4.5-4.5L13 17l3-3 4 4"/></svg>
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('browse.show', $request->donation) }}" class="text-sm font-bold hover:text-clay-700">
                                        {{ $request->donation->title }}
                                    </a>
                                    <span class="{{ $request->status->badge() }}">{{ $request->status->label() }}</span>
                                </div>

                                <p class="mt-1 text-xs text-ink-muted">
                                    {{ $request->donation->donor->name }} · {{ $request->donation->barangay }}
                                    · {{ $request->donation->weightLabel() }} · requested {{ $request->created_at->diffForHumans() }}
                                </p>

                                @if ($request->scheduled_at)
                                    <p class="mt-2 inline-flex items-center gap-1.5 rounded-lg bg-forest-50 px-2.5 py-1.5 text-[11px] font-semibold text-forest-700">
                                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 11h18"/></svg>
                                        Handover {{ $request->scheduled_at->format('M j, Y · g:i A') }}
                                    </p>
                                @endif

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <a href="{{ route('chat.show', $request->donation) }}" class="btn-secondary btn-sm">Open chat</a>

                                    @if ($request->isAccepted())
                                        <form method="POST" action="{{ route('requests.receive', $request) }}">
                                            @csrf
                                            <button type="submit" class="btn-forest btn-sm">I received the items</button>
                                        </form>
                                    @endif

                                    @if ($request->isPending())
                                        <form method="POST" action="{{ route('requests.withdraw', $request) }}">
                                            @csrf
                                            <button type="submit" class="btn-ghost btn-sm">Withdraw</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">{{ $requests->links() }}</div>
        @endif
    </div>
@endsection

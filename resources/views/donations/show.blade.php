@extends('layouts.app')

@section('title', $donation->title . ' · Your donation · Customyzables')

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <a href="{{ route('donations.index') }}" class="text-xs font-semibold text-ink-muted hover:text-ink">&larr; My donations</a>

        <div class="mt-3 flex flex-wrap items-start justify-between gap-4" data-reveal>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-status-badge :status="$donation->status" />
                    <span class="text-xs text-ink-faint">Posted {{ $donation->created_at->diffForHumans() }}</span>
                    <span class="text-xs text-ink-faint">· {{ $donation->views }} {{ Str::plural('view', $donation->views) }}</span>
                </div>
                <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $donation->title }}</h1>
                <p class="mt-1 text-sm text-ink-muted">
                    {{ $donation->categoryLabel() }} · {{ $donation->weightLabel() }} · {{ $donation->item_count }} {{ Str::plural('item', $donation->item_count) }} · {{ $donation->barangay }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('chat.show', $donation) }}" class="btn-secondary">Messages</a>

                @if ($donation->status === \App\Enums\DonationStatus::Received)
                    <form method="POST" action="{{ route('donations.complete', $donation) }}">
                        @csrf
                        <button type="submit" class="btn-forest">Mark completed</button>
                    </form>
                @endif

                @if ($donation->isOpen())
                    <form method="POST" action="{{ route('donations.cancel', $donation) }}"
                          onsubmit="return confirm('Withdraw this donation? Open requests will be cancelled.')">
                        @csrf
                        <button type="submit" class="btn-ghost">Withdraw</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1.55fr_1fr]">
            <div class="space-y-6">
                {{-- Progress --}}
                <section class="card p-6" data-reveal>
                    <h2 class="text-sm font-bold">Progress</h2>
                    <p class="text-xs text-ink-muted">The upcycler sees exactly the same stepper.</p>
                    <x-stepper :donation="$donation" class="mt-5" />
                </section>

                {{-- Requests --}}
                <section class="card overflow-hidden" data-reveal>
                    <div class="flex items-center justify-between gap-3 border-b border-sand-200 px-5 py-4">
                        <div>
                            <h2 class="text-sm font-bold">Requests</h2>
                            <p class="text-xs text-ink-muted">{{ $donation->requests->count() }} {{ Str::plural('upcycler', $donation->requests->count()) }} interested</p>
                        </div>
                        @if ($donation->pendingRequests()->count() > 0)
                            <span class="badge badge-clay">{{ $donation->pendingRequests()->count() }} waiting</span>
                        @endif
                    </div>

                    @if ($donation->requests->isEmpty())
                        <div class="px-5 py-8 text-center">
                            <p class="text-sm font-semibold">No requests yet</p>
                            <p class="mx-auto mt-1 max-w-sm text-xs leading-relaxed text-ink-muted">
                                Your donation is in the feed. Verified upcyclers in {{ $donation->barangay }} and nearby
                                barangays can request it — most requests arrive within a day or two.
                            </p>
                        </div>
                    @else
                        <ul class="divide-y divide-sand-200">
                            @foreach ($donation->requests->sortByDesc('created_at') as $request)
                                <li class="p-5">
                                    <div class="flex items-start gap-3">
                                        <x-avatar :user="$request->upcycler" class="h-10 w-10 shrink-0" />

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="text-sm font-bold">{{ $request->upcycler->name }}</p>
                                                <span class="{{ $request->status->badge() }}">{{ $request->status->label() }}</span>
                                                <span class="text-[11px] text-ink-faint">{{ $request->created_at->diffForHumans() }}</span>
                                            </div>

                                            <p class="text-xs text-ink-muted">
                                                {{ $request->upcycler->organization ?: 'Independent upcycler' }}
                                                @if ($request->upcycler->barangay) · {{ $request->upcycler->barangay }} @endif
                                                · {{ $request->upcycler->claimedWeight() }} kg claimed to date
                                            </p>

                                            @if ($request->message)
                                                <p class="mt-2.5 rounded-lg bg-sand-100 px-3 py-2.5 text-xs leading-relaxed text-ink-soft">
                                                    “{{ $request->message }}”
                                                </p>
                                            @endif

                                            @if ($request->isPending())
                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    <form method="POST" action="{{ route('requests.accept', $request) }}">
                                                        @csrf
                                                        <button type="submit" class="btn-forest btn-sm" data-press>Accept request</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('requests.decline', $request) }}">
                                                        @csrf
                                                        <button type="submit" class="btn-secondary btn-sm">Decline</button>
                                                    </form>
                                                    <a href="{{ route('chat.show', $donation) }}" class="btn-ghost btn-sm">Ask a question</a>
                                                </div>
                                            @endif

                                            @if ($request->isAccepted())
                                                <div class="mt-3 rounded-xl border border-forest-200 bg-forest-50/60 p-3.5">
                                                    @if ($request->scheduled_at)
                                                        <p class="text-xs font-semibold text-forest-700">
                                                            Handover set for {{ $request->scheduled_at->format('M j, Y · g:i A') }}
                                                        </p>
                                                        @if ($request->scheduled_note)
                                                            <p class="mt-0.5 text-[11px] text-ink-muted">{{ $request->scheduled_note }}</p>
                                                        @endif
                                                    @else
                                                        <form method="POST" action="{{ route('requests.schedule', $request) }}" class="space-y-2.5">
                                                            @csrf
                                                            <label for="scheduled_at" class="text-xs font-semibold text-ink-soft">Set a handover date and time</label>
                                                            <input id="scheduled_at" name="scheduled_at" type="datetime-local" required
                                                                   class="field" min="{{ now()->format('Y-m-d\TH:i') }}">
                                                            <input name="scheduled_note" type="text" class="field" placeholder="Landmark or note (optional)">
                                                            <button type="submit" class="btn-forest btn-sm w-full">Schedule handover</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @endif

                                            @if ($request->status === \App\Enums\RequestStatus::Completed)
                                                <p class="mt-2 text-[11px] font-semibold text-forest-700">
                                                    Collected {{ $request->completed_at?->diffForHumans() }} — mark the donation completed to close it.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                {{-- Details --}}
                <section class="card p-6" data-reveal>
                    <h2 class="text-sm font-bold">Listing details</h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-ink-soft">{{ $donation->description }}</p>

                    <dl class="mt-5 grid gap-4 border-t border-sand-200 pt-5 sm:grid-cols-3">
                        <div>
                            <dt class="text-[11px] font-bold uppercase tracking-[0.12em] text-ink-faint">Condition</dt>
                            <dd class="mt-0.5 text-sm font-semibold text-ink-soft">{{ $donation->conditionLabel() }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold uppercase tracking-[0.12em] text-ink-faint">Handover</dt>
                            <dd class="mt-0.5 text-sm font-semibold text-ink-soft">{{ $donation->handoff_method->short() }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold uppercase tracking-[0.12em] text-ink-faint">Available</dt>
                            <dd class="mt-0.5 text-sm font-semibold text-ink-soft">{{ $donation->available_from?->format('M j, Y') ?? 'Immediately' }}</dd>
                        </div>
                    </dl>

                    @if ($donation->pickup_address)
                        <p class="mt-4 text-xs text-ink-muted">
                            Pickup: <span class="font-semibold text-ink-soft">{{ $donation->pickup_address }}</span>
                        </p>
                    @endif
                </section>
            </div>

            <aside class="space-y-6">
                {{-- Photos --}}
                <section class="card overflow-hidden" data-reveal>
                    @if ($donation->displayImage())
                        <img src="{{ $donation->displayImage() }}" alt="{{ $donation->title }}" class="aspect-[4/3] w-full object-cover">
                        @if ($donation->images->count() > 1)
                            <div class="flex gap-2 overflow-x-auto p-3">
                                @foreach ($donation->images as $image)
                                    <img src="{{ $image->url() }}" alt="" class="h-14 w-16 shrink-0 rounded-lg object-cover">
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="flex aspect-[4/3] w-full items-center justify-center bg-gradient-to-br from-sand-200 to-clay-100">
                            <svg class="h-10 w-10 text-sand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="3" y="5" width="18" height="14" rx="2.5"/><circle cx="9" cy="10" r="1.6"/><path d="m4 17 4.5-4.5L13 17l3-3 4 4"/></svg>
                        </div>
                    @endif
                </section>

                {{-- Timeline --}}
                <section class="card p-5" data-reveal>
                    <h2 class="text-sm font-bold">Activity log</h2>
                    <ol class="mt-4 space-y-4">
                        @forelse ($donation->events as $event)
                            <li class="flex gap-3">
                                <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-clay-400"></span>
                                <div>
                                    <p class="text-xs font-semibold leading-snug">
                                        {{ \App\Enums\DonationStatus::from($event->to_status)->label() }}
                                    </p>
                                    <p class="mt-0.5 text-[11px] leading-relaxed text-ink-muted">{{ $event->note }}</p>
                                    <p class="mt-0.5 text-[10px] uppercase tracking-wide text-ink-faint">
                                        {{ $event->created_at->diffForHumans() }}
                                        @if ($event->actor) · {{ $event->actor->name }} @endif
                                    </p>
                                </div>
                            </li>
                        @empty
                            <li class="text-xs text-ink-muted">No activity recorded yet.</li>
                        @endforelse
                    </ol>
                </section>

                @if ($donation->status === \App\Enums\DonationStatus::Cancelled)
                    <form method="POST" action="{{ route('donations.destroy', $donation) }}"
                          onsubmit="return confirm('Delete this cancelled donation permanently?')" data-reveal>
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-secondary w-full text-xs">Delete permanently</button>
                    </form>
                @endif
            </aside>
        </div>
    </div>
@endsection

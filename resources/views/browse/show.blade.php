@extends('layouts.app')

@section('title', $donation->title . ' · Customyzables')

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-1.5 text-xs text-ink-muted" aria-label="Breadcrumb">
            <a href="{{ route('browse.index') }}" class="hover:text-ink">Feed</a>
            <span>/</span>
            <span class="truncate text-ink-soft">{{ $donation->title }}</span>
        </nav>

        <div class="mt-4 grid gap-6 lg:grid-cols-[1.5fr_1fr]">
            {{-- Left: photos + description --}}
            <div class="space-y-6">
                <div class="card overflow-hidden" data-reveal>
                    <div class="relative aspect-[16/10] bg-sand-200">
                        @if ($donation->displayImage())
                            <img src="{{ $donation->displayImage() }}" alt="{{ $donation->title }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-sand-200 via-sand-100 to-clay-100">
                                <svg class="h-14 w-14 text-sand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="5" width="18" height="14" rx="2.5"/><circle cx="9" cy="10" r="1.6"/><path d="m4 17 4.5-4.5L13 17l3-3 4 4"/>
                                </svg>
                            </div>
                        @endif

                        <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
                            <span class="badge bg-white/95 text-ink shadow-sm">{{ $donation->weightLabel() }}</span>
                            <span class="badge bg-white/95 text-ink-muted shadow-sm">{{ $donation->conditionLabel() }}</span>
                        </div>
                    </div>

                    @if ($donation->images->count() > 1)
                        <div class="flex gap-2 overflow-x-auto border-t border-sand-200 p-3">
                            @foreach ($donation->images as $image)
                                <img src="{{ $image->url() }}" alt="" class="h-16 w-20 shrink-0 rounded-lg object-cover">
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="card p-6" data-reveal>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-sand">{{ $donation->categoryLabel() }}</span>
                        <x-status-badge :status="$donation->status" />
                        <span class="text-xs text-ink-faint">Listed {{ $donation->created_at->diffForHumans() }}</span>
                    </div>

                    <h1 class="mt-3 text-2xl font-extrabold leading-tight tracking-tight sm:text-3xl">{{ $donation->title }}</h1>
                    <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-ink-soft">{{ $donation->description }}</p>

                    <dl class="mt-6 grid gap-4 border-t border-sand-200 pt-5 sm:grid-cols-2">
                        @foreach ([
                            ['label' => 'Quantity', 'value' => $donation->item_count . ' ' . Str::plural('item', $donation->item_count)],
                            ['label' => 'Weight', 'value' => $donation->weightLabel()],
                            ['label' => 'Barangay', 'value' => $donation->barangay],
                            ['label' => 'Handover', 'value' => $donation->handoff_method->short()],
                            ['label' => 'Condition', 'value' => $donation->conditionLabel()],
                            ['label' => 'Available from', 'value' => $donation->available_from?->format('M j, Y') ?? 'Immediately'],
                        ] as $row)
                            <div>
                                <dt class="text-[11px] font-bold uppercase tracking-[0.12em] text-ink-faint">{{ $row['label'] }}</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-ink-soft">{{ $row['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    @if ($donation->handoff_method === \App\Enums\HandoffMethod::Pickup && $donation->pickup_address)
                        <div class="mt-5 flex items-start gap-2 rounded-xl bg-sand-100 px-3.5 py-3">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-clay-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            <p class="text-xs leading-relaxed text-ink-muted">
                                Pickup area shown after a request is accepted: <span class="font-semibold text-ink-soft">{{ \Illuminate\Support\Str::limit($donation->pickup_address, 60) }}</span>
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Progress --}}
                <div class="card p-6" data-reveal>
                    <h2 class="text-sm font-bold">Donation progress</h2>
                    <p class="text-xs text-ink-muted">Visible to everyone, so nobody has to ask “what happened?”</p>
                    <x-stepper :donation="$donation" class="mt-5" />
                </div>
            </div>

            {{-- Right: donor + action --}}
            <aside class="space-y-4">
                <div class="card p-5" data-reveal>
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-ink-faint">Donated by</p>
                    <div class="mt-3 flex items-center gap-3">
                        <x-avatar :user="$donation->donor" class="h-11 w-11" />
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold">{{ $donation->donor->name }}</p>
                            <p class="text-xs text-ink-muted">
                                {{ $donation->donor->barangay ?? 'Lapu-Lapu City' }}
                                @if ($donation->donor->donations()->where('status', 'completed')->count() > 0)
                                    · {{ $donation->donor->donations()->where('status', 'completed')->count() }} completed
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Action panel --}}
                <div class="card p-5" data-reveal>
                    @guest
                        <h2 class="text-sm font-bold">Want this bundle?</h2>
                        <p class="mt-1.5 text-xs leading-relaxed text-ink-muted">
                            Upcycler accounts are verified by an admin so donors know their fabric goes to a real maker.
                        </p>
                        <a href="{{ route('register') }}" class="btn-primary mt-4 w-full">Join as an upcycler</a>
                        <a href="{{ route('login') }}" class="btn-ghost mt-1 w-full">I already have an account</a>
                    @endguest

                    @auth
                        @php $user = auth()->user(); @endphp

                        @if ($myRequest)
                            <div class="flex items-center justify-between gap-2">
                                <h2 class="text-sm font-bold">Your request</h2>
                                <span class="{{ $myRequest->status->badge() }}">{{ $myRequest->status->label() }}</span>
                            </div>
                            <p class="mt-2 text-xs leading-relaxed text-ink-muted">
                                Sent {{ $myRequest->created_at->diffForHumans() }}.
                                @if ($myRequest->isPending())
                                    The donor has not answered yet — you can chat to make your case.
                                @elseif ($myRequest->isAccepted())
                                    Accepted. Arrange the handover with the donor.
                                @endif
                            </p>

                            <a href="{{ route('chat.show', $donation) }}" class="btn-secondary mt-4 w-full">Open chat</a>

                            @if ($myRequest->isAccepted())
                                <form method="POST" action="{{ route('requests.receive', $myRequest) }}" class="mt-2">
                                    @csrf
                                    <button type="submit" class="btn-forest w-full">I have received the items</button>
                                </form>
                            @endif

                            @if ($myRequest->isPending())
                                <form method="POST" action="{{ route('requests.withdraw', $myRequest) }}" class="mt-2">
                                    @csrf
                                    <button type="submit" class="btn-ghost w-full text-xs">Withdraw request</button>
                                </form>
                            @endif
                        @elseif ($canRequest)
                            <h2 class="text-sm font-bold">Request this bundle</h2>
                            <p class="mt-1.5 text-xs leading-relaxed text-ink-muted">
                                Tell the donor what you will make with it. Specifics get accepted.
                            </p>

                            <form method="POST" action="{{ route('requests.store', $donation) }}" class="mt-4 space-y-3">
                                @csrf
                                <textarea name="message" rows="4" class="field" placeholder="e.g. I would use the denim for 15 tote bags for a school order. I can collect in Basak this weekend.">{{ old('message') }}</textarea>
                                @error('message')<p class="field-error-text">{{ $message }}</p>@enderror
                                @error('donation')<p class="field-error-text">{{ $message }}</p>@enderror
                                <button type="submit" class="btn-primary w-full" data-press>Send request</button>
                            </form>
                        @else
                            <h2 class="text-sm font-bold">Not available to you</h2>
                            <p class="mt-1.5 text-xs leading-relaxed text-ink-muted">
                                @if ($user->isDonor())
                                    You are signed in as a donor. Switch to an upcycler account to request bundles.
                                @elseif (! $user->is_verified)
                                    Your upcycler account is still pending verification. Browse while you wait — an admin will approve you shortly.
                                @elseif ($donation->status !== \App\Enums\DonationStatus::Listed)
                                    This bundle already has a request in progress. Keep an eye on the feed for the next one.
                                @else
                                    You have already interacted with this donation.
                                @endif
                            </p>
                        @endif
                    @endauth
                </div>

                <div class="card-flat p-5" data-reveal>
                    <h2 class="text-xs font-bold uppercase tracking-[0.12em] text-ink-faint">Safety</h2>
                    <p class="mt-2 text-xs leading-relaxed text-ink-muted">
                        Meet in public for pickups when you can, and keep the conversation in the app so there is a record
                        if something goes wrong. Admins can review any report.
                    </p>
                </div>
            </aside>
        </div>
    </div>
@endsection

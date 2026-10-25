@extends('layouts.app')

@section('title', 'Messages · Customyzables')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <div data-reveal>
            <h1 class="text-3xl font-extrabold tracking-tight">Messages</h1>
            <p class="mt-2 text-sm text-ink-muted">One conversation per donation, so the context never gets lost.</p>
        </div>

        @if ($donations->isEmpty())
            <div class="mt-8">
                <x-empty-state
                    title="No conversations yet"
                    description="Chats open automatically once there is a request between you and a donor or upcycler."
                    action="Browse donations"
                    :action-href="route('browse.index')"
                />
            </div>
        @else
            <div class="mt-8 space-y-3" data-stagger>
                @foreach ($donations as $donation)
                    @php $last = $donation->messages->first(); @endphp
                    <a href="{{ route('chat.show', $donation) }}" class="card flex items-center gap-4 p-4 transition hover:border-clay-300 hover:shadow-lift">
                        <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-sand-200">
                            @if ($donation->displayImage())
                                <img src="{{ $donation->displayImage() }}" alt="" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-sand-200 to-clay-100">
                                    <svg class="h-5 w-5 text-sand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="5" width="18" height="14" rx="2.5"/><circle cx="9" cy="10" r="1.6"/><path d="m4 17 4.5-4.5L13 17l3-3 4 4"/></svg>
                                </div>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="truncate text-sm font-bold">{{ $donation->title }}</p>
                                @if ($last)
                                    <span class="shrink-0 text-[11px] text-ink-faint">{{ $last->created_at->diffForHumans(short: true) }}</span>
                                @endif
                            </div>

                            <p class="mt-0.5 truncate text-xs text-ink-muted">
                                @if ($last)
                                    <span class="font-semibold text-ink-soft">{{ $last->sender->id === auth()->id() ? 'You' : $last->sender->name }}:</span>
                                    {{ $last->body }}
                                @else
                                    No messages yet — say hello.
                                @endif
                            </p>

                            <p class="mt-1 text-[11px] text-ink-faint">
                                {{ $donation->donor->id === auth()->id() ? 'You are the donor' : 'With ' . $donation->donor->name }}
                                · {{ $donation->status->label() }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $donations->links() }}</div>
        @endif
    </div>
@endsection

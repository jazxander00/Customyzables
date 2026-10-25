@extends('layouts.app')

@section('title', 'My donations · Customyzables')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div data-reveal>
                <h1 class="text-3xl font-extrabold tracking-tight">My donations</h1>
                <p class="mt-2 text-sm text-ink-muted">Everything you have posted, and where each bundle stands.</p>
            </div>
            <a href="{{ route('donations.create') }}" class="btn-primary" data-press>
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                New donation
            </a>
        </div>

        <div class="mt-6 flex flex-wrap gap-2" data-reveal>
            <a href="{{ route('donations.index') }}" @class(['chip', 'chip-active' => ! $status])>All</a>
            @foreach ($statuses as $value => $label)
                <a href="{{ route('donations.index', ['status' => $value]) }}" @class(['chip', 'chip-active' => $status === $value])>
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if ($donations->isEmpty())
            <div class="mt-6">
                <x-empty-state
                    title="No donations here yet"
                    description="Post a bag of fabric, an old uniform run, or a box of offcuts. Someone nearby is looking for exactly that."
                    action="Create your first donation"
                    :action-href="route('donations.create')"
                />
            </div>
        @else
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
                @foreach ($donations as $donation)
                    <div class="card overflow-hidden">
                        <a href="{{ route('donations.show', $donation) }}" class="block">
                            <div class="relative aspect-[16/10] bg-sand-200">
                                @if ($donation->displayImage())
                                    <img src="{{ $donation->displayImage() }}" alt="{{ $donation->title }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-sand-200 via-sand-100 to-clay-100">
                                        <svg class="h-9 w-9 text-sand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2.5"/><circle cx="9" cy="10" r="1.6"/><path d="m4 17 4.5-4.5L13 17l3-3 4 4"/></svg>
                                    </div>
                                @endif

                                <div class="absolute right-3 top-3">
                                    <x-status-badge :status="$donation->status" class="shadow-sm" />
                                </div>
                            </div>
                        </a>

                        <div class="p-4">
                            <div class="flex items-center justify-between text-[11px] text-ink-faint">
                                <span class="font-semibold uppercase tracking-[0.12em]">{{ $donation->categoryLabel() }}</span>
                                <span>{{ $donation->weightLabel() }}</span>
                            </div>

                            <h2 class="mt-1.5 line-clamp-2 text-sm font-bold leading-snug">
                                <a href="{{ route('donations.show', $donation) }}" class="hover:text-clay-700">{{ $donation->title }}</a>
                            </h2>

                            <p class="mt-1 text-xs text-ink-muted">
                                {{ $donation->barangay }} · {{ $donation->created_at->diffForHumans(short: true) }}
                            </p>

                            @if ($donation->pending_requests_count > 0)
                                <div class="mt-3 flex items-center justify-between rounded-lg bg-clay-50 px-3 py-2">
                                    <span class="text-[11px] font-semibold text-clay-700">
                                        {{ $donation->pending_requests_count }} pending {{ Str::plural('request', $donation->pending_requests_count) }}
                                    </span>
                                    <a href="{{ route('donations.show', $donation) }}" class="text-[11px] font-bold text-clay-700 hover:underline">Review</a>
                                </div>
                            @elseif ($donation->acceptedRequest?->upcycler)
                                <div class="mt-3 flex items-center gap-2 rounded-lg bg-forest-50 px-3 py-2">
                                    <x-avatar :user="$donation->acceptedRequest->upcycler" class="h-5 w-5" />
                                    <span class="truncate text-[11px] font-semibold text-forest-700">
                                        {{ $donation->acceptedRequest->upcycler->name }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">{{ $donations->links() }}</div>
        @endif
    </div>
@endsection

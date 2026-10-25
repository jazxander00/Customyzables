@extends('layouts.app')

@section('title', 'Browse donations · Customyzables')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Feed header --}}
        <div class="rounded-[1.5rem] border border-sand-300/80 bg-white p-6 sm:p-8" data-reveal>
            <div class="flex flex-wrap items-start justify-between gap-6">
                <div>
                    <span class="eyebrow">
                        <span class="h-1.5 w-1.5 rounded-full bg-forest-500"></span>
                        Lapu-Lapu City
                    </span>
                    <h1 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Upcycler studio feed</h1>
                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-ink-muted">
                        Discover available textile donations posted by households, schools, and businesses across Mactan Island.
                    </p>
                </div>

                <div class="rounded-xl bg-forest-50 px-4 py-3">
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-forest-700">Available bundles</p>
                    <p class="mt-1 text-2xl font-extrabold text-forest-700">{{ $counts['total'] }}</p>
                </div>
            </div>

            {{-- Search + filters --}}
            <form method="GET" action="{{ route('browse.index') }}" class="mt-6 space-y-4">
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-faint" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                        <input type="search" name="q" value="{{ $filters['q'] ?? '' }}"
                               class="field pl-10" placeholder="Search fabric or garment">
                    </div>

                    <select name="barangay" class="field sm:w-48" aria-label="Filter by barangay">
                        <option value="">All barangays</option>
                        @foreach ($barangays as $barangay)
                            <option value="{{ $barangay }}" @selected(($filters['barangay'] ?? '') === $barangay)>{{ $barangay }}</option>
                        @endforeach
                    </select>

                    <select name="condition" class="field sm:w-48" aria-label="Filter by condition">
                        <option value="">Any condition</option>
                        @foreach (\App\Models\Donation::CONDITIONS as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['condition'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn-primary sm:w-auto">Filter</button>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('browse.index', array_filter(['q' => $filters['q'] ?? null, 'barangay' => $filters['barangay'] ?? null, 'condition' => $filters['condition'] ?? null])) }}"
                       @class(['chip', 'chip-active' => empty($filters['category'])])>
                        All textiles ({{ $counts['total'] }})
                    </a>

                    @foreach ($categories as $key => $label)
                        <a href="{{ route('browse.index', array_filter(array_merge($filters, ['category' => $key]))) }}"
                           @class(['chip', 'chip-active' => ($filters['category'] ?? null) === $key])>
                            {{ $label }}@if (($counts['categories'][$key] ?? 0) > 0) <span class="opacity-70">({{ $counts['categories'][$key] }})</span>@endif
                        </a>
                    @endforeach

                    @if (array_filter($filters))
                        <a href="{{ route('browse.index') }}" class="ml-1 text-xs font-semibold text-clay-700 hover:underline">Clear all</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Results --}}
        <div class="mt-6 flex items-center justify-between">
            <p class="text-xs text-ink-muted">
                Showing {{ $donations->firstItem() ?? 0 }}–{{ $donations->lastItem() ?? 0 }} of {{ $donations->total() }}
                {{ Str::plural('bundle', $donations->total()) }}
            </p>

            @auth
                @if (auth()->user()->isUpcycler() && ! auth()->user()->is_verified)
                    <span class="badge badge-amber">Verify to request</span>
                @endif
            @endauth
        </div>

        @if ($donations->isEmpty())
            <div class="mt-4">
                <x-empty-state
                    title="Nothing matches those filters"
                    description="Try a wider barangay, a different fibre, or clear the filters entirely."
                    action="Clear filters"
                    :action-href="route('browse.index')"
                />
            </div>
        @else
            <div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" data-stagger>
                @foreach ($donations as $donation)
                    <x-donation-card :donation="$donation" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $donations->links() }}
            </div>
        @endif
    </div>
@endsection

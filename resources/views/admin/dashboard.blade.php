@extends('layouts.app')

@section('title', 'Admin dashboard · Customyzables')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4" data-reveal>
            <div>
                <span class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-clay-500"></span> Administration</span>
                <h1 class="mt-3 text-3xl font-extrabold tracking-tight">Platform overview</h1>
                <p class="mt-2 max-w-xl text-sm text-ink-muted">
                    Verifications, moderation, and the numbers that tell you whether the hub is healthy.
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.users') }}" class="btn-secondary">Users</a>
                <a href="{{ route('admin.audit') }}" class="btn-secondary">Audit trail</a>
            </div>
        </div>

        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4" data-stagger>
            <x-stat-card label="Donors" :count-to="$stats['donors']" hint="Registered households and orgs" />
            <x-stat-card label="Upcyclers" :count-to="$stats['upcyclers']" tone="clay" :hint="$stats['pendingUpcyclers'] . ' awaiting verification'" />
            <x-stat-card label="Live donations" :count-to="$stats['live']" tone="forest" :hint="$stats['donations'] . ' posted in total'" />
            <x-stat-card label="Textiles moved" :count-to="round($stats['kg'], 0)" suffix=" kg" tone="forest" :hint="$stats['openRequests'] . ' open requests'" />
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1.3fr_1fr]">
            <section class="card overflow-hidden" data-reveal>
                <div class="flex items-center justify-between gap-3 border-b border-sand-200 px-5 py-4">
                    <div>
                        <h2 class="text-sm font-bold">Upcyclers awaiting verification</h2>
                        <p class="text-xs text-ink-muted">They can browse but cannot request until approved</p>
                    </div>
                    <a href="{{ route('admin.users', ['role' => 'upcycler']) }}" class="text-xs font-semibold text-clay-700 hover:underline">All upcyclers</a>
                </div>

                @if ($pending->isEmpty())
                    <p class="px-5 py-8 text-center text-xs text-ink-muted">Nothing pending. Everyone is verified.</p>
                @else
                    <ul class="divide-y divide-sand-200">
                        @foreach ($pending as $user)
                            <li class="flex flex-wrap items-center gap-3 px-5 py-4">
                                <x-avatar :user="$user" class="h-9 w-9" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold">{{ $user->name }}</p>
                                    <p class="truncate text-xs text-ink-muted">
                                        {{ $user->organization ?: 'Independent' }} · {{ $user->barangay ?: 'Barangay not set' }}
                                        · joined {{ $user->created_at->diffForHumans(short: true) }}
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('admin.users.verify', $user) }}">
                                    @csrf
                                    <button type="submit" class="btn-forest btn-sm">Verify</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="card p-5" data-reveal>
                <h2 class="text-sm font-bold">Donations by fibre</h2>
                <p class="text-xs text-ink-muted">Where the material actually comes from</p>

                @if ($byCategory->isEmpty())
                    <p class="mt-4 text-xs text-ink-muted">No donations posted yet.</p>
                @else
                    @php $maxCat = max(1, (int) $byCategory->max('total')); @endphp
                    <div class="mt-4 space-y-3">
                        @foreach ($byCategory as $row)
                            <div>
                                <div class="flex items-center justify-between text-[11px] text-ink-muted">
                                    <span class="font-semibold">{{ \App\Models\Donation::CATEGORIES[$row->category] ?? $row->category }}</span>
                                    <span>{{ $row->total }}</span>
                                </div>
                                <div class="mt-1 h-1.5 w-full overflow-hidden rounded-pill bg-sand-200">
                                    <div class="h-full rounded-pill bg-clay-400" data-bar="{{ round(($row->total / $maxCat) * 100) }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <section class="card mt-6 overflow-hidden" data-reveal>
            <div class="flex items-center justify-between gap-3 border-b border-sand-200 px-5 py-4">
                <div>
                    <h2 class="text-sm font-bold">Recent admin activity</h2>
                    <p class="text-xs text-ink-muted">Every privileged action is logged</p>
                </div>
                <a href="{{ route('admin.audit') }}" class="text-xs font-semibold text-clay-700 hover:underline">Full trail</a>
            </div>

            @if ($recentLogs->isEmpty())
                <p class="px-5 py-6 text-xs text-ink-muted">No admin actions recorded yet.</p>
            @else
                <ul class="divide-y divide-sand-200">
                    @foreach ($recentLogs as $log)
                        <li class="flex items-center justify-between gap-3 px-5 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-xs font-semibold">
                                    <span class="font-mono text-[11px] text-clay-700">{{ $log->action }}</span>
                                    <span class="font-normal text-ink-muted"> by {{ $log->actor?->name ?? 'system' }}</span>
                                </p>
                                @if ($log->meta)
                                    <p class="truncate text-[11px] text-ink-faint">{{ collect($log->meta)->take(3)->map(fn ($v, $k) => "$k: $v")->implode(' · ') }}</p>
                                @endif
                            </div>
                            <span class="shrink-0 text-[10px] uppercase tracking-wide text-ink-faint">{{ $log->created_at->diffForHumans(short: true) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection

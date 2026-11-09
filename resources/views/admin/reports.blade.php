@extends('layouts.app')

@section('title', 'Reports · Admin · Customyzables')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4" data-reveal>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-ink-muted hover:text-ink">&larr; Admin dashboard</a>
                <h1 class="mt-2 text-3xl font-medium tracking-tight">Reports</h1>
                <p class="mt-2 max-w-xl text-sm text-ink-muted">
                    Summary, transaction history, and user activity for the selected period. Every figure comes live from the database.
                </p>
            </div>
            <a href="{{ route('admin.reports.export', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}"
               class="btn-primary" data-press>Export CSV</a>
        </div>

        <form method="GET" action="{{ route('admin.reports') }}" class="card mt-6 flex flex-wrap items-end gap-3 p-4" data-reveal>
            <div>
                <label for="from" class="field-label">From</label>
                <input id="from" name="from" type="date" value="{{ $from->toDateString() }}" class="field">
            </div>
            <div>
                <label for="to" class="field-label">To</label>
                <input id="to" name="to" type="date" value="{{ $to->toDateString() }}" class="field">
            </div>
            <button type="submit" class="btn-secondary">Apply</button>
            <a href="{{ route('admin.reports') }}" class="btn-ghost">Last 30 days</a>
        </form>

        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4" data-stagger>
            <x-stat-card label="Donations posted" :count-to="$summary['posted']" hint="New listings in this period" />
            <x-stat-card label="Requests received" :count-to="$summary['requests']" tone="clay" :hint="$summary['completionRate'] . '% completed'" />
            <x-stat-card label="Handovers done" :count-to="$summary['completed']" tone="forest" hint="Received or completed" />
            <x-stat-card label="Textiles posted" :count-to="round($summary['kg'])" suffix=" kg" tone="forest" hint="Total weight of new listings" />
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1.3fr_1fr]">
            <section class="card p-5" data-reveal>
                <h2 class="font-sans text-sm font-bold">Donations per month</h2>
                <p class="text-xs text-ink-muted">Volume posted, with total kilograms</p>

                @if ($monthly->isEmpty())
                    <p class="mt-4 text-xs text-ink-muted">No donations in this period.</p>
                @else
                    @php $maxMonth = max(1, (int) $monthly->max('total')); @endphp
                    <div class="mt-4 space-y-3">
                        @foreach ($monthly as $row)
                            <div>
                                <div class="flex items-center justify-between text-[11px] text-ink-muted">
                                    <span class="font-semibold">{{ \Illuminate\Support\Carbon::parse($row->month . '-01')->format('M Y') }}</span>
                                    <span>{{ $row->total }} donations · {{ number_format((float) $row->kg, 1) }} kg</span>
                                </div>
                                <div class="mt-1 h-1.5 w-full overflow-hidden rounded-pill bg-sand-200">
                                    <div class="h-full rounded-pill bg-clay-400" data-bar="{{ round(($row->total / $maxMonth) * 100) }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="card p-5" data-reveal>
                <h2 class="font-sans text-sm font-bold">Donations by fibre</h2>
                <p class="text-xs text-ink-muted">Where the material comes from</p>

                @if ($byCategory->isEmpty())
                    <p class="mt-4 text-xs text-ink-muted">No donations in this period.</p>
                @else
                    @php $maxCat = max(1, (int) $byCategory->max('total')); @endphp
                    <div class="mt-4 space-y-3">
                        @foreach ($byCategory as $row)
                            <div>
                                <div class="flex items-center justify-between text-[11px] text-ink-muted">
                                    <span class="font-semibold">{{ \App\Models\Donation::CATEGORIES[$row->category] ?? $row->category }}</span>
                                    <span>{{ $row->total }} · {{ number_format((float) $row->kg, 1) }} kg</span>
                                </div>
                                <div class="mt-1 h-1.5 w-full overflow-hidden rounded-pill bg-sand-200">
                                    <div class="h-full rounded-pill bg-forest-600" data-bar="{{ round(($row->total / $maxCat) * 100) }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <section class="card mt-6 overflow-hidden" data-reveal>
            <div class="border-b border-sand-200 px-5 py-4">
                <h2 class="font-sans text-sm font-bold">Transaction history</h2>
                <p class="text-xs text-ink-muted">Every request joined to its donation, donor, and upcycler</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-sand-100 text-[11px] uppercase tracking-[0.1em] text-ink-faint">
                        <tr>
                            <th class="px-5 py-3 font-bold">Date</th>
                            <th class="px-5 py-3 font-bold">Donation</th>
                            <th class="px-5 py-3 font-bold">Donor</th>
                            <th class="px-5 py-3 font-bold">Upcycler</th>
                            <th class="px-5 py-3 font-bold">Status</th>
                            <th class="px-5 py-3 text-right font-bold">Weight</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $row)
                            <tr class="table-row">
                                <td class="whitespace-nowrap px-5 py-3.5 text-xs text-ink-muted">{{ $row->created_at->format('M j, Y') }}</td>
                                <td class="px-5 py-3.5 text-sm font-semibold">{{ $row->donation_title }}</td>
                                <td class="px-5 py-3.5 text-xs text-ink-muted">{{ $row->donor_name }}</td>
                                <td class="px-5 py-3.5 text-xs text-ink-muted">{{ $row->upcycler_name }}</td>
                                <td class="px-5 py-3.5"><span class="badge {{ $row->status->badge() }}">{{ $row->status->label() }}</span></td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-right text-xs text-ink-muted">{{ number_format((float) $row->weight_kg, 1) }} kg</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-ink-muted">No transactions in this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="mt-4">{{ $transactions->links() }}</div>

        <section class="card mt-6 overflow-hidden" data-reveal>
            <div class="border-b border-sand-200 px-5 py-4">
                <h2 class="font-sans text-sm font-bold">User activity</h2>
                <p class="text-xs text-ink-muted">Privileged actions grouped by actor, including system events</p>
            </div>

            @if ($activity->isEmpty())
                <p class="px-5 py-8 text-center text-xs text-ink-muted">No activity in this period.</p>
            @else
                <table class="w-full text-left text-sm">
                    <thead class="bg-sand-100 text-[11px] uppercase tracking-[0.1em] text-ink-faint">
                        <tr>
                            <th class="px-5 py-3 font-bold">Actor</th>
                            <th class="px-5 py-3 font-bold">Actions</th>
                            <th class="px-5 py-3 text-right font-bold">Last action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activity as $row)
                            <tr class="table-row">
                                <td class="px-5 py-3.5 text-sm font-semibold">{{ $row->actor }}</td>
                                <td class="px-5 py-3.5 text-xs text-ink-muted">{{ $row->total }}</td>
                                <td class="px-5 py-3.5 text-right text-xs text-ink-muted">
                                    {{ \Illuminate\Support\Carbon::parse($row->last_at)->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </div>
@endsection

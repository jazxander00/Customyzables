@extends('layouts.app')

@section('title', 'Moderate donations · Admin · Customyzables')

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div data-reveal>
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-ink-muted hover:text-ink">&larr; Admin dashboard</a>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight">Donations</h1>
            <p class="mt-2 text-sm text-ink-muted">Review what is posted. Removing a donation writes an audit entry.</p>
        </div>

        <div class="mt-6 flex flex-wrap gap-2" data-reveal>
            <a href="{{ route('admin.donations') }}" @class(['chip', 'chip-active' => ! $status])>All</a>
            @foreach ($statuses as $value => $label)
                <a href="{{ route('admin.donations', ['status' => $value]) }}" @class(['chip', 'chip-active' => $status === $value])>{{ $label }}</a>
            @endforeach
        </div>

        <div class="card mt-6 overflow-hidden" data-reveal>
            <table class="w-full text-left text-sm">
                <thead class="bg-sand-100 text-[11px] uppercase tracking-[0.1em] text-ink-faint">
                    <tr>
                        <th class="px-5 py-3 font-bold">Donation</th>
                        <th class="px-5 py-3 font-bold">Donor</th>
                        <th class="px-5 py-3 font-bold">Status</th>
                        <th class="px-5 py-3 font-bold">Requests</th>
                        <th class="px-5 py-3 text-right font-bold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($donations as $donation)
                        <tr class="table-row">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-sand-200">
                                        @if ($donation->displayImage())
                                            <img src="{{ $donation->displayImage() }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full bg-gradient-to-br from-sand-200 to-clay-100"></div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold">{{ $donation->title }}</p>
                                        <p class="text-[11px] text-ink-faint">
                                            {{ $donation->categoryLabel() }} · {{ $donation->weightLabel() }} · {{ $donation->barangay }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-3.5 text-xs text-ink-muted">{{ $donation->donor->name }}</td>

                            <td class="px-5 py-3.5"><x-status-badge :status="$donation->status" /></td>

                            <td class="px-5 py-3.5 text-xs text-ink-muted">{{ $donation->requests_count }}</td>

                            <td class="px-5 py-3.5 text-right">
                                <form method="POST" action="{{ route('admin.donations.remove', $donation) }}"
                                      onsubmit="return confirm('Remove this donation permanently? This is logged.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost btn-sm">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-ink-muted">No donations in this view.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $donations->links() }}</div>
    </div>
@endsection

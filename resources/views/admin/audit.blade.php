@extends('layouts.app')

@section('title', 'Audit trail · Admin · Customyzables')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div data-reveal>
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-ink-muted hover:text-ink">&larr; Admin dashboard</a>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight">Audit trail</h1>
            <p class="mt-2 max-w-xl text-sm text-ink-muted">
                Every privileged action — verifications, suspensions, removals, and status changes — with the actor and timestamp.
            </p>
        </div>

        <div class="card mt-6 overflow-hidden" data-reveal>
            @if ($logs->isEmpty())
                <p class="px-5 py-10 text-center text-sm text-ink-muted">No entries yet.</p>
            @else
                <ul class="divide-y divide-sand-200">
                    @foreach ($logs as $log)
                        <li class="px-5 py-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="font-mono text-xs font-semibold text-clay-700">{{ $log->action }}</p>
                                <p class="text-[10px] uppercase tracking-wide text-ink-faint">{{ $log->created_at->format('M j, Y · g:i A') }}</p>
                            </div>

                            <p class="mt-1 text-xs text-ink-muted">
                                <span class="font-semibold text-ink-soft">{{ $log->actor?->name ?? 'System' }}</span>
                                @if ($log->subject_type)
                                    · {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                @endif
                                @if ($log->ip)
                                    · {{ $log->ip }}
                                @endif
                            </p>

                            @if ($log->meta)
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    @foreach ($log->meta as $key => $value)
                                        <span class="badge badge-sand">
                                            {{ $key }}: {{ is_array($value) ? json_encode($value) : $value }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="mt-6">{{ $logs->links() }}</div>
    </div>
@endsection

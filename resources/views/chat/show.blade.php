@extends('layouts.app')

@section('title', 'Chat · ' . $donation->title . ' · Customyzables')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <a href="{{ route('chat.index') }}" class="text-xs font-semibold text-ink-muted hover:text-ink">&larr; All messages</a>

        <div class="card mt-3 overflow-hidden" data-reveal>
            <div class="flex items-center gap-3 border-b border-sand-200 px-5 py-4">
                <x-avatar :user="$counterpart" class="h-10 w-10" />

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-bold">{{ $counterpart?->name ?? 'Conversation' }}</p>
                    <p class="truncate text-xs text-ink-muted">
                        {{ $donation->title }} · {{ $donation->status->label() }}
                    </p>
                </div>

                <a href="{{ route('browse.show', $donation) }}" class="btn-secondary btn-sm">View donation</a>
            </div>

            <div class="max-h-[28rem] space-y-3 overflow-y-auto bg-sand-50/60 px-5 py-5">
                @forelse ($messages as $message)
                    @php $mine = $message->sender_id === auth()->id(); @endphp

                    <div @class(['flex gap-2.5', 'justify-end' => $mine])>
                        @unless ($mine)
                            <x-avatar :user="$message->sender" class="h-7 w-7 shrink-0" />
                        @endunless

                        <div @class(['max-w-[75%] rounded-2xl px-3.5 py-2.5 text-sm leading-relaxed', 'bg-white text-ink-soft shadow-sm' => ! $mine, 'bg-clay-400 text-white' => $mine])>
                            <p class="whitespace-pre-line">{{ $message->body }}</p>
                        </div>
                    </div>

                    <p @class(['text-[10px] text-ink-faint', 'text-right' => $mine])>
                        {{ $mine ? 'You' : $message->sender->name }} · {{ $message->created_at->format('M j · g:i A') }}
                    </p>
                @empty
                    <div class="py-10 text-center">
                        <p class="text-sm font-semibold">No messages yet</p>
                        <p class="mx-auto mt-1 max-w-xs text-xs leading-relaxed text-ink-muted">
                            Introduce yourself and mention what you plan to make, or when you can collect.
                        </p>
                    </div>
                @endforelse
            </div>

            <form method="POST" action="{{ route('chat.store', $donation) }}" class="border-t border-sand-200 p-4">
                @csrf
                <label for="body" class="sr-only">Message</label>
                <div class="flex items-end gap-2">
                    <textarea id="body" name="body" rows="2" required maxlength="1000"
                              class="field resize-none" placeholder="Write a message…">{{ old('body') }}</textarea>
                    <button type="submit" class="btn-primary shrink-0 px-4 py-3" data-press aria-label="Send message">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m4 4 16 8-16 8 3-8-3-8Z"/></svg>
                    </button>
                </div>
                @error('body')<p class="field-error-text">{{ $message }}</p>@enderror
            </form>
        </div>
    </div>
@endsection

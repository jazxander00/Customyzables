@extends('layouts.guest')

@section('title', 'Page not found · Customyzables')

@section('content')
    <div class="card p-8 text-center sm:p-9">
        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-ink-faint">404</p>
        <h1 class="mt-3 text-2xl font-extrabold tracking-tight">This page has been upcycled</h1>
        <p class="mx-auto mt-2 max-w-sm text-sm leading-relaxed text-ink-muted">
            The link is broken or the listing is gone. Plenty of fabric is still waiting in the feed.
        </p>

        <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-center">
            <a href="{{ route('browse.index') }}" class="btn-primary">Browse donations</a>
            <a href="{{ route('home') }}" class="btn-ghost">Back home</a>
        </div>
    </div>
@endsection

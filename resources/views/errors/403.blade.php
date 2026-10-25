@extends('layouts.guest')

@section('title', 'Not allowed · Customyzables')

@section('content')
    <div class="card p-8 text-center sm:p-9">
        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-ink-faint">403</p>
        <h1 class="mt-3 text-2xl font-extrabold tracking-tight">That area isn't yours</h1>
        <p class="mx-auto mt-2 max-w-sm text-sm leading-relaxed text-ink-muted">
            @auth
                Your account is signed in as {{ auth()->user()->role->label() }}, so this page is off limits.
            @else
                Sign in with the right account to open this page.
            @endauth
        </p>

        <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-center">
            @auth
                <a href="{{ route(auth()->user()->role->dashboardRoute()) }}" class="btn-primary">Go to my dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-primary">Sign in</a>
            @endauth
            <a href="{{ route('home') }}" class="btn-ghost">Back home</a>
        </div>
    </div>
@endsection

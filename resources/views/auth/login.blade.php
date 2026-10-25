@extends('layouts.guest')

@section('title', 'Sign in · Customyzables')

@section('content')
    <div class="card p-7 sm:p-9">
        <div class="flex flex-col items-center text-center">
            <span class="eyebrow">
                <span class="h-1.5 w-1.5 rounded-full bg-forest-500"></span>
                Lapu-Lapu circular hub
            </span>

            <h1 class="mt-4 text-2xl font-extrabold tracking-tight sm:text-[1.7rem]">Welcome back</h1>
            <p class="mt-2 max-w-xs text-sm leading-relaxed text-ink-muted">
                Sign in to connect with Lapu-Lapu City's textile upcycling community.
            </p>
        </div>

        <div class="mt-6 flex items-start gap-2 rounded-xl border border-clay-200 bg-clay-50/70 px-3.5 py-3">
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-clay-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 8h.01M11 12h1v4h1"/></svg>
            <p class="text-xs leading-relaxed text-ink-soft">
                Donors and upcyclers use the same portal — your role decides what you see after signing in.
            </p>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="field-label">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       @class(['field', 'field-error' => $errors->has('email')])
                       placeholder="you@lapulapu.example">
                @error('email')
                    <p class="field-error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex items-baseline justify-between">
                    <label for="password" class="field-label">Password</label>
                    <span class="text-[11px] font-medium text-ink-faint">Forgot password? Ask an admin</span>
                </div>
                <div class="relative">
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                           @class(['field pr-11', 'field-error' => $errors->has('password')])
                           placeholder="••••••••••">
                    <button type="button" data-toggle-password="password"
                            class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-ink-faint hover:text-ink"
                            aria-label="Show password">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password')
                    <p class="field-error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="inline-flex items-center gap-2 text-sm text-ink-soft">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-sand-400 text-forest-600 focus:ring-forest-500/30">
                    Remember me
                </label>
                <span class="text-[11px] text-ink-faint">30-day session</span>
            </div>

            <button type="submit" class="btn-primary w-full py-3 text-[15px]" data-press>
                Sign in
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </button>
        </form>

        <div class="my-5 flex items-center gap-3">
            <span class="h-px flex-1 bg-sand-300"></span>
            <span class="text-[10px] font-semibold uppercase tracking-[0.14em] text-ink-faint">or continue with</span>
            <span class="h-px flex-1 bg-sand-300"></span>
        </div>

        <button type="button" disabled
                class="flex w-full cursor-not-allowed items-center justify-center gap-2.5 rounded-xl border border-sand-300 bg-white px-4 py-3 text-sm font-semibold text-ink-faint"
                title="Google sign-in is not enabled for this deployment yet">
            <svg class="h-4 w-4" viewBox="0 0 24 24"><path fill="#EA4335" d="M12 10.2v3.9h5.5c-.24 1.4-1.7 4.1-5.5 4.1-3.3 0-6-2.7-6-6.1s2.7-6.1 6-6.1c1.9 0 3.1.8 3.9 1.5l2.6-2.5C16.9 3.4 14.7 2.4 12 2.4 6.7 2.4 2.4 6.7 2.4 12s4.3 9.6 9.6 9.6c5.5 0 9.2-3.9 9.2-9.4 0-.7-.1-1.3-.2-1.9H12Z"/></svg>
            Continue with Google
        </button>

        <p class="mt-6 text-center text-sm text-ink-muted">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-semibold text-clay-700 hover:underline">Sign up</a>
        </p>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-toggle-password]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.togglePassword);
                const showing = input.type === 'text';
                input.type = showing ? 'password' : 'text';
                button.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            });
        });
    </script>
@endpush

@extends('layouts.guest')

@section('title', 'Create an account · Customyzables')

@section('content')
    <div class="card p-7 sm:p-9">
        <div class="flex flex-col items-center text-center">
            <span class="eyebrow">
                <span class="h-1.5 w-1.5 rounded-full bg-forest-500"></span>
                Join the hub
            </span>

            <h1 class="mt-4 text-2xl font-extrabold tracking-tight sm:text-[1.7rem]">Create your account</h1>
            <p class="mt-2 max-w-sm text-sm leading-relaxed text-ink-muted">
                One account, one role. You can always contact an admin if you need to change later.
            </p>
        </div>

        <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-5">
            @csrf

            <fieldset>
                <legend class="field-label">I am joining as…</legend>

                <div class="mt-2 grid gap-3 sm:grid-cols-2">
                    @foreach ($roles as $value => $label)
                        @php $checked = old('role', 'donor') === $value; @endphp
                        <label class="role-option">
                            <input type="radio" name="role" value="{{ $value }}" class="sr-only" @checked($checked)>
                            <span class="role-dot" aria-hidden="true"></span>
                            <span>
                                <span class="block text-sm font-bold">{{ $label }}</span>
                                <span class="mt-0.5 block text-[11px] leading-relaxed text-ink-muted">
                                    {{ $value === 'donor' ? 'I have pre-loved clothing to give.' : 'I turn donated textiles into new things.' }}
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('role')
                    <p class="field-error-text">{{ $message }}</p>
                @enderror
            </fieldset>

            <div>
                <label for="name" class="field-label">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name"
                       @class(['field', 'field-error' => $errors->has('name')]) placeholder="Maria S. Dela Cruz">
                @error('name')<p class="field-error-text">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="field-label">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                       @class(['field', 'field-error' => $errors->has('email')]) placeholder="username@email.com">
                @error('email')<p class="field-error-text">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="field-label">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                           @class(['field', 'field-error' => $errors->has('password')]) placeholder="At least 8 characters">
                    @error('password')<p class="field-error-text">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="field-label">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                           class="field" placeholder="Repeat it">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="barangay" class="field-label">Barangay</label>
                    <select id="barangay" name="barangay" class="field">
                        <option value="">Choose one</option>
                        @foreach (\App\Models\Donation::BARANGAYS as $barangay)
                            <option value="{{ $barangay }}" @selected(old('barangay') === $barangay)>{{ $barangay }}</option>
                        @endforeach
                    </select>
                    @error('barangay')<p class="field-error-text">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="field-label">Mobile number <span class="font-normal text-ink-faint">(optional)</span></label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}" autocomplete="tel"
                           class="field" placeholder="63+">
                    @error('phone')<p class="field-error-text">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="organization" class="field-label">
                    Studio or group <span class="font-normal text-ink-faint">(optional)</span>
                </label>
                <input id="organization" name="organization" type="text" value="{{ old('organization') }}"
                       class="field" placeholder="e.g. Mactan Crafts Collective">
                <p class="field-hint">Upcyclers who list a studio or group are easier for donors to trust.</p>
                @error('organization')<p class="field-error-text">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="btn-primary w-full py-3 text-[15px]" data-press>
                Create account
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </button>

            <p class="text-center text-[11px] leading-relaxed text-ink-faint">
                By joining you agree to keep exchanges local, honest, and free of charge.
            </p>
        </form>

        <p class="mt-5 text-center text-sm text-ink-muted">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-clay-700 hover:underline">Sign in</a>
        </p>
    </div>
@endsection

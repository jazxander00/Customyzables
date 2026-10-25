@extends('layouts.app')

@section('title', 'Profile settings · Customyzables')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <div data-reveal>
            <h1 class="text-3xl font-extrabold tracking-tight">Profile settings</h1>
            <p class="mt-2 text-sm text-ink-muted">Your role is {{ strtolower(auth()->user()->role->label()) }} — contact an admin to change it.</p>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1.5fr_1fr]">
            <form method="POST" action="{{ route('profile.update') }}" class="card p-6" data-reveal>
                @csrf
                @method('PATCH')

                <div class="flex items-center gap-4">
                    <x-avatar :user="auth()->user()" class="h-14 w-14" />
                    <div>
                        <p class="text-sm font-bold">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-ink-muted">
                            {{ auth()->user()->role->label() }}
                            @if (auth()->user()->is_verified) · <span class="font-semibold text-forest-700">Verified</span> @endif
                        </p>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <div>
                        <label for="name" class="field-label">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" required
                               @class(['field', 'field-error' => $errors->has('name')])>
                        @error('name')<p class="field-error-text">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="field-label">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required
                               @class(['field', 'field-error' => $errors->has('email')])>
                        @error('email')<p class="field-error-text">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="barangay" class="field-label">Barangay</label>
                            <select id="barangay" name="barangay" class="field">
                                <option value="">Not set</option>
                                @foreach (\App\Models\Donation::BARANGAYS as $barangay)
                                    <option value="{{ $barangay }}" @selected(old('barangay', auth()->user()->barangay) === $barangay)>{{ $barangay }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="phone" class="field-label">Mobile number</label>
                            <input id="phone" name="phone" type="text" value="{{ old('phone', auth()->user()->phone) }}" class="field">
                        </div>
                    </div>

                    <div>
                        <label for="organization" class="field-label">
                            Studio or group
                            @if (auth()->user()->isUpcycler())
                                <span class="font-normal text-ink-faint">(helps admins verify you)</span>
                            @endif
                        </label>
                        <input id="organization" name="organization" type="text" value="{{ old('organization', auth()->user()->organization) }}"
                               class="field" placeholder="e.g. Mactan Crafts Collective">
                    </div>

                    <div>
                        <label for="bio" class="field-label">Short bio</label>
                        <textarea id="bio" name="bio" rows="3" class="field" placeholder="What you make, or what you usually donate.">{{ old('bio', auth()->user()->bio) }}</textarea>
                    </div>

                    <button type="submit" class="btn-primary" data-press>Save profile</button>
                </div>
            </form>

            <aside class="space-y-6">
                <form method="POST" action="{{ route('profile.password') }}" class="card p-6" data-reveal>
                    @csrf
                    @method('PUT')

                    <h2 class="text-sm font-bold">Change password</h2>

                    <div class="mt-4 space-y-3">
                        <div>
                            <label for="current_password" class="field-label">Current password</label>
                            <input id="current_password" name="current_password" type="password" required
                                   @class(['field', 'field-error' => $errors->has('current_password')])>
                            @error('current_password')<p class="field-error-text">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="password" class="field-label">New password</label>
                            <input id="password" name="password" type="password" required
                                   @class(['field', 'field-error' => $errors->has('password')])>
                            @error('password')<p class="field-error-text">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="field-label">Confirm new password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="field">
                        </div>

                        <button type="submit" class="btn-secondary w-full">Update password</button>
                    </div>
                </form>

                <div class="card-flat p-5" data-reveal>
                    <h2 class="text-sm font-bold">Your impact</h2>
                    <p class="mt-1.5 text-xs leading-relaxed text-ink-muted">
                        @if (auth()->user()->isDonor())
                            {{ round(auth()->user()->donatedWeight(), 1) }} kg handed over so far.
                        @else
                            {{ round(auth()->user()->claimedWeight(), 1) }} kg of textiles claimed so far.
                        @endif
                    </p>
                    <a href="{{ route('profile.impact') }}" class="btn-secondary btn-sm mt-3">See the breakdown</a>
                </div>
            </aside>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Create a donation · Customyzables')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-3" data-reveal>
            <div>
                <a href="{{ route('donations.index') }}" class="text-xs font-semibold text-ink-muted hover:text-ink">&larr; My donations</a>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight">Post a donation</h1>
                <p class="mt-2 max-w-xl text-sm text-ink-muted">
                    Three short steps. The more honest the condition description, the better the requests you get.
                </p>
            </div>

            <div class="hidden gap-1.5 sm:flex" aria-hidden="true">
                @foreach ([1, 2, 3] as $step)
                    <span class="flex h-7 w-7 items-center justify-center rounded-full {{ $step === 1 ? 'bg-clay-500 text-white' : 'bg-sand-200 text-ink-faint' }} text-[11px] font-bold">{{ $step }}</span>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ route('donations.store') }}" enctype="multipart/form-data" class="mt-8 grid gap-6 lg:grid-cols-[1.6fr_1fr]">
            @csrf

            <div class="space-y-6">
                {{-- Step 1 --}}
                <section class="card p-6" data-reveal>
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-clay-100 text-[11px] font-bold text-clay-700">1</span>
                        <h2 class="text-sm font-bold">What are you giving?</h2>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div>
                            <label for="title" class="field-label">Listing title</label>
                            <input id="title" name="title" type="text" value="{{ old('title') }}" required
                                   @class(['field', 'field-error' => $errors->has('title')])
                                   placeholder="e.g. Pre-loved denim jackets &amp; jeans (5 pcs)">
                            @error('title')<p class="field-error-text">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="description" class="field-label">Description</label>
                            <textarea id="description" name="description" rows="5" required
                                      @class(['field', 'field-error' => $errors->has('description')])
                                      placeholder="Fibre, colour, size range, flaws, and what it could become. Mention if it has been washed or altered.">{{ old('description') }}</textarea>
                            <p class="field-hint">Upcyclers use this to plan a project — vague listings get fewer requests.</p>
                            @error('description')<p class="field-error-text">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="category" class="field-label">Main fibre</label>
                                <select id="category" name="category" class="field">
                                    <option value="">Select a category</option>
                                    @foreach ($categories as $value => $label)
                                        <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category')<p class="field-error-text">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="condition" class="field-label">Condition</label>
                                <select id="condition" name="condition" class="field">
                                    <option value="">Select condition</option>
                                    @foreach ($conditions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('condition') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('condition')<p class="field-error-text">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Step 2 --}}
                <section class="card p-6" data-reveal>
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-clay-100 text-[11px] font-bold text-clay-700">2</span>
                        <h2 class="text-sm font-bold">How much, and what does it look like?</h2>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="item_count" class="field-label">Number of items</label>
                                <input id="item_count" name="item_count" type="number" min="1" max="500" value="{{ old('item_count', 1) }}" required
                                       @class(['field', 'field-error' => $errors->has('item_count')])>
                                @error('item_count')<p class="field-error-text">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="weight_kg" class="field-label">Total weight (kg)</label>
                                <input id="weight_kg" name="weight_kg" type="number" step="0.1" min="0.1" max="500" value="{{ old('weight_kg') }}" required
                                       @class(['field', 'field-error' => $errors->has('weight_kg')]) placeholder="e.g. 4.8">
                                @error('weight_kg')<p class="field-error-text">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="images" class="field-label">Photos <span class="font-normal text-ink-faint">(up to 4, optional)</span></label>
                            <input id="images" name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple
                                   class="field file:mr-3 file:rounded-lg file:border-0 file:bg-sand-200 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-ink-soft">
                            <p class="field-hint">JPG, PNG, or WebP · up to 4 MB each. A photo of the pile is enough.</p>
                            @error('images.*')<p class="field-error-text">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                {{-- Step 3 --}}
                <section class="card p-6" data-reveal>
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-clay-100 text-[11px] font-bold text-clay-700">3</span>
                        <h2 class="text-sm font-bold">Where and when can they collect?</h2>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="barangay" class="field-label">Barangay</label>
                                <select id="barangay" name="barangay" class="field">
                                    <option value="">Select barangay</option>
                                    @foreach ($barangays as $barangay)
                                        <option value="{{ $barangay }}" @selected(old('barangay') === $barangay)>{{ $barangay }}</option>
                                    @endforeach
                                </select>
                                @error('barangay')<p class="field-error-text">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="available_from" class="field-label">Available from <span class="font-normal text-ink-faint">(optional)</span></label>
                                <input id="available_from" name="available_from" type="date" value="{{ old('available_from') }}" class="field">
                                @error('available_from')<p class="field-error-text">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <fieldset>
                            <legend class="field-label">Handover method</legend>
                            <div class="mt-1 grid gap-3 sm:grid-cols-2">
                                @foreach (\App\Enums\HandoffMethod::cases() as $method)
                                    <label class="role-option">
                                        <input type="radio" name="handoff_method" value="{{ $method->value }}"
                                               class="sr-only" @checked(old('handoff_method', 'pickup') === $method->value)>
                                        <span class="role-dot" aria-hidden="true"></span>
                                        <span>
                                            <span class="block text-sm font-bold">{{ $method->label() }}</span>
                                            <span class="mt-0.5 block text-[11px] text-ink-muted">
                                                {{ $method === \App\Enums\HandoffMethod::Pickup ? 'The upcycler comes to you.' : 'Bring it to a partner site in Lapu-Lapu.' }}
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('handoff_method')<p class="field-error-text">{{ $message }}</p>@enderror
                        </fieldset>

                        <div>
                            <label for="pickup_address" class="field-label">Pickup address <span class="font-normal text-ink-faint">(pickup only)</span></label>
                            <input id="pickup_address" name="pickup_address" type="text" value="{{ old('pickup_address') }}"
                                   @class(['field', 'field-error' => $errors->has('pickup_address')])
                                   placeholder="Street, landmark, or meeting point">
                            <p class="field-hint">Only shown to the upcycler whose request you accept.</p>
                            @error('pickup_address')<p class="field-error-text">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>
            </div>

            {{-- Summary --}}
            <aside class="space-y-4 lg:sticky lg:top-24 lg:self-start">
                <div class="card p-5" data-reveal>
                    <h2 class="text-sm font-bold">Before you publish</h2>
                    <ul class="mt-3 space-y-2.5 text-xs leading-relaxed text-ink-muted">
                        <li class="flex gap-2"><span class="text-forest-600">✓</span> Photos show the real condition, not a stock image.</li>
                        <li class="flex gap-2"><span class="text-forest-600">✓</span> The weight is close — upcyclers plan by kilograms.</li>
                        <li class="flex gap-2"><span class="text-forest-600">✓</span> No money is involved anywhere in the exchange.</li>
                    </ul>

                    <div class="divider my-4"></div>

                    <button type="submit" class="btn-primary w-full py-3" data-press>
                        Publish donation
                    </button>
                    <a href="{{ route('donations.index') }}" class="btn-ghost mt-2 w-full">Cancel</a>
                </div>

                <div class="card-flat p-5" data-reveal>
                    <p class="text-xs font-bold text-ink-soft">What happens next</p>
                    <p class="mt-2 text-xs leading-relaxed text-ink-muted">
                        Your listing appears in the feed immediately. When an upcycler requests it, you will see the request
                        on your dashboard and can accept, decline, or ask a question first.
                    </p>
                </div>
            </aside>
        </form>
    </div>
@endsection

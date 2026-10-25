@props(['user', 'class' => 'h-9 w-9'])

@if ($user?->avatar_path)
    <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" {{ $attributes->merge(['class' => $class . ' rounded-full object-cover']) }}>
@else
    <span {{ $attributes->merge(['class' => $class . ' inline-flex items-center justify-center rounded-full bg-forest-100 text-[11px] font-bold text-forest-700']) }} aria-hidden="true">
        {{ $user?->initials() ?? '?' }}
    </span>
@endif

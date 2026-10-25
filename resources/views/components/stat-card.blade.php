@props(['label', 'value', 'hint' => null, 'tone' => 'default', 'countTo' => null, 'decimals' => 0, 'suffix' => ''])

@php
    $tones = [
        'default' => 'text-ink',
        'clay' => 'text-clay-600',
        'forest' => 'text-forest-700',
    ];
    $barTones = [
        'default' => 'bg-ink/80',
        'clay' => 'bg-clay-400',
        'forest' => 'bg-forest-600',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'stat-card']) }} data-reveal>
    <div class="flex items-start justify-between gap-3">
        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-ink-faint">{{ $label }}</p>
        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-sand-200 text-ink-faint">
            {{ $icon ?? '' }}
        </span>
    </div>

    <p @class(['stat-value', $tones[$tone] ?? $tones['default']])
       @if ($countTo !== null) data-count-to="{{ $countTo }}" data-count-decimals="{{ $decimals }}" data-count-suffix="{{ $suffix }}" @endif>
        {{ $countTo !== null ? number_format((float) $countTo, $decimals) . $suffix : $value }}
    </p>

    @if ($hint)
        <p class="mt-1 text-xs text-ink-muted">{{ $hint }}</p>
    @endif

    <div class="mt-4 h-1 w-full overflow-hidden rounded-pill bg-sand-200">
        <div class="h-full rounded-pill {{ $barTones[$tone] ?? $barTones['default'] }}" data-bar="100"></div>
    </div>
</div>

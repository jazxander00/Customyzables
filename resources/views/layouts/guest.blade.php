<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Customyzables')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        document.documentElement.classList.add('js');
    </script>
</head>
<body class="flex min-h-screen flex-col items-center justify-center px-4 py-10">

    <a href="{{ route('home') }}" class="mb-7 flex items-center gap-2.5" aria-label="Back to Customyzables home">
        <x-logo class="h-10 w-10" />
        <span class="text-base font-extrabold tracking-tight">Customyzables</span>
    </a>

    <div class="w-full max-w-md" data-reveal>
        @yield('content')
    </div>

    <div class="mt-8 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-[11px] text-ink-faint">
        <span class="inline-flex items-center gap-1.5">
            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
            Lapu-Lapu Circular Hub
        </span>
        <span class="hidden sm:inline">&middot;</span>
        <span>Punta Engaño drop-off</span>
        <span class="hidden sm:inline">&middot;</span>
        <a href="{{ route('browse.index') }}" class="hover:text-ink">Browse before joining</a>
    </div>

    @stack('scripts')
</body>
</html>

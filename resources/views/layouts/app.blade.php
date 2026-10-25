<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Customyzables connects clothing donors with local upcyclers in Lapu-Lapu City.">

    <title>@yield('title', 'Customyzables — Lapu-Lapu Circular Textiles')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Flag JS early so reveal animations can hide elements without risking invisible content.
        document.documentElement.classList.add('js');
    </script>
</head>
<body class="min-h-screen antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:shadow-lift">
        Skip to content
    </a>

    <header class="sticky top-0 z-40 border-b border-sand-300/70 bg-sand-100/85 backdrop-blur-md">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="Customyzables home">
                    <x-logo class="h-9 w-9" />
                    <span class="text-[15px] font-extrabold tracking-tight text-ink">Customyzables</span>
                </a>

                @auth
                    <nav class="hidden items-center gap-1 md:flex" aria-label="Main">
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
                            <a href="{{ route('admin.users') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('admin.users')])>Users</a>
                            <a href="{{ route('admin.donations') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('admin.donations')])>Donations</a>
                            <a href="{{ route('admin.audit') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('admin.audit')])>Audit trail</a>
                        @else
                            <a href="{{ route(auth()->user()->role->dashboardRoute()) }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('dashboard.*')])>Dashboard</a>
                            <a href="{{ route('browse.index') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('browse.*')])>Browse</a>
                            @if (auth()->user()->isDonor())
                                <a href="{{ route('donations.index') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('donations.*')])>My donations</a>
                            @else
                                <a href="{{ route('requests.index') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('requests.*')])>My requests</a>
                            @endif
                            <a href="{{ route('chat.index') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('chat.*')])>Messages</a>
                            <a href="{{ route('profile.impact') }}" @class(['nav-link hidden lg:inline-flex', 'nav-link-active' => request()->routeIs('profile.impact')])>Impact</a>
                        @endif
                    </nav>
                @else
                    <nav class="hidden items-center gap-1 md:flex" aria-label="Main">
                        <a href="{{ route('browse.index') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('browse.*')])>Browse donations</a>
                        <a href="{{ route('home') }}#how-it-works" class="nav-link">How it works</a>
                    </nav>
                @endauth
            </div>

            <div class="flex items-center gap-2">
                @auth
                    @if (auth()->user()->isDonor())
                        <a href="{{ route('donations.create') }}" class="btn-primary hidden lg:inline-flex" data-press>
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                            Create donation
                        </a>
                    @endif

                    <div class="relative" data-menu>
                        <button type="button" data-menu-trigger class="flex items-center gap-2 rounded-pill border border-sand-300 bg-white py-1 pl-1 pr-3 transition hover:border-clay-300" aria-expanded="false" aria-haspopup="true">
                            <x-avatar :user="auth()->user()" class="h-8 w-8" />
                            <span class="hidden text-xs font-semibold text-ink-soft sm:block">{{ Str::limit(auth()->user()->name, 16) }}</span>
                        </button>

                        <div data-menu-panel class="absolute right-0 mt-2 hidden w-56 overflow-hidden rounded-xl border border-sand-300 bg-white p-1.5 shadow-lift">
                            <div class="px-3 py-2">
                                <p class="text-xs font-semibold text-ink">{{ auth()->user()->name }}</p>
                                <p class="truncate text-[11px] text-ink-faint">{{ auth()->user()->email }}</p>
                                <p class="mt-1 text-[11px] font-semibold text-clay-600">{{ auth()->user()->role->label() }}</p>
                            </div>
                            <div class="divider my-1"></div>
                            <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm text-ink-soft hover:bg-sand-200">Profile settings</a>
                            <a href="{{ route('profile.impact') }}" class="block rounded-lg px-3 py-2 text-sm text-ink-soft hover:bg-sand-200">My impact</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-clay-700 hover:bg-clay-50">Sign out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost">Sign in</a>
                    <a href="{{ route('register') }}" class="btn-primary">Join the hub</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Flash messages --}}
    <div class="pointer-events-none fixed inset-x-0 top-20 z-50 mx-auto flex max-w-md flex-col gap-2 px-4" aria-live="polite">
        @if (session('status'))
            <div data-flash class="pointer-events-auto flex items-start gap-2.5 rounded-xl border border-forest-200 bg-white px-4 py-3 shadow-lift">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-forest-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                <p class="text-sm font-medium text-ink-soft">{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->any() && ! request()->routeIs('login', 'register'))
            <div data-flash class="pointer-events-auto flex items-start gap-2.5 rounded-xl border border-clay-200 bg-white px-4 py-3 shadow-lift">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-clay-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
                <div class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm font-medium text-ink-soft">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <main id="main">
        @yield('content')
    </main>

    <footer class="mt-24 border-t border-sand-300/80 bg-sand-50/60">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-4 lg:px-8">
            <div>
                <div class="flex items-center gap-2.5">
                    <x-logo class="h-8 w-8" />
                    <span class="text-sm font-extrabold tracking-tight">Customyzables</span>
                </div>
                <p class="mt-3 max-w-xs text-sm leading-relaxed text-ink-muted">
                    Fostering circular fashion and low-waste living across Lapu-Lapu City, Cebu.
                </p>
                <p class="mt-3 text-xs text-ink-faint">Mactan Island, Philippines</p>
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-[0.14em] text-ink-faint">Lapu-Lapu barangays</h3>
                <ul class="mt-3 space-y-2 text-sm text-ink-muted">
                    <li>Pajo &amp; Poblacion collection</li>
                    <li>Basak Upcycling Hub</li>
                    <li>Gun-ob drop-off station</li>
                    <li>Marigondon crafters cluster</li>
                </ul>
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-[0.14em] text-ink-faint">Community &amp; resources</h3>
                <ul class="mt-3 space-y-2 text-sm text-ink-muted">
                    <li><a href="{{ route('browse.index') }}" class="hover:text-ink">Browse donations</a></li>
                    <li><a href="{{ route('home') }}#how-it-works" class="hover:text-ink">How it works</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-ink">Become an upcycler</a></li>
                </ul>
            </div>

            <div class="rounded-card border border-sand-300 bg-white p-5">
                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-ink-faint">Community impact</p>
                <p class="mt-2 text-3xl font-extrabold tracking-tight text-forest-700">
                    {{ number_format((float) \App\Models\Donation::whereIn('status', ['received','completed'])->sum('weight_kg'), 0) }} kg
                </p>
                <p class="mt-1 text-xs text-ink-muted">Textiles handed over to upcyclers through the hub.</p>
            </div>
        </div>

        <div class="border-t border-sand-300/70">
            <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-5 text-xs text-ink-faint sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p>&copy; {{ date('Y') }} Customyzables · Lapu-Lapu City. A University of Cebu CCS project.</p>
                <p>Team Tynamo · Elaine Ena B. Villaluna &amp; Johnriel Dave L. Legara</p>
            </div>
        </div>
    </footer>

    <script>
        // Dropdown menu — small and dependency-free.
        document.querySelectorAll('[data-menu]').forEach((menu) => {
            const trigger = menu.querySelector('[data-menu-trigger]');
            const panel = menu.querySelector('[data-menu-panel]');

            trigger?.addEventListener('click', () => {
                const open = panel.classList.toggle('hidden');
                trigger.setAttribute('aria-expanded', String(!open));
            });

            document.addEventListener('click', (event) => {
                if (!menu.contains(event.target)) {
                    panel.classList.add('hidden');
                    trigger?.setAttribute('aria-expanded', 'false');
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    panel.classList.add('hidden');
                    trigger?.setAttribute('aria-expanded', 'false');
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>

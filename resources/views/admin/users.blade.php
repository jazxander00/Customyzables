@extends('layouts.app')

@section('title', 'Users · Admin · Customyzables')

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div data-reveal>
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-ink-muted hover:text-ink">&larr; Admin dashboard</a>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight">Users</h1>
            <p class="mt-2 text-sm text-ink-muted">Verify upcyclers, suspend accounts, and find anyone by name or email.</p>
        </div>

        <form method="GET" action="{{ route('admin.users') }}" class="card mt-6 flex flex-wrap items-center gap-3 p-4" data-reveal>
            <input type="search" name="q" value="{{ $search }}" class="field flex-1" placeholder="Search name, email, or studio">
            <select name="role" class="field sm:w-44">
                <option value="">All roles</option>
                @foreach ($roles as $value => $label)
                    <option value="{{ $value }}" @selected($role === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">Search</button>
            @if ($search || $role)
                <a href="{{ route('admin.users') }}" class="text-xs font-semibold text-clay-700 hover:underline">Reset</a>
            @endif
        </form>

        <div class="card mt-6 overflow-hidden" data-reveal>
            <table class="w-full text-left text-sm">
                <thead class="bg-sand-100 text-[11px] uppercase tracking-[0.1em] text-ink-faint">
                    <tr>
                        <th class="px-5 py-3 font-bold">Member</th>
                        <th class="px-5 py-3 font-bold">Role</th>
                        <th class="px-5 py-3 font-bold">Status</th>
                        <th class="px-5 py-3 text-right font-bold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="table-row">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <x-avatar :user="$user" class="h-9 w-9" />
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold">{{ $user->name }}</p>
                                        <p class="truncate text-[11px] text-ink-faint">{{ $user->email }}</p>
                                        @if ($user->organization)
                                            <p class="truncate text-[11px] text-ink-muted">{{ $user->organization }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-3.5">
                                <span class="badge badge-sand">{{ $user->role->label() }}</span>
                            </td>

                            <td class="px-5 py-3.5">
                                @if ($user->is_suspended)
                                    <span class="badge badge-slate">Suspended</span>
                                @elseif ($user->isUpcycler())
                                    @if ($user->is_verified)
                                        <span class="badge badge-forest">Verified</span>
                                    @else
                                        <span class="badge badge-amber">Pending</span>
                                    @endif
                                @else
                                    <span class="badge badge-forest">Active</span>
                                @endif
                            </td>

                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap justify-end gap-2">
                                    @if ($user->isUpcycler())
                                        <form method="POST" action="{{ route('admin.users.verify', $user) }}">
                                            @csrf
                                            <button type="submit" class="btn-secondary btn-sm">
                                                {{ $user->is_verified ? 'Unverify' : 'Verify' }}
                                            </button>
                                        </form>
                                    @endif

                                    @unless ($user->isAdmin())
                                        <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                                            @csrf
                                            <button type="submit" class="btn-ghost btn-sm">
                                                {{ $user->is_suspended ? 'Reinstate' : 'Suspend' }}
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-ink-muted">No users match that search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $users->links() }}</div>
    </div>
@endsection

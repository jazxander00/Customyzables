<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Usage: ->middleware('role:donor') or ->middleware('role:donor,upcycler').
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user, 403, 'You need to sign in first.');
        abort_if($user->is_suspended, 403, 'This account is suspended. Contact the organizers.');
        abort_unless(in_array($user->role->value, $roles, true), 403, 'This area is not available for your account type.');

        return $next($request);
    }
}

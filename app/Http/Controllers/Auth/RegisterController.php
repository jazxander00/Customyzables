<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register', [
            'roles' => Role::options(),
        ]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => $request->validated('role'),
            'barangay' => $request->validated('barangay'),
            'phone' => $request->validated('phone'),
            'organization' => $request->validated('organization'),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        AuditLog::record('user.registered', $user, ['role' => $user->role->value], $user);

        $message = $user->isUpcycler()
            ? 'Welcome! Your upcycler account is pending verification — you can browse the feed now and request once an admin approves you.'
            : 'Welcome to Customyzables. Post your first donation whenever you are ready.';

        return redirect()
            ->route($user->role->dashboardRoute())
            ->with('status', $message);
    }
}

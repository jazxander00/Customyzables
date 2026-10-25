<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:190', Rule::unique('users')->ignore($user->id)],
            'barangay' => ['nullable', 'string', 'max:80'],
            'phone' => ['nullable', 'string', 'max:30'],
            'organization' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:600'],
        ]);

        $user->update($validated);

        return back()->with('status', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'Password changed.');
    }

    public function impact(Request $request): View
    {
        $user = $request->user();

        if ($user->isDonor()) {
            $completed = $user->donations()->whereIn('status', ['received', 'completed'])->get();
            $monthly = $user->donations()
                ->whereIn('status', ['received', 'completed'])
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(weight_kg) as kg")
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        } else {
            $completed = Donation::whereIn('status', ['received', 'completed'])
                ->whereHas('acceptedRequest', fn ($q) => $q->where('upcycler_id', $user->id))
                ->get();
            $monthly = Donation::whereIn('status', ['received', 'completed'])
                ->whereHas('acceptedRequest', fn ($q) => $q->where('upcycler_id', $user->id))
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(weight_kg) as kg")
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        $totalKg = (float) $completed->sum('weight_kg');
        $communityKg = (float) Donation::whereIn('status', ['received', 'completed'])->sum('weight_kg');

        return view('profile.impact', compact('user', 'completed', 'monthly', 'totalKg', 'communityKg'));
    }
}

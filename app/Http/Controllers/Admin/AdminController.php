<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RequestStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $stats = [
            'donors' => User::where('role', Role::Donor->value)->count(),
            'upcyclers' => User::where('role', Role::Upcycler->value)->count(),
            'pendingUpcyclers' => User::where('role', Role::Upcycler->value)->where('is_verified', false)->count(),
            'donations' => Donation::count(),
            'live' => Donation::where('status', 'listed')->count(),
            'kg' => (float) Donation::whereIn('status', ['received', 'completed'])->sum('weight_kg'),
            'openRequests' => \App\Models\DonationRequest::whereIn('status', [
                RequestStatus::Pending->value,
                RequestStatus::Accepted->value,
            ])->count(),
        ];

        $pending = User::where('role', Role::Upcycler->value)
            ->where('is_verified', false)
            ->latest()
            ->take(5)
            ->get();

        $recentLogs = AuditLog::with('actor')->latest()->take(8)->get();

        $byCategory = Donation::query()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return view('admin.dashboard', compact('stats', 'pending', 'recentLogs', 'byCategory'));
    }

    public function users(Request $request): View
    {
        $search = $request->query('q');
        $role = $request->query('role');

        $users = User::query()
            ->when($search, function ($q, $term) {
                $like = '%' . addcslashes($term, '%_\\') . '%';

                $q->where(function ($inner) use ($like) {
                    $inner->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('organization', 'like', $like);
                });
            })
            ->when($role && Role::tryFrom($role), fn ($q) => $q->where('role', $role))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'roles' => collect(Role::cases())->mapWithKeys(fn ($r) => [$r->value => $r->label()]),
        ]);
    }

    public function verify(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isUpcycler(), 403);

        $user->forceFill([
            'is_verified' => ! $user->is_verified,
            'verified_at' => $user->is_verified ? null : Carbon::now(),
        ])->save();

        AuditLog::record(
            $user->is_verified ? 'user.verified' : 'user.unverified',
            $user,
            ['email' => $user->email]
        );

        return back()->with('status', $user->is_verified
            ? "{$user->name} is verified and can now request donations."
            : "{$user->name} was unverified.");
    }

    public function suspend(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403, 'Administrators cannot be suspended here.');

        $user->forceFill(['is_suspended' => ! $user->is_suspended])->save();

        AuditLog::record(
            $user->is_suspended ? 'user.suspended' : 'user.reinstated',
            $user,
            ['email' => $user->email]
        );

        return back()->with('status', $user->is_suspended
            ? "{$user->name} was suspended."
            : "{$user->name} was reinstated.");
    }

    public function donations(Request $request): View
    {
        $status = $request->query('status');

        $donations = Donation::query()
            ->with(['images', 'donor'])
            ->withCount('requests')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.donations', [
            'donations' => $donations,
            'status' => $status,
            'statuses' => \App\Enums\DonationStatus::options(),
        ]);
    }

    public function removeDonation(Request $request, Donation $donation): RedirectResponse
    {
        AuditLog::record('donation.removed', $donation, [
            'title' => $donation->title,
            'donor' => $donation->donor?->name,
            'reason' => $request->input('reason'),
        ]);

        // Drop the uploaded photos too, or the public disk keeps them forever.
        foreach ($donation->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $donation->delete();

        return back()->with('status', 'Donation removed from the platform.');
    }

    public function audit(): View
    {
        $logs = AuditLog::with('actor')->latest()->paginate(20);

        return view('admin.audit', compact('logs'));
    }
}

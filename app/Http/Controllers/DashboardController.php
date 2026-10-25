<?php

namespace App\Http\Controllers;

use App\Enums\DonationStatus;
use App\Enums\RequestStatus;
use App\Models\Donation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /** /dashboard sends each role to its own home. Kept as a controller action
     *  (not a closure) so `php artisan route:cache` works in production. */
    public function redirect(Request $request): RedirectResponse
    {
        return redirect()->route($request->user()->role->dashboardRoute());
    }

    public function donor(Request $request): View
    {
        $user = $request->user();

        $donations = $user->donations()
            ->withCount(['pendingRequests'])
            ->with(['images', 'acceptedRequest.upcycler'])
            ->latest()
            ->take(6)
            ->get();

        $stats = [
            'open' => $user->donations()->whereIn('status', [
                DonationStatus::Listed->value,
                DonationStatus::Requested->value,
                DonationStatus::Accepted->value,
                DonationStatus::Scheduled->value,
                DonationStatus::Received->value,
            ])->count(),
            'waiting' => $user->donations()
                ->whereHas('requests', fn ($q) => $q->where('status', RequestStatus::Pending->value))
                ->count(),
            'completed' => $user->donations()->where('status', DonationStatus::Completed->value)->count(),
            'kg' => $user->donatedWeight(),
        ];

        $activity = $user->donations()
            ->whereHas('events')
            ->with(['events' => fn ($q) => $q->with('actor')->take(6)])
            ->latest('updated_at')
            ->take(4)
            ->get();

        return view('dashboard.donor', compact('user', 'donations', 'stats', 'activity'));
    }

    public function upcycler(Request $request): View
    {
        $user = $request->user();

        $available = Donation::available()
            ->with(['images', 'donor'])
            ->latest()
            ->take(6)
            ->get();

        $requests = $user->donationRequests()
            ->with(['donation.images', 'donation.donor'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'available' => Donation::available()->count(),
            'open' => $user->donationRequests()
                ->whereIn('status', [RequestStatus::Pending->value, RequestStatus::Accepted->value])
                ->count(),
            'kg' => $user->claimedWeight(),
            'completed' => $user->donationRequests()
                ->where('status', RequestStatus::Completed->value)
                ->count(),
        ];

        return view('dashboard.upcycler', compact('user', 'available', 'requests', 'stats'));
    }
}

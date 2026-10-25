<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'category', 'barangay', 'condition']);

        $donations = Donation::available()
            ->with(['images', 'donor'])
            ->filter($filters)
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $counts = [
            'total' => Donation::available()->count(),
            'categories' => Donation::available()
                ->selectRaw('category, count(*) as total')
                ->groupBy('category')
                ->pluck('total', 'category'),
        ];

        return view('browse.index', [
            'donations' => $donations,
            'filters' => $filters,
            'counts' => $counts,
            'categories' => Donation::CATEGORIES,
            'barangays' => Donation::BARANGAYS,
        ]);
    }

    public function show(Request $request, Donation $donation): View
    {
        $donation->increment('views');
        $donation->load(['images', 'donor', 'events.actor', 'requests.upcycler', 'acceptedRequest.upcycler']);

        $viewer = $request->user();
        $canRequest = $viewer
            && $viewer->canRequestDonations()
            && $donation->status->value === 'listed'
            && ! $donation->hasOpenRequestFrom($viewer);

        $myRequest = $viewer
            ? $donation->requests->firstWhere('upcycler_id', $viewer->id)
            : null;

        return view('browse.show', compact('donation', 'canRequest', 'myRequest'));
    }
}

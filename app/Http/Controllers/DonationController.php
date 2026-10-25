<?php

namespace App\Http\Controllers;

use App\Enums\DonationStatus;
use App\Http\Requests\StoreDonationRequest;
use App\Models\Donation;
use App\Services\DonationWorkflow;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $donations = $request->user()->donations()
            ->withCount('pendingRequests')
            ->with(['images', 'acceptedRequest.upcycler'])
            ->when($status && DonationStatus::tryFrom($status), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('donations.index', [
            'donations' => $donations,
            'status' => $status,
            'statuses' => DonationStatus::options(),
        ]);
    }

    public function create(): View
    {
        return view('donations.create', [
            'categories' => Donation::CATEGORIES,
            'conditions' => Donation::CONDITIONS,
            'barangays' => Donation::BARANGAYS,
        ]);
    }

    public function store(StoreDonationRequest $request): RedirectResponse
    {
        $donation = $request->user()->donations()->create([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'category' => $request->validated('category'),
            'condition' => $request->validated('condition'),
            'weight_kg' => $request->validated('weight_kg'),
            'item_count' => $request->validated('item_count'),
            'barangay' => $request->validated('barangay'),
            'handoff_method' => $request->validated('handoff_method'),
            'pickup_address' => $request->validated('pickup_address'),
            'available_from' => $request->validated('available_from'),
            'status' => DonationStatus::Listed,
        ]);

        foreach ($request->file('images', []) as $position => $image) {
            $path = $image->store("donations/{$donation->id}", 'public');

            $donation->images()->create([
                'path' => $path,
                'position' => $position,
            ]);
        }

        $donation->events()->create([
            'actor_id' => $request->user()->id,
            'from_status' => null,
            'to_status' => DonationStatus::Listed->value,
            'note' => 'Donation published to the feed',
        ]);

        return redirect()
            ->route('donations.show', $donation)
            ->with('status', 'Your donation is live. Upcyclers nearby can now request it.');
    }

    public function show(Request $request, Donation $donation): View
    {
        abort_unless($donation->user_id === $request->user()->id, 403);

        $donation->load([
            'images',
            'requests.upcycler',
            'acceptedRequest.upcycler',
            'events.actor',
        ]);

        return view('donations.show', compact('donation'));
    }

    public function cancel(Request $request, Donation $donation, DonationWorkflow $workflow): RedirectResponse
    {
        abort_unless($donation->user_id === $request->user()->id, 403);

        $workflow->cancel($donation, $request->user());

        return back()->with('status', 'Donation withdrawn. Open requests were notified.');
    }

    public function destroy(Request $request, Donation $donation): RedirectResponse
    {
        abort_unless($donation->user_id === $request->user()->id, 403);
        abort_unless($donation->status === DonationStatus::Cancelled, 403, 'Withdraw the donation before deleting it.');

        foreach ($donation->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $donation->delete();

        return redirect()
            ->route('donations.index')
            ->with('status', 'Donation deleted.');
    }
}

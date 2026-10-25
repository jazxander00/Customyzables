<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Models\Donation;
use App\Models\DonationRequest;
use App\Services\DonationWorkflow;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request): View
    {
        $requests = $request->user()->donationRequests()
            ->with(['donation.images', 'donation.donor', 'donation.acceptedRequest'])
            ->latest()
            ->paginate(9);

        return view('requests.index', compact('requests'));
    }

    public function store(Request $request, Donation $donation, DonationWorkflow $workflow): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:600'],
        ]);

        $workflow->request($donation, $request->user(), $validated['message'] ?? null);

        return redirect()
            ->route('browse.show', $donation)
            ->with('status', 'Request sent. The donor will approve or decline it — you can chat meanwhile.');
    }

    public function accept(Request $request, DonationRequest $donationRequest, DonationWorkflow $workflow): RedirectResponse
    {
        $this->authorizeDonor($request, $donationRequest);

        $workflow->accept($donationRequest, $request->user());

        return redirect()
            ->route('donations.show', $donationRequest->donation)
            ->with('status', 'Request accepted. Message the upcycler to arrange the handover.');
    }

    public function decline(Request $request, DonationRequest $donationRequest, DonationWorkflow $workflow): RedirectResponse
    {
        $this->authorizeDonor($request, $donationRequest);

        $workflow->decline($donationRequest, $request->user());

        return redirect()
            ->route('donations.show', $donationRequest->donation)
            ->with('status', 'Request declined.');
    }

    public function withdraw(Request $request, DonationRequest $donationRequest, DonationWorkflow $workflow): RedirectResponse
    {
        abort_unless($donationRequest->upcycler_id === $request->user()->id, 403);

        $workflow->withdraw($donationRequest);

        return back()->with('status', 'Request withdrawn.');
    }

    public function schedule(Request $request, DonationRequest $donationRequest, DonationWorkflow $workflow): RedirectResponse
    {
        $this->authorizeDonor($request, $donationRequest);

        $validated = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'scheduled_note' => ['nullable', 'string', 'max:180'],
        ]);

        $workflow->schedule(
            $donationRequest,
            \Illuminate\Support\Carbon::parse($validated['scheduled_at']),
            $validated['scheduled_note'] ?? null,
            $request->user()
        );

        return redirect()
            ->route('donations.show', $donationRequest->donation)
            ->with('status', 'Handover scheduled. Both sides can see the date now.');
    }

    public function receive(Request $request, DonationRequest $donationRequest, DonationWorkflow $workflow): RedirectResponse
    {
        abort_unless($donationRequest->upcycler_id === $request->user()->id, 403);

        $workflow->markReceived($donationRequest, $request->user());

        return redirect()
            ->route('requests.index')
            ->with('status', 'Marked as received. The donor can now close the donation.');
    }

    public function complete(Request $request, Donation $donation, DonationWorkflow $workflow): RedirectResponse
    {
        abort_unless($donation->user_id === $request->user()->id, 403);

        $workflow->complete($donation, $request->user());

        return redirect()
            ->route('donations.show', $donation)
            ->with('status', 'Donation completed — thank you for keeping textiles in use.');
    }

    private function authorizeDonor(Request $request, DonationRequest $donationRequest): void
    {
        abort_unless($donationRequest->donation->user_id === $request->user()->id, 403);
    }
}

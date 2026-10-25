<?php

namespace App\Services;

use App\Enums\DonationStatus;
use App\Enums\RequestStatus;
use App\Models\AuditLog;
use App\Models\Donation;
use App\Models\DonationEvent;
use App\Models\DonationRequest;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Every donation status change goes through this class.
 *
 * Keeping the rules in one place means the stepper, the dashboards, and the admin
 * panel can never disagree about what a status means or which transition is legal.
 */
class DonationWorkflow
{
    public function request(Donation $donation, User $upcycler, ?string $message = null): DonationRequest
    {
        if ($donation->status !== DonationStatus::Listed) {
            throw ValidationException::withMessages([
                'donation' => 'This donation is no longer available to request.',
            ]);
        }

        if ($donation->donor->is($upcycler)) {
            throw ValidationException::withMessages([
                'donation' => 'You cannot request your own donation.',
            ]);
        }

        // Authorization is enforced here, not only in the UI: an unverified or
        // suspended account must not be able to create a request by posting directly.
        abort_unless(
            $upcycler->canRequestDonations(),
            403,
            'Your upcycler account is not verified yet.'
        );

        if ($donation->requests()->where('upcycler_id', $upcycler->id)->exists()) {
            throw ValidationException::withMessages([
                'donation' => 'You already sent a request for this donation.',
            ]);
        }

        return DB::transaction(function () use ($donation, $upcycler, $message) {
            $request = $donation->requests()->create([
                'upcycler_id' => $upcycler->id,
                'message' => $message,
                'status' => RequestStatus::Pending,
            ]);

            $this->transition($donation, DonationStatus::Requested, $upcycler, 'Request received from ' . $upcycler->name);

            return $request;
        });
    }

    public function accept(DonationRequest $request, User $actor): DonationRequest
    {
        $this->ensurePending($request);

        return DB::transaction(function () use ($request, $actor) {
            $request->forceFill([
                'status' => RequestStatus::Accepted,
                'responded_at' => Carbon::now(),
            ])->save();

            // Only one upcycler can receive a bundle — decline the rest explicitly
            // so nobody is left waiting on an answer that will never come.
            $request->donation->requests()
                ->whereKeyNot($request->getKey())
                ->where('status', RequestStatus::Pending->value)
                ->update([
                    'status' => RequestStatus::Declined->value,
                    'responded_at' => Carbon::now(),
                ]);

            $this->transition($request->donation, DonationStatus::Accepted, $actor, 'Request accepted by donor');

            return $request->refresh();
        });
    }

    public function decline(DonationRequest $request, User $actor): DonationRequest
    {
        $this->ensurePending($request);

        return DB::transaction(function () use ($request, $actor) {
            $request->forceFill([
                'status' => RequestStatus::Declined,
                'responded_at' => Carbon::now(),
            ])->save();

            $donation = $request->donation;

            // If that was the last pending request, the bundle drops back to the feed.
            if ($donation->pendingRequests()->doesntExist() && $donation->status === DonationStatus::Requested) {
                $this->transition($donation, DonationStatus::Listed, $actor, 'Request declined — back in the feed');
            }

            return $request->refresh();
        });
    }

    public function withdraw(DonationRequest $request): DonationRequest
    {
        $this->ensurePending($request);

        $request->forceFill([
            'status' => RequestStatus::Withdrawn,
            'responded_at' => Carbon::now(),
        ])->save();

        return $request->refresh();
    }

    public function schedule(DonationRequest $request, Carbon $when, ?string $note, User $actor): DonationRequest
    {
        if (! $request->isAccepted()) {
            throw ValidationException::withMessages([
                'request' => 'Only an accepted request can be scheduled.',
            ]);
        }

        return DB::transaction(function () use ($request, $when, $note, $actor) {
            $request->forceFill([
                'scheduled_at' => $when,
                'scheduled_note' => $note,
            ])->save();

            $this->transition($request->donation, DonationStatus::Scheduled, $actor, 'Handover scheduled for ' . $when->format('M j, Y g:i A'));

            return $request->refresh();
        });
    }

    public function markReceived(DonationRequest $request, User $actor): DonationRequest
    {
        if (! in_array($request->status, [RequestStatus::Accepted], true)) {
            throw ValidationException::withMessages([
                'request' => 'Only an accepted request can be marked as received.',
            ]);
        }

        return DB::transaction(function () use ($request, $actor) {
            $request->forceFill([
                'status' => RequestStatus::Completed,
                'completed_at' => Carbon::now(),
            ])->save();

            $this->transition($request->donation, DonationStatus::Received, $actor, 'Items handed over');

            return $request->refresh();
        });
    }

    public function complete(Donation $donation, User $actor): Donation
    {
        if (! in_array($donation->status, [DonationStatus::Received], true)) {
            throw ValidationException::withMessages([
                'donation' => 'Only a received donation can be completed.',
            ]);
        }

        return DB::transaction(function () use ($donation, $actor) {
            $this->transition($donation, DonationStatus::Completed, $actor, 'Thank you — this donation is complete');

            return $donation->refresh();
        });
    }

    public function cancel(Donation $donation, User $actor): Donation
    {
        if (! $donation->status->isOpen()) {
            throw ValidationException::withMessages([
                'donation' => 'This donation is already closed.',
            ]);
        }

        return DB::transaction(function () use ($donation, $actor) {
            $donation->requests()
                ->whereIn('status', [RequestStatus::Pending->value, RequestStatus::Accepted->value])
                ->update([
                    'status' => RequestStatus::Withdrawn->value,
                    'responded_at' => Carbon::now(),
                ]);

            $this->transition($donation, DonationStatus::Cancelled, $actor, 'Donation withdrawn by donor');

            return $donation->refresh();
        });
    }

    private function ensurePending(DonationRequest $request): void
    {
        if (! $request->isPending()) {
            throw ValidationException::withMessages([
                'request' => 'This request has already been answered.',
            ]);
        }
    }

    private function transition(Donation $donation, DonationStatus $to, User $actor, ?string $note = null): void
    {
        $from = $donation->status;

        if ($from === $to) {
            return;
        }

        $donation->forceFill(['status' => $to])->save();

        DonationEvent::create([
            'donation_id' => $donation->id,
            'actor_id' => $actor->id,
            'from_status' => $from->value,
            'to_status' => $to->value,
            'note' => $note,
        ]);

        AuditLog::record('donation.status_changed', $donation, [
            'from' => $from->value,
            'to' => $to->value,
            'note' => $note,
        ], $actor);
    }
}

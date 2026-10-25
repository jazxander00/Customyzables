<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Donation;
use App\Models\Message;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /** Every conversation is anchored to a donation, so context is never lost. */
    public function index(Request $request): View
    {
        $user = $request->user();

        $donations = Donation::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('requests', fn ($r) => $r->where('upcycler_id', $user->id));
            })
            ->with(['images', 'donor', 'messages' => fn ($q) => $q->latest()->take(1), 'messages.sender'])
            ->orderByDesc('updated_at')
            ->paginate(8);

        return view('chat.index', compact('donations'));
    }

    public function show(Request $request, Donation $donation): View
    {
        $user = $request->user();
        abort_unless($this->isParticipant($donation, $user->id), 403);

        Message::query()
            ->where('donation_id', $donation->id)
            ->whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->update(['read_at' => now()]);

        $donation->load(['donor', 'acceptedRequest.upcycler', 'requests.upcycler']);

        $messages = $donation->messages()->with('sender')->oldest()->get();

        $counterpart = $user->id === $donation->user_id
            ? ($donation->acceptedRequest?->upcycler ?? $donation->requests->first()?->upcycler)
            : $donation->donor;

        return view('chat.show', compact('donation', 'messages', 'counterpart'));
    }

    public function store(StoreMessageRequest $request, Donation $donation): RedirectResponse
    {
        abort_unless($this->isParticipant($donation, $request->user()->id), 403);

        $donation->messages()->create([
            'sender_id' => $request->user()->id,
            'request_id' => $donation->acceptedRequest?->id,
            'body' => $request->validated('body'),
        ]);

        $donation->touch();

        return redirect()
            ->route('chat.show', $donation)
            ->with('status', 'Message sent.');
    }

    private function isParticipant(Donation $donation, int $userId): bool
    {
        return $donation->user_id === $userId
            || $donation->requests()->where('upcycler_id', $userId)->exists();
    }
}

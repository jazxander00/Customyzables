<?php

namespace Tests\Feature;

use App\Enums\DonationStatus;
use App\Enums\Role;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function user(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    public function test_public_pages_render(): void
    {
        $this->get('/')->assertOk()->assertSee('Customyzables');
        $this->get('/browse')->assertOk()->assertSee('Upcycler studio feed');
        $this->get('/login')->assertOk()->assertSee('Welcome back');
        $this->get('/register')->assertOk()->assertSee('Create your account');
    }

    public function test_donation_detail_renders_without_login(): void
    {
        $donation = Donation::where('status', DonationStatus::Listed)->firstOrFail();

        $this->get(route('browse.show', $donation))->assertOk()->assertSee($donation->title);
    }

    public function test_donor_dashboard_and_listing_pages_render(): void
    {
        $donor = $this->user('elaine@customyzables.test');

        $this->actingAs($donor)->get(route('dashboard.donor'))->assertOk()->assertSee('Good to see you');
        $this->actingAs($donor)->get(route('donations.index'))->assertOk()->assertSee('My donations');
        $this->actingAs($donor)->get(route('donations.create'))->assertOk()->assertSee('Post a donation');
        $this->actingAs($donor)->get(route('profile.impact'))->assertOk()->assertSee('Your impact');
        $this->actingAs($donor)->get(route('chat.index'))->assertOk();
    }

    public function test_upcycler_dashboard_and_requests_render(): void
    {
        $upcycler = $this->user('johnriel@customyzables.test');

        $this->actingAs($upcycler)->get(route('dashboard.upcycler'))->assertOk()->assertSee('Basak Upcycling Co-op');
        $this->actingAs($upcycler)->get(route('requests.index'))->assertOk()->assertSee('My requests');
        $this->actingAs($upcycler)->get(route('browse.index'))->assertOk();
    }

    public function test_admin_pages_render(): void
    {
        $admin = $this->user('admin@customyzables.test');

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Platform overview');
        $this->actingAs($admin)->get(route('admin.users'))->assertOk()->assertSee('Users');
        $this->actingAs($admin)->get(route('admin.donations'))->assertOk();
        $this->actingAs($admin)->get(route('admin.audit'))->assertOk()->assertSee('Audit trail');
    }

    public function test_roles_cannot_reach_each_others_areas(): void
    {
        $donor = $this->user('elaine@customyzables.test');
        $upcycler = $this->user('johnriel@customyzables.test');

        $this->actingAs($donor)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($donor)->get(route('dashboard.upcycler'))->assertForbidden();
        $this->actingAs($upcycler)->get(route('donations.index'))->assertForbidden();
    }

    public function test_unverified_upcycler_cannot_request(): void
    {
        $pending = $this->user('marisol@customyzables.test');
        $donation = Donation::where('status', DonationStatus::Listed)->firstOrFail();

        $this->actingAs($pending)
            ->post(route('requests.store', $donation), ['message' => 'I would like this.'])
            ->assertForbidden();
    }

    public function test_donor_can_publish_a_donation(): void
    {
        $donor = $this->user('elaine@customyzables.test');

        $response = $this->actingAs($donor)->post(route('donations.store'), [
            'title' => 'Cotton jersey offcuts from a t-shirt run',
            'description' => 'Twelve kilos of clean cotton jersey offcuts, white and heather grey, no prints.',
            'category' => 'cotton',
            'condition' => 'scrap',
            'weight_kg' => 12,
            'item_count' => 40,
            'barangay' => 'Mactan',
            'handoff_method' => 'dropoff',
        ]);

        $donation = Donation::where('title', 'Cotton jersey offcuts from a t-shirt run')->firstOrFail();

        $response->assertRedirect(route('donations.show', $donation));
        $this->assertSame(DonationStatus::Listed, $donation->status);
        $this->assertDatabaseHas('donation_events', [
            'donation_id' => $donation->id,
            'to_status' => 'listed',
        ]);
    }

    public function test_full_request_flow_from_listed_to_completed(): void
    {
        $upcycler = $this->user('johnriel@customyzables.test');
        $donor = $this->user('elaine@customyzables.test');

        $donation = Donation::create([
            'user_id' => $donor->id,
            'title' => 'Test bundle of canvas remnants',
            'description' => 'A test bundle used to walk the workflow from listed to completed.',
            'category' => 'linen',
            'condition' => 'gently_used',
            'weight_kg' => 2,
            'item_count' => 3,
            'barangay' => 'Mactan',
            'handoff_method' => 'pickup',
            'pickup_address' => 'Test address',
        ]);

        $this->actingAs($upcycler)
            ->post(route('requests.store', $donation), ['message' => 'I will make tote bags.'])
            ->assertRedirect();

        $request = $donation->requests()->firstOrFail();
        $this->assertSame(DonationStatus::Requested, $donation->fresh()->status);

        $this->actingAs($donor)->post(route('requests.accept', $request))->assertRedirect();
        $this->assertSame(DonationStatus::Accepted, $donation->fresh()->status);

        $this->actingAs($donor)->post(route('requests.schedule', $request), [
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'scheduled_note' => 'Barangay hall',
        ])->assertRedirect();
        $this->assertSame(DonationStatus::Scheduled, $donation->fresh()->status);

        $this->actingAs($upcycler)->post(route('requests.receive', $request))->assertRedirect();
        $this->assertSame(DonationStatus::Received, $donation->fresh()->status);

        $this->actingAs($donor)->post(route('donations.complete', $donation))->assertRedirect();
        $this->assertSame(DonationStatus::Completed, $donation->fresh()->status);

        // Every transition left a trail on this donation: requested, accepted,
        // scheduled, received, completed.
        $this->assertSame(5, $donation->events()->count());
    }

    public function test_admin_can_verify_an_upcycler(): void
    {
        $admin = $this->user('admin@customyzables.test');
        $pending = $this->user('marisol@customyzables.test');

        $this->assertFalse($pending->is_verified);

        $this->actingAs($admin)->post(route('admin.users.verify', $pending))->assertRedirect();

        $this->assertTrue($pending->fresh()->is_verified);
        $this->assertDatabaseHas('audit_logs', ['action' => 'user.verified']);
    }

    public function test_conversation_is_visible_to_both_participants(): void
    {
        $upcycler = $this->user('johnriel@customyzables.test');
        $donor = $this->user('maria@customyzables.test');

        $donation = Donation::where('title', 'like', 'Clean cotton T-shirts%')->firstOrFail();

        $this->actingAs($donor)->get(route('chat.show', $donation))->assertOk()->assertSee('approved');
        $this->actingAs($upcycler)->get(route('chat.show', $donation))->assertOk();

        $this->actingAs($upcycler)
            ->post(route('chat.store', $donation), ['body' => 'Collected, thank you!'])
            ->assertRedirect();

        $this->assertDatabaseHas('messages', ['body' => 'Collected, thank you!']);
    }

    public function test_outsider_cannot_read_a_conversation(): void
    {
        $outsider = User::create([
            'name' => 'Nosy Neighbour',
            'email' => 'nosy@example.test',
            'password' => 'password',
            'role' => Role::Donor,
        ]);

        $donation = Donation::where('title', 'like', 'Clean cotton T-shirts%')->firstOrFail();

        $this->actingAs($outsider)->get(route('chat.show', $donation))->assertForbidden();
    }
}

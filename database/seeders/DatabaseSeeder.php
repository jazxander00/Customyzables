<?php

namespace Database\Seeders;

use App\Enums\DonationStatus;
use App\Enums\HandoffMethod;
use App\Enums\RequestStatus;
use App\Enums\Role;
use App\Models\Donation;
use App\Models\DonationRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Customyzables Admin',
            'email' => 'admin@customyzables.test',
            'password' => 'password',
            'role' => Role::Admin,
            'barangay' => 'Poblacion',
            'is_verified' => true,
            'verified_at' => Carbon::now(),
        ]);

        $elaine = User::create([
            'name' => 'Elaine Ena B. Villaluna',
            'email' => 'elaine@customyzables.test',
            'password' => 'password',
            'role' => Role::Donor,
            'barangay' => 'Mactan',
            'phone' => '0917 000 0001',
            'organization' => 'Villaluna Residence',
            'bio' => 'Clearing out a decade of denim. Happy to hand it to someone who will actually use it.',
        ]);

        $johnriel = User::create([
            'name' => 'Johnriel Dave L. Legara',
            'email' => 'johnriel@customyzables.test',
            'password' => 'password',
            'role' => Role::Upcycler,
            'barangay' => 'Basak',
            'phone' => '0917 000 0002',
            'organization' => 'Basak Upcycling Co-op',
            'bio' => 'We turn hotel linen and denim into school bags and floor rugs.',
            'is_verified' => true,
            'verified_at' => Carbon::now(),
        ]);

        $kuya = User::create([
            'name' => 'Kuya Ben',
            'email' => 'ben@customyzables.test',
            'password' => 'password',
            'role' => Role::Upcycler,
            'barangay' => 'Gun-ob',
            'organization' => 'Gun-ob Parish Craft Circle',
            'bio' => 'Parish volunteers making rags, rugs, and tote bags.',
            'is_verified' => true,
            'verified_at' => Carbon::now(),
        ]);

        $pending = User::create([
            'name' => 'Marisol Tan',
            'email' => 'marisol@customyzables.test',
            'password' => 'password',
            'role' => Role::Upcycler,
            'barangay' => 'Marigondon',
            'organization' => 'Marigondon Crafters',
            'bio' => 'Handwoven coasters and patchwork aprons.',
        ]);

        $maria = User::create([
            'name' => 'Maria S. Dela Cruz',
            'email' => 'maria@customyzables.test',
            'password' => 'password',
            'role' => Role::Donor,
            'barangay' => 'Mactan',
            'bio' => 'Hotel housekeeping supervisor. We cycle uniforms twice a year.',
        ]);

        $kyra = User::create([
            'name' => 'Kyra Bautista',
            'email' => 'kyra@customyzables.test',
            'password' => 'password',
            'role' => Role::Donor,
            'barangay' => 'Punta Engaño',
            'organization' => 'Punta Engaño Resort Hub',
        ]);

        $donations = [
            [
                'user' => $elaine,
                'title' => 'Pre-loved denim jackets & jeans (5 pcs)',
                'description' => "Heavy 12–14oz cotton, no stretch, washed twice. Three jackets and two pairs of jeans with blown knees — the denim itself is still strong.\n\nGreat for tote bags, apron pockets, or patchwork panels. One jacket has a small paint spot on the sleeve.",
                'category' => 'denim',
                'condition' => 'gently_used',
                'weight_kg' => 4.8,
                'item_count' => 5,
                'barangay' => 'Mactan',
                'handoff_method' => HandoffMethod::Pickup,
                'pickup_address' => 'Purok Kalubihan, near Mactan Elementary School gate',
                'status' => DonationStatus::Requested,
                'days_ago' => 2,
            ],
            [
                'user' => $maria,
                'title' => 'Clean cotton T-shirts & scraps (12 pcs)',
                'description' => 'Assorted plain tones from a hotel laundry run — white, sand, olive. Zero graphic prints, washed thoroughly. A few have thin collars; the body fabric is fine. Ideal for rag weaving or braided coasters.',
                'category' => 'cotton',
                'condition' => 'wearable',
                'weight_kg' => 3.2,
                'item_count' => 12,
                'barangay' => 'Gun-ob',
                'handoff_method' => HandoffMethod::Dropoff,
                'status' => DonationStatus::Accepted,
                'days_ago' => 4,
            ],
            [
                'user' => $elaine,
                'title' => 'Natural sand linen remnants',
                'description' => "Leftover from a local coastal cafe curtain tailoring job. High-grade unbleached linen with minor water spots, easily cleaned.\n\nAbout 6 running metres in irregular cuts — enough for napkins, bread bags, or a summer waistcoat.",
                'category' => 'linen',
                'condition' => 'flawed',
                'weight_kg' => 2.4,
                'item_count' => 8,
                'barangay' => 'Marigondon',
                'handoff_method' => HandoffMethod::Pickup,
                'pickup_address' => 'Marigondon Heights, block 4 lot 12',
                'status' => DonationStatus::Listed,
                'days_ago' => 1,
            ],
            [
                'user' => $kyra,
                'title' => 'White & khaki twill fabric bundles',
                'description' => 'Resort staff uniform surplus. Heavy-duty poly-cotton twill, colour-fast and tear-resistant after many washes. Ideal for structural book bags, tool rolls, and outdoor cushion covers.',
                'category' => 'blend',
                'condition' => 'gently_used',
                'weight_kg' => 6.0,
                'item_count' => 14,
                'barangay' => 'Punta Engaño',
                'handoff_method' => HandoffMethod::Dropoff,
                'status' => DonationStatus::Listed,
                'days_ago' => 1,
            ],
            [
                'user' => $maria,
                'title' => 'Hotel bed linen offcuts (white)',
                'description' => '300-thread-count cotton percale offcuts from a linen replacement cycle. Clean, professionally laundered, some light grey marks near the hem. Roughly 40 pieces between A4 and A2 size.',
                'category' => 'cotton',
                'condition' => 'gently_used',
                'weight_kg' => 9.5,
                'item_count' => 40,
                'barangay' => 'Mactan',
                'handoff_method' => HandoffMethod::Pickup,
                'pickup_address' => 'Mactan resort service yard, ask for housekeeping',
                'status' => DonationStatus::Completed,
                'days_ago' => 20,
            ],
            [
                'user' => $kyra,
                'title' => 'Knitwear pile — sweaters & cardigans',
                'description' => 'Wool-blend knits with moth nibbles on two pieces. Felted after a hot wash, which makes them perfect for slippers, mittens, and patchwork throws. No synthetic fibre labels left, so treat as mixed wool.',
                'category' => 'knit',
                'condition' => 'flawed',
                'weight_kg' => 5.4,
                'item_count' => 9,
                'barangay' => 'Punta Engaño',
                'handoff_method' => HandoffMethod::Dropoff,
                'status' => DonationStatus::Listed,
                'days_ago' => 3,
            ],
            [
                'user' => $elaine,
                'title' => 'School uniform scraps — navy & white',
                'description' => 'Offcuts from a tailoring shop that handles school uniforms. Poly-cotton, mostly navy with white piping. Small pieces, best for patchwork or appliqué.',
                'category' => 'blend',
                'condition' => 'scrap',
                'weight_kg' => 1.8,
                'item_count' => 25,
                'barangay' => 'Basak',
                'handoff_method' => HandoffMethod::Dropoff,
                'status' => DonationStatus::Listed,
                'days_ago' => 5,
            ],
            [
                'user' => $maria,
                'title' => 'Curtain panels — heavy cotton canvas',
                'description' => 'Four full-length panels removed during a renovation. Sun-faded on one side, structurally sound. Excellent weight for market bags, cushion covers, or a sturdy apron.',
                'category' => 'linen',
                'condition' => 'wearable',
                'weight_kg' => 7.2,
                'item_count' => 4,
                'barangay' => 'Gun-ob',
                'handoff_method' => HandoffMethod::Pickup,
                'pickup_address' => 'Gun-ob, near the parish multi-hall',
                'status' => DonationStatus::Scheduled,
                'days_ago' => 9,
            ],
            [
                'user' => $kyra,
                'title' => 'Denim offcuts from a tailoring shop',
                'description' => 'Legs, waistbands, and pocket bags from a month of alterations. Indigo and black mix. Roughly two sacks — bring a car if you can.',
                'category' => 'denim',
                'condition' => 'scrap',
                'weight_kg' => 8.4,
                'item_count' => 60,
                'barangay' => 'Poblacion',
                'handoff_method' => HandoffMethod::Pickup,
                'pickup_address' => 'Poblacion tailoring row, beside the public market',
                'status' => DonationStatus::Received,
                'days_ago' => 14,
            ],
            [
                'user' => $elaine,
                'title' => 'Baby clothes bundle (0–2 years)',
                'description' => 'Soft cotton onesies and sleepsuits, washed in fragrance-free detergent. Two have small stains at the collar. Good for quilting or doll clothes.',
                'category' => 'cotton',
                'condition' => 'gently_used',
                'weight_kg' => 2.1,
                'item_count' => 18,
                'barangay' => 'Mactan',
                'handoff_method' => HandoffMethod::Dropoff,
                'status' => DonationStatus::Listed,
                'days_ago' => 6,
            ],
        ];

        foreach ($donations as $data) {
            $donation = Donation::create([
                'user_id' => $data['user']->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'category' => $data['category'],
                'condition' => $data['condition'],
                'weight_kg' => $data['weight_kg'],
                'item_count' => $data['item_count'],
                'barangay' => $data['barangay'],
                'handoff_method' => $data['handoff_method'],
                'pickup_address' => $data['pickup_address'] ?? null,
                'available_from' => null,
                'status' => $data['status'],
                'created_at' => Carbon::now()->subDays($data['days_ago']),
                'updated_at' => Carbon::now()->subDays($data['days_ago']),
            ]);

            $donation->events()->create([
                'actor_id' => $data['user']->id,
                'from_status' => null,
                'to_status' => DonationStatus::Listed->value,
                'note' => 'Donation published to the feed',
                'created_at' => $donation->created_at,
            ]);
        }

        // Requests, keyed by donation title so the seeded story stays readable.
        $denim = Donation::where('title', 'like', 'Pre-loved denim%')->first();
        $cotton = Donation::where('title', 'like', 'Clean cotton T-shirts%')->first();
        $linen = Donation::where('title', 'like', 'Curtain panels%')->first();
        $offcuts = Donation::where('title', 'like', 'Denim offcuts%')->first();

        DonationRequest::create([
            'donation_id' => $denim->id,
            'upcycler_id' => $johnriel->id,
            'message' => 'We would cut these into 15 tote bags for a school order in Basak. I can collect this weekend — I have a tricycle.',
            'status' => RequestStatus::Pending,
            'created_at' => Carbon::now()->subDay(),
        ]);

        DonationRequest::create([
            'donation_id' => $denim->id,
            'upcycler_id' => $kuya->id,
            'message' => 'Interested in the jackets only, for a patchwork throw for the parish hall. Happy to take the jeans too if nobody else wants them.',
            'status' => RequestStatus::Pending,
            'created_at' => Carbon::now()->subHours(20),
        ]);

        DonationRequest::create([
            'donation_id' => $cotton->id,
            'upcycler_id' => $johnriel->id,
            'message' => 'Perfect for our rag-weaving batch next week. We can pick up from the Gun-ob drop box.',
            'status' => RequestStatus::Accepted,
            'responded_at' => Carbon::now()->subDays(3),
            'created_at' => Carbon::now()->subDays(4),
        ]);

        DonationRequest::create([
            'donation_id' => $linen->id,
            'upcycler_id' => $johnriel->id,
            'message' => 'Need this for cushion covers — we have a cafe client waiting. Can collect Thursday morning.',
            'status' => RequestStatus::Accepted,
            'responded_at' => Carbon::now()->subDays(8),
            'scheduled_at' => Carbon::now()->addDays(2)->setTime(9, 30),
            'scheduled_note' => 'Meet at the barangay hall parking area',
            'created_at' => Carbon::now()->subDays(9),
        ]);

        DonationRequest::create([
            'donation_id' => $offcuts->id,
            'upcycler_id' => $johnriel->id,
            'message' => 'We will sort these into patchwork panels. Collected already — thank you!',
            'status' => RequestStatus::Completed,
            'responded_at' => Carbon::now()->subDays(13),
            'completed_at' => Carbon::now()->subDays(12),
            'created_at' => Carbon::now()->subDays(14),
        ]);

        // A little conversation so the chat screen is not empty on first run.
        $cotton->messages()->create([
            'sender_id' => $maria->id,
            'request_id' => $cotton->requests()->first()->id,
            'body' => 'Hi Johnriel — approved. The box is at the Gun-ob drop box, labelled with your name.',
            'created_at' => Carbon::now()->subDays(3),
        ]);

        $cotton->messages()->create([
            'sender_id' => $johnriel->id,
            'request_id' => $cotton->requests()->first()->id,
            'body' => 'Salamat! I will collect tomorrow morning and send photos once the first batch is woven.',
            'created_at' => Carbon::now()->subDays(3)->addHours(2),
        ]);

        $this->command->info('Seeded: admin@customyzables.test / elaine@ / johnriel@ / ben@ / marisol@ / maria@ / kyra@ — password: password');
    }
}

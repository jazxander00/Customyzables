<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'kg' => (float) Donation::whereIn('status', ['received', 'completed'])->sum('weight_kg'),
            'completed' => Donation::where('status', 'completed')->count(),
            'upcyclers' => User::where('role', 'upcycler')->where('is_verified', true)->count(),
            'barangays' => Donation::query()->distinct()->count('barangay'),
        ];

        $recent = Donation::available()
            ->with(['images', 'donor'])
            ->latest()
            ->take(3)
            ->get();

        return view('home', compact('stats', 'recent'));
    }
}

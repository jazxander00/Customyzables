<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// Public feed — donors can show off their donations, upcyclers can window-shop before signing up.
Route::get('/browse', [BrowseController::class, 'index'])->name('browse.index');
Route::get('/browse/{donation}', [BrowseController::class, 'show'])->name('browse.show');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard');

    Route::get('/dashboard/donor', [DashboardController::class, 'donor'])
        ->middleware('role:donor')
        ->name('dashboard.donor');

    Route::get('/dashboard/upcycler', [DashboardController::class, 'upcycler'])
        ->middleware('role:upcycler')
        ->name('dashboard.upcycler');

    // Donor side.
    Route::middleware('role:donor')->group(function () {
        Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
        Route::get('/donations/create', [DonationController::class, 'create'])->name('donations.create');
        Route::post('/donations', [DonationController::class, 'store'])->name('donations.store');
        Route::get('/donations/{donation}', [DonationController::class, 'show'])->name('donations.show');
        Route::post('/donations/{donation}/cancel', [DonationController::class, 'cancel'])->name('donations.cancel');
        Route::delete('/donations/{donation}', [DonationController::class, 'destroy'])->name('donations.destroy');
        Route::post('/donations/{donation}/complete', [RequestController::class, 'complete'])->name('donations.complete');

        Route::post('/requests/{donationRequest}/accept', [RequestController::class, 'accept'])->name('requests.accept');
        Route::post('/requests/{donationRequest}/decline', [RequestController::class, 'decline'])->name('requests.decline');
        Route::post('/requests/{donationRequest}/schedule', [RequestController::class, 'schedule'])->name('requests.schedule');
    });

    // Upcycler side.
    Route::middleware('role:upcycler')->group(function () {
        Route::get('/my-requests', [RequestController::class, 'index'])->name('requests.index');
        Route::post('/donations/{donation}/requests', [RequestController::class, 'store'])->name('requests.store');
        Route::post('/requests/{donationRequest}/withdraw', [RequestController::class, 'withdraw'])->name('requests.withdraw');
        Route::post('/requests/{donationRequest}/receive', [RequestController::class, 'receive'])->name('requests.receive');
    });

    // Shared by both roles.
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{donation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{donation}', [ChatController::class, 'store'])->name('chat.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/impact', [ProfileController::class, 'impact'])->name('profile.impact');

    // Admin side.
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users/{user}/verify', [AdminController::class, 'verify'])->name('users.verify');
        Route::post('/users/{user}/suspend', [AdminController::class, 'suspend'])->name('users.suspend');
        Route::get('/donations', [AdminController::class, 'donations'])->name('donations');
        Route::delete('/donations/{donation}', [AdminController::class, 'removeDonation'])->name('donations.remove');
        Route::get('/audit', [AdminController::class, 'audit'])->name('audit');
    });
});

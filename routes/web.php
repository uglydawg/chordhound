<?php

use App\Http\Controllers\ChordController;
use App\Http\Controllers\SocialAuthController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use Laravel\Pulse\Facades\Pulse;

Route::get('/test', function () {
    return view('test');
});

Route::get('/', [ChordController::class, 'index'])->name('home');
Route::get('/chords', [ChordController::class, 'index'])->name('chords.index');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Social Authentication Routes
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

// Magic Link Authentication
Route::get('/auth/magic-link/{token}', [SocialAuthController::class, 'verifyMagicLink'])->name('auth.magic-link.verify');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Chord routes
    Route::get('/my-chord-sets', [ChordController::class, 'mySets'])->name('chords.my-sets');
    Route::get('/chords/{chordSet}/edit', [ChordController::class, 'edit'])->name('chords.edit');
});

require __DIR__.'/auth.php';

// Donation routes
Route::get('/donate', function () {
    return view('donate');
})->name('donate');

Route::get('/donation/success', function () {
    return view('donation.success');
})->name('donation.success');

// Admin monitoring routes
Route::middleware(['auth'])->group(function () {
    Route::get('/pulse', function () {
        return Pulse::dashboard();
    })->name('pulse');
});

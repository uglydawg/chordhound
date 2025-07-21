<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\MagicLink;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                ]
            );

            Auth::login($user, true);

            return redirect()->intended(route('dashboard'));
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Unable to login with Google. Please try again.');
        }
    }

    public function verifyMagicLink(string $token)
    {
        $magicLink = MagicLink::where('token', $token)->first();

        if (! $magicLink) {
            return redirect()->route('login')->with('error', 'Invalid login link.');
        }

        if ($magicLink->isExpired()) {
            return redirect()->route('login')->with('error', 'This login link has expired.');
        }

        if ($magicLink->isUsed()) {
            return redirect()->route('login')->with('error', 'This login link has already been used.');
        }

        // Create or find user
        $user = User::firstOrCreate(
            ['email' => $magicLink->email],
            [
                'name' => explode('@', $magicLink->email)[0],
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => now(),
            ]
        );

        $magicLink->markAsUsed();

        Auth::login($user, true);

        return redirect()->intended(route('dashboard'));
    }
}

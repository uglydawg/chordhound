<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\AuthCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AuthCodeLogin extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|digits:6')]
    public string $code = '';

    public bool $codeSent = false;

    public string $error = '';

    public function sendCode()
    {
        $this->validate(['email' => 'required|email']);
        $this->error = '';

        $authCode = AuthCode::generateFor($this->email);

        Mail::raw(
            "Your Piano Chords login code is: {$authCode->code}\n\nThis code will expire in 10 minutes.",
            function ($message) {
                $message->to($this->email)
                    ->subject('Your Piano Chords Login Code');
            }
        );

        $this->codeSent = true;
    }

    public function verifyCode()
    {
        $this->validate();
        $this->error = '';

        // Check for fixed development login code
        if (config('app.auth_fixed_login_code_enabled', false) && $this->code === '555121') {
            // Create or find user with fixed code
            $user = User::firstOrCreate(
                ['email' => $this->email],
                [
                    'name' => explode('@', $this->email)[0],
                    'username' => User::generateUsername($this->email),
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                ]
            );

            Auth::login($user, true);

            return redirect()->intended(route('dashboard'));
        }

        $authCode = AuthCode::where('email', $this->email)
            ->where('code', $this->code)
            ->first();

        if (! $authCode) {
            $this->error = 'Invalid code. Please check and try again.';

            return;
        }

        if ($authCode->isExpired()) {
            $this->error = 'This code has expired. Please request a new one.';

            return;
        }

        if ($authCode->isUsed()) {
            $this->error = 'This code has already been used.';

            return;
        }

        if ($authCode->tooManyAttempts()) {
            $this->error = 'Too many failed attempts. Please request a new code.';

            return;
        }

        // Create or find user
        $user = User::firstOrCreate(
            ['email' => $this->email],
            [
                'name' => explode('@', $this->email)[0],
                'username' => User::generateUsername($this->email),
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => now(),
            ]
        );

        $authCode->markAsUsed();

        Auth::login($user, true);

        return redirect()->intended(route('dashboard'));
    }

    public function resendCode()
    {
        $this->codeSent = false;
        $this->code = '';
        $this->error = '';
        $this->sendCode();
    }

    public function render()
    {
        return view('livewire.auth-code-login');
    }
}

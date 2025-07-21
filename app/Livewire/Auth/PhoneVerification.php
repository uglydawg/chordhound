<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Models\PhoneVerification as PhoneVerificationModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PhoneVerification extends Component
{
    #[Validate('required|string|regex:/^\+?[1-9]\d{1,14}$/')]
    public string $phoneNumber = '';

    #[Validate('required|digits:6')]
    public string $verificationCode = '';

    public bool $codeSent = false;

    public string $error = '';

    public ?User $user = null;

    public function mount(?User $user = null)
    {
        $this->user = $user ?? Auth::user();
    }

    public function sendVerificationCode()
    {
        $this->validate(['phoneNumber' => 'required|string|regex:/^\+?[1-9]\d{1,14}$/']);
        $this->error = '';

        // Check if phone number is already verified by another user
        if (User::where('phone_number', $this->phoneNumber)
            ->where('phone_verified_at', '!=', null)
            ->where('id', '!=', $this->user?->id)
            ->exists()) {
            $this->error = 'This phone number is already registered to another account.';

            return;
        }

        $verification = PhoneVerificationModel::generateFor($this->phoneNumber);

        // In a real implementation, you would send this via SMS
        // For demo purposes, we'll log it or show it in development
        if (app()->environment('local')) {
            logger("Phone verification code for {$this->phoneNumber}: {$verification->code}");
        }

        $this->codeSent = true;
    }

    public function verifyCode()
    {
        $this->validate();
        $this->error = '';

        $verification = PhoneVerificationModel::where('phone_number', $this->phoneNumber)
            ->where('code', $this->verificationCode)
            ->first();

        if (! $verification) {
            $this->addError('verificationCode', 'Invalid verification code.');

            return;
        }

        if ($verification->isExpired()) {
            $this->addError('verificationCode', 'This verification code has expired.');

            return;
        }

        if ($verification->isUsed()) {
            $this->addError('verificationCode', 'This verification code has already been used.');

            return;
        }

        if ($verification->tooManyAttempts()) {
            $this->addError('verificationCode', 'Too many failed attempts. Please request a new code.');

            return;
        }

        // Update user's phone number and mark as verified
        if ($this->user) {
            $this->user->update([
                'phone_number' => $this->phoneNumber,
                'phone_verified_at' => now(),
            ]);

            $verification->markAsUsed();

            session()->flash('success', 'Phone number verified successfully!');

            return redirect()->route('dashboard');
        }
    }

    public function resendCode()
    {
        $this->codeSent = false;
        $this->verificationCode = '';
        $this->error = '';
        $this->sendVerificationCode();
    }

    public function render()
    {
        return view('livewire.auth.phone-verification');
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';
    public string $username = '';
    public string $display_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $robot_verification = false;
    public string $step = 'email'; // email, details, verification

    /**
     * Validate email and proceed to details step
     */
    public function validateEmail(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        ]);

        $this->step = 'details';
    }

    /**
     * Validate details and proceed to robot verification
     */
    public function validateDetails(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username', 'regex:/^[a-zA-Z0-9_]+$/'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $this->step = 'verification';
    }

    /**
     * Simple robot verification (in production, use proper CAPTCHA)
     */
    public function verifyRobot(): void
    {
        if (!$this->robot_verification) {
            $this->addError('robot_verification', 'Please confirm you are not a robot.');
            return;
        }

        $this->register();
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username', 'regex:/^[a-zA-Z0-9_]+$/'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        // Redirect to phone verification instead of dashboard
        $this->redirect(route('auth.phone-verification'), navigate: true);
    }

    public function goBack(): void
    {
        if ($this->step === 'details') {
            $this->step = 'email';
        } elseif ($this->step === 'verification') {
            $this->step = 'details';
        }
    }
}

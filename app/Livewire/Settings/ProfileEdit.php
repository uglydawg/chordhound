<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ProfileEdit extends Component
{
    public string $name = '';

    public string $username = '';

    public string $display_name = '';

    public string $email = '';

    public string $new_email = '';

    public string $password = '';

    public array $usernameSuggestions = [];

    public bool $showEmailChange = false;

    public string $successMessage = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->username = $user->username ?? User::generateUsername($user->email);
        $this->display_name = $user->display_name ?? '';
        $this->email = $user->email;

        // If user doesn't have a username, save the generated one
        if (empty($user->username)) {
            $user->update(['username' => $this->username]);
        }
    }

    /**
     * Update profile information
     */
    public function updateProfile(): void
    {
        $user = auth()->user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => User::getUsernameValidationRules($user->id),
            'display_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($validated);

        $this->successMessage = 'Profile updated successfully!';
        $this->usernameSuggestions = [];

        // Clear success message after 3 seconds
        $this->js('setTimeout(() => $wire.clearSuccessMessage(), 3000)');
    }

    /**
     * Check username availability and suggest alternatives
     */
    public function checkUsername(): void
    {
        if (empty($this->username)) {
            return;
        }

        $user = auth()->user();

        // If username hasn't changed, no need to check
        if ($this->username === $user->username) {
            $this->usernameSuggestions = [];
            $this->resetErrorBag('username');

            return;
        }

        if (! User::isUsernameAvailable($this->username, $user->email)) {
            $this->usernameSuggestions = User::suggestUsernames($this->username, $user->email);
            $this->addError('username', 'This username is already taken. Try one of the suggestions below.');
        } else {
            $this->usernameSuggestions = [];
            $this->resetErrorBag('username');
        }
    }

    /**
     * Select a suggested username
     */
    public function selectUsername(string $username): void
    {
        $this->username = $username;
        $this->usernameSuggestions = [];
        $this->resetErrorBag('username');
    }

    /**
     * Show email change form
     */
    public function showEmailChangeForm(): void
    {
        $this->showEmailChange = true;
        $this->new_email = '';
        $this->password = '';
    }

    /**
     * Cancel email change
     */
    public function cancelEmailChange(): void
    {
        $this->showEmailChange = false;
        $this->new_email = '';
        $this->password = '';
        $this->resetErrorBag(['new_email', 'password']);
    }

    /**
     * Initiate email change (sends confirmation email)
     */
    public function initiateEmailChange(): void
    {
        $this->validate([
            'new_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string'],
        ]);

        $user = auth()->user();

        // Verify current password
        if (! Hash::check($this->password, $user->password)) {
            $this->addError('password', 'The provided password is incorrect.');

            return;
        }

        // In a real app, you would send a confirmation email here
        // For now, we'll just update the email directly
        $user->update([
            'email' => $this->new_email,
            'email_verified_at' => null, // Force re-verification
        ]);

        $this->email = $this->new_email;
        $this->showEmailChange = false;
        $this->new_email = '';
        $this->password = '';

        $this->successMessage = 'Email updated successfully! Please verify your new email address.';
        $this->js('setTimeout(() => $wire.clearSuccessMessage(), 5000)');
    }

    /**
     * Clear success message
     */
    public function clearSuccessMessage(): void
    {
        $this->successMessage = '';
    }

    public function render()
    {
        return view('livewire.settings.profile-edit-simple');
    }
}

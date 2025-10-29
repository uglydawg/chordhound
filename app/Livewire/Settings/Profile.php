<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use Livewire\Component;

class Profile extends Component
{
    public string $name = '';
    public string $email = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function updateProfileInformation(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $user = auth()->user();
        $emailChanged = $this->email !== $user->email;

        $user->fill($validated);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        session()->flash('status', 'profile-updated');
    }

    public function render()
    {
        return view('livewire.settings.profile');
    }
}

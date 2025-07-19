<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\MagicLink;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;

class MagicLinkLogin extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public bool $linkSent = false;

    public function sendMagicLink()
    {
        $this->validate();

        $magicLink = MagicLink::generateFor($this->email);

        Mail::raw(
            "Click this link to log in to Piano Chords: " . route('auth.magic-link.verify', $magicLink->token),
            function ($message) {
                $message->to($this->email)
                    ->subject('Your Piano Chords Login Link');
            }
        );

        $this->linkSent = true;
    }

    public function render()
    {
        return view('livewire.magic-link-login');
    }
}
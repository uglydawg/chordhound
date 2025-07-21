<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class SupportSite extends Component
{
    public array $amounts = [5, 10, 20];

    public ?int $selectedAmount = null;

    public ?int $customAmount = null;

    public bool $showCustomInput = false;

    public function mount(): void
    {
        $this->selectedAmount = $this->amounts[0];
    }

    public function selectAmount(int $amount): void
    {
        $this->selectedAmount = $amount;
        $this->showCustomInput = false;
        $this->customAmount = null;
    }

    public function showCustom(): void
    {
        $this->showCustomInput = true;
        $this->selectedAmount = null;
    }

    public function updatedCustomAmount(): void
    {
        if ($this->customAmount && $this->customAmount > 0) {
            $this->selectedAmount = $this->customAmount;
        }
    }

    public function donate()
    {
        $amount = $this->selectedAmount ?? $this->customAmount;

        if (! $amount || $amount < 1) {
            $this->addError('amount', 'Please select or enter a valid donation amount.');

            return;
        }

        try {
            Stripe::setApiKey(config('cashier.secret'));

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Support ChordHound',
                                'description' => 'Thank you for supporting ChordHound development!',
                            ],
                            'unit_amount' => $amount * 100, // Convert to cents
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => route('donation.success'),
                'cancel_url' => url('/'),
                'metadata' => [
                    'type' => 'donation',
                    'amount' => $amount,
                ],
            ]);

            return $this->redirect($session->url);
        } catch (\Exception $e) {
            $this->addError('donation', 'Unable to process donation at this time. Please try again later.');
            logger()->error('Donation error: '.$e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.support-site');
    }
}

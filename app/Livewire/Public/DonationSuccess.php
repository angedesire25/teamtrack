<?php

namespace App\Livewire\Public;

use App\Models\Donation;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

#[Layout('layouts.public-donation')]
class DonationSuccess extends Component
{
    public ?Donation $donation = null;
    public bool $found = false;

    public function mount(string $session_id = ''): void
    {
        if (!$session_id) return;

        $this->donation = Donation::withoutGlobalScope('tenant')
            ->with(['donor', 'campaign'])
            ->where('stripe_session_id', $session_id)
            ->first();

        $this->found = (bool) $this->donation;
    }

    public function render()
    {
        return view('livewire.public.donation-success');
    }
}

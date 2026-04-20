<?php

namespace App\Livewire\Public;

use App\Models\DonationCampaign;
use App\Models\Donation;
use App\Models\Donor;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

#[Layout('layouts.public-donation')]
class DonationPage extends Component
{
    public ?DonationCampaign $campaign = null;

    // Montant
    public string $selectedAmount = '';
    public string $customAmount   = '';
    public string $frequency      = 'one_time';

    // Donateur
    public string $firstName   = '';
    public string $lastName    = '';
    public string $email       = '';
    public bool   $isAnonymous = false;
    public string $message     = '';

    public bool   $step2 = false; // false = choix montant, true = infos donateur

    protected function rules(): array
    {
        return [
            'selectedAmount' => 'required_without:customAmount',
            'customAmount'   => 'nullable|numeric|min:100',
            'frequency'      => 'required|in:one_time,monthly,annual',
            'firstName'      => $this->isAnonymous ? 'nullable' : 'required|string|max:100',
            'lastName'       => $this->isAnonymous ? 'nullable' : 'required|string|max:100',
            'email'          => $this->isAnonymous ? 'nullable|email' : 'required|email|max:255',
            'message'        => 'nullable|string|max:500',
        ];
    }

    public function mount(?int $campaign = null): void
    {
        if ($campaign) {
            $this->campaign = DonationCampaign::withoutGlobalScope('tenant')
                ->where('is_active', true)
                ->findOrFail($campaign);
        } else {
            // Prendre la première campagne active du tenant
            if (app()->has('tenant')) {
                $this->campaign = DonationCampaign::where('is_active', true)->first();
            }
        }
    }

    public function selectAmount(string $amount): void
    {
        $this->selectedAmount = $amount;
        $this->customAmount   = '';
    }

    public function updatedCustomAmount(): void
    {
        if ($this->customAmount !== '') {
            $this->selectedAmount = '';
        }
    }

    public function goToStep2(): void
    {
        $amount = $this->getAmount();
        if (!$amount || $amount < 100) {
            $this->addError('selectedAmount', 'Veuillez saisir un montant valide (minimum 100 F CFA).');
            return;
        }
        $this->step2 = true;
    }

    public function backToStep1(): void
    {
        $this->step2 = false;
    }

    public function checkout(): void
    {
        $this->validate();

        $amount    = $this->getAmount();
        $tenantId  = app()->has('tenant') ? app('tenant')->id : null;
        $currency  = config('services.stripe.currency', 'eur');

        // Créer ou retrouver le donateur
        $donor = null;
        if (!$this->isAnonymous && $this->email) {
            $donor = Donor::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenantId)
                ->where('email', $this->email)
                ->first();

            if (!$donor) {
                $donor = Donor::create([
                    'tenant_id'  => $tenantId,
                    'first_name' => $this->firstName,
                    'last_name'  => $this->lastName,
                    'email'      => $this->email,
                ]);
            }
        }

        // Créer la donation en attente
        $donation = Donation::create([
            'tenant_id'   => $tenantId,
            'campaign_id' => $this->campaign?->id,
            'donor_id'    => $donor?->id,
            'amount'      => $amount,
            'currency'    => $currency,
            'frequency'   => $this->frequency,
            'status'      => 'pending',
            'is_anonymous'=> $this->isAnonymous,
            'message'     => $this->message ?: null,
        ]);

        // Créer la session Stripe Checkout
        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItem = [
            'price_data' => [
                'currency'     => $currency,
                'unit_amount'  => (int) round($amount * 100), // Stripe en centimes
                'product_data' => [
                    'name' => 'Don — ' . ($this->campaign?->title ?? app('tenant')?->name ?? 'Club'),
                ],
            ],
            'quantity' => 1,
        ];

        // Récurrence : Stripe subscription
        if ($this->frequency !== 'one_time') {
            $lineItem['price_data']['recurring'] = [
                'interval' => $this->frequency === 'monthly' ? 'month' : 'year',
            ];
        }

        $sessionParams = [
            'payment_method_types' => ['card'],
            'mode'                 => $this->frequency !== 'one_time' ? 'subscription' : 'payment',
            'line_items'           => [$lineItem],
            'success_url'          => url('/dons/merci?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url'           => url('/dons'),
            'metadata'             => ['donation_id' => $donation->id],
            'client_reference_id'  => (string) $donation->id,
        ];

        if ($donor?->stripe_customer_id) {
            $sessionParams['customer'] = $donor->stripe_customer_id;
        } elseif (!$this->isAnonymous && $this->email) {
            $sessionParams['customer_email'] = $this->email;
        }

        $session = StripeSession::create($sessionParams);

        // Sauvegarder l'ID session
        $donation->update(['stripe_session_id' => $session->id]);

        // Rediriger vers Stripe
        $this->redirect($session->url);
    }

    private function getAmount(): float
    {
        if ($this->customAmount !== '' && is_numeric($this->customAmount)) {
            return (float) $this->customAmount;
        }
        return (float) $this->selectedAmount;
    }

    public function render()
    {
        $campaigns = collect();
        if (app()->has('tenant')) {
            $campaigns = DonationCampaign::where('is_active', true)
                ->orderByDesc('created_at')->get();
        }
        return view('livewire.public.donation-page', compact('campaigns'));
    }
}

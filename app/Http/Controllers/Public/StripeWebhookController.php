<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\DonationReceiptMail;
use App\Models\Donation;
use App\Models\DonationCampaign;
use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Signature du webhook Stripe invalide', ['error' => $e->getMessage()]);
            return response('Signature invalide', 400);
        }

        match ($event->type) {
            'checkout.session.completed'         => $this->handleSessionCompleted($event->data->object),
            'invoice.payment_succeeded'          => $this->handleInvoicePaymentSucceeded($event->data->object),
            'customer.subscription.deleted'      => $this->handleSubscriptionDeleted($event->data->object),
            default                              => null,
        };

        return response('OK', 200);
    }

    private function handleSessionCompleted(object $session): void
    {
        $donation = Donation::withoutGlobalScope('tenant')
            ->where('stripe_session_id', $session->id)
            ->first();

        if (!$donation || $donation->status === 'completed') {
            return;
        }

        $donation->update([
            'status'                  => 'completed',
            'stripe_payment_intent_id'=> $session->payment_intent ?? null,
            'stripe_subscription_id'  => $session->subscription ?? null,
            'receipt_number'          => Donation::generateReceiptNumber($donation->tenant_id),
        ]);

        // Mettre à jour le stripe_customer_id du donateur si disponible
        if ($session->customer && $donation->donor_id) {
            $donation->donor?->update(['stripe_customer_id' => $session->customer]);
        }

        // Incrémenter le montant collecté de la campagne
        if ($donation->campaign_id) {
            DonationCampaign::withoutGlobalScope('tenant')
                ->where('id', $donation->campaign_id)
                ->increment('collected_amount', $donation->amount);
        }

        // Envoyer le reçu par e-mail au donateur
        $this->sendReceipt($donation);
    }

    private function handleInvoicePaymentSucceeded(object $invoice): void
    {
        // Paiement récurrent : créer une nouvelle entrée de donation
        if (!$invoice->subscription) return;

        $parent = Donation::withoutGlobalScope('tenant')
            ->where('stripe_subscription_id', $invoice->subscription)
            ->where('status', 'completed')
            ->latest()
            ->first();

        if (!$parent) return;

        // Éviter les doublons sur le premier paiement, déjà traité par l'événement session.completed
        if ($invoice->billing_reason === 'subscription_create') return;

        $newDonation = Donation::create([
            'tenant_id'               => $parent->tenant_id,
            'campaign_id'             => $parent->campaign_id,
            'donor_id'                => $parent->donor_id,
            'amount'                  => $parent->amount,
            'currency'                => $parent->currency,
            'frequency'               => $parent->frequency,
            'status'                  => 'completed',
            'stripe_subscription_id'  => $invoice->subscription,
            'stripe_payment_intent_id'=> $invoice->payment_intent ?? null,
            'is_anonymous'            => $parent->is_anonymous,
            'receipt_number'          => Donation::generateReceiptNumber($parent->tenant_id),
        ]);

        if ($parent->campaign_id) {
            DonationCampaign::withoutGlobalScope('tenant')
                ->where('id', $parent->campaign_id)
                ->increment('collected_amount', $newDonation->amount);
        }

        $this->sendReceipt($newDonation);
    }

    private function handleSubscriptionDeleted(object $subscription): void
    {
        // Abonnement résilié : les futurs dons récurrents sont stoppés (aucune action en base pour l'instant)
        Log::info('Abonnement Stripe supprimé', ['subscription_id' => $subscription->id]);
    }

    private function sendReceipt(Donation $donation): void
    {
        $donation->load(['donor', 'campaign']);
        $email = $donation->donor?->email;

        if ($email && !$donation->is_anonymous) {
            try {
                Mail::to($email)->send(new DonationReceiptMail($donation));
                $donation->update(['receipt_sent_at' => now()]);
            } catch (\Exception $e) {
                Log::error('Échec de l\'envoi du reçu de don', [
                    'donation_id' => $donation->id,
                    'error'       => $e->getMessage(),
                ]);
            }
        }
    }
}

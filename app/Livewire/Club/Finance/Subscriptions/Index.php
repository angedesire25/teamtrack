<?php

namespace App\Livewire\Club\Finance\Subscriptions;

use App\Mail\SubscriptionReminderMail;
use App\Models\Player;
use App\Models\PlayerSubscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Mail;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

#[Layout('layouts.club')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $tab = 'subscriptions';
    #[Url]
    public string $season = '';
    public string $statusFilter = '';
    public string $search = '';

    // Modale de paiement
    public bool $showPaymentModal = false;
    public ?int $payingSubscriptionId = null;
    public string $payAmount = '';
    public string $payMethod = 'cash';
    public string $payDate = '';
    public string $payReference = '';
    public string $payNotes = '';
    public ?string $stripeUrl = null;

    // Modale de plan
    public bool $showPlanModal = false;
    public ?int $editingPlanId = null;
    public string $planName = '';
    public string $planSeason = '';
    public string $planAmount = '';
    public string $planFrequency = 'annual';
    public string $planDescription = '';
    public bool $planIsActive = true;

    // Affectation en masse
    public string $bulkPlanId = '';

    public function mount(): void
    {
        $this->season = SubscriptionPlan::currentSeason();
        $this->planSeason = $this->season;
    }

    public function updatedSearch(): void     { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }
    public function updatedSeason(): void    { $this->resetPage(); }

    // ── Payment ──────────────────────────────────────────────
    public function openPayment(int $id): void
    {
        $sub = PlayerSubscription::findOrFail($id);
        $this->payingSubscriptionId = $id;
        $this->payAmount    = $sub->amountRemaining();
        $this->payMethod    = 'cash';
        $this->payDate      = today()->format('Y-m-d');
        $this->payReference = '';
        $this->payNotes     = '';
        $this->stripeUrl    = $sub->stripe_checkout_url;
        $this->showPaymentModal = true;
    }

    public function savePayment(): void
    {
        $this->validate([
            'payAmount'    => 'required|integer|min:1',
            'payMethod'    => 'required|in:cash,mobile_money,bank_transfer,cheque,online',
            'payDate'      => 'required|date',
            'payReference' => 'nullable|string|max:100',
            'payNotes'     => 'nullable|string',
        ]);

        $sub = PlayerSubscription::findOrFail($this->payingSubscriptionId);

        SubscriptionPayment::create([
            'player_subscription_id' => $sub->id,
            'amount'       => $this->payAmount,
            'payment_date' => $this->payDate,
            'method'       => $this->payMethod,
            'reference'    => $this->payReference ?: null,
            'notes'        => $this->payNotes ?: null,
        ]);

        $sub->updateAmountAndStatus();
        $this->showPaymentModal = false;
    }

    public function generateStripeLink(int $id): void
    {
        $sub = PlayerSubscription::with('player')->findOrFail($id);
        $remaining = $sub->amountRemaining();
        if ($remaining <= 0) return;

        Stripe::setApiKey(config('services.stripe.secret'));

        $currency = config('services.stripe.currency', 'eur');
        // XOF et certaines devises sont sans décimales ; EUR/USD nécessitent une multiplication par 100
        $zeroDecimal = in_array(strtolower($currency), ['xof', 'xaf', 'jpy', 'krw']);
        $stripeAmount = $zeroDecimal ? $remaining : $remaining * 100;

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'mode'                 => 'payment',
            'line_items'           => [[
                'price_data' => [
                    'currency'     => $currency,
                    'product_data' => ['name' => 'Cotisation ' . $sub->season . ' — ' . $sub->player->fullName()],
                    'unit_amount'  => $stripeAmount,
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'type'                   => 'subscription',
                'player_subscription_id' => $sub->id,
                'tenant_id'              => app('tenant')->id,
            ],
            'success_url' => route('club.finance.subscriptions') . '?stripe_ok=1',
            'cancel_url'  => route('club.finance.subscriptions'),
        ]);

        $sub->update([
            'stripe_session_id'   => $session->id,
            'stripe_checkout_url' => $session->url,
        ]);

        $this->stripeUrl = $session->url;
    }

    public function markExempted(int $id): void
    {
        PlayerSubscription::findOrFail($id)->update(['status' => 'exempted']);
    }

    public function deleteSubscription(int $id): void
    {
        PlayerSubscription::findOrFail($id)->delete();
    }

    public function sendReminders(): void
    {
        $tenantId = app('tenant')->id;
        $subs = PlayerSubscription::where('tenant_id', $tenantId)
            ->whereNotIn('status', ['paid', 'exempted'])
            ->where(fn ($q) => $q->where('status', 'overdue')->orWhere('due_date', '<', now()))
            ->where(fn ($q) => $q->whereNull('last_reminder_at')->orWhere('last_reminder_at', '<', now()->subDays(7)))
            ->whereHas('player', fn ($p) => $p->whereNotNull('email'))
            ->with('player.tenant')
            ->get();

        foreach ($subs as $sub) {
            if ($sub->status === 'pending' && $sub->due_date->isPast()) {
                $sub->update(['status' => 'overdue']);
            }
            try {
                Mail::to($sub->player->email)->send(new SubscriptionReminderMail($sub));
                $sub->update(['last_reminder_at' => now()]);
            } catch (\Throwable) {}
        }

        session()->flash('message', $subs->count() . ' relance(s) envoyée(s).');
    }

    // ── Plans ─────────────────────────────────────────────────
    public function openCreatePlan(): void
    {
        $this->reset(['editingPlanId', 'planName', 'planAmount', 'planDescription']);
        $this->planSeason    = $this->season;
        $this->planFrequency = 'annual';
        $this->planIsActive  = true;
        $this->showPlanModal = true;
    }

    public function openEditPlan(int $id): void
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $this->editingPlanId    = $id;
        $this->planName         = $plan->name;
        $this->planSeason       = $plan->season;
        $this->planAmount       = $plan->amount;
        $this->planFrequency    = $plan->frequency;
        $this->planDescription  = $plan->description ?? '';
        $this->planIsActive     = $plan->is_active;
        $this->showPlanModal    = true;
    }

    public function savePlan(): void
    {
        $this->validate([
            'planName'     => 'required|string|max:100',
            'planSeason'   => 'required|string|max:10',
            'planAmount'   => 'required|integer|min:1',
            'planFrequency'=> 'required|in:one_time,monthly,quarterly,annual',
        ]);

        $tenantId = app('tenant')->id;
        $data = [
            'tenant_id'   => $tenantId,
            'name'        => $this->planName,
            'season'      => $this->planSeason,
            'amount'      => $this->planAmount,
            'frequency'   => $this->planFrequency,
            'description' => $this->planDescription ?: null,
            'is_active'   => $this->planIsActive,
        ];

        if ($this->editingPlanId) {
            SubscriptionPlan::findOrFail($this->editingPlanId)->update($data);
        } else {
            SubscriptionPlan::create($data);
        }

        $this->showPlanModal = false;
    }

    public function deletePlan(int $id): void
    {
        SubscriptionPlan::findOrFail($id)->delete();
    }

    public function bulkAssign(): void
    {
        if (!$this->bulkPlanId) return;

        $plan = SubscriptionPlan::findOrFail($this->bulkPlanId);
        $tenantId = app('tenant')->id;

        $existingPlayerIds = PlayerSubscription::where('tenant_id', $tenantId)
            ->where('plan_id', $plan->id)
            ->where('season', $this->season)
            ->pluck('player_id');

        $players = Player::where('tenant_id', $tenantId)
            ->whereIn('status', ['active', 'injured', 'suspended'])
            ->whereNotIn('id', $existingPlayerIds)
            ->get();

        foreach ($players as $player) {
            PlayerSubscription::create([
                'tenant_id'  => $tenantId,
                'player_id'  => $player->id,
                'plan_id'    => $plan->id,
                'season'     => $this->season,
                'amount_due' => $plan->amount,
                'due_date'   => now()->endOfYear(),
                'status'     => 'pending',
            ]);
        }

        session()->flash('message', $players->count() . ' joueur(s) assigné(s) au plan.');
    }

    public function render()
    {
        $tenantId = app('tenant')->id;

        $subscriptions = PlayerSubscription::where('tenant_id', $tenantId)
            ->where('season', $this->season)
            ->with('player', 'plan', 'payments')
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, fn ($q) => $q->whereHas('player', fn ($p) =>
                $p->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")))
            ->orderByRaw("FIELD(status, 'overdue', 'partial', 'pending', 'paid', 'exempted')")
            ->paginate(20);

        $stats = [
            'total_due'     => PlayerSubscription::where('tenant_id', $tenantId)->where('season', $this->season)->sum('amount_due'),
            'total_paid'    => PlayerSubscription::where('tenant_id', $tenantId)->where('season', $this->season)->sum('amount_paid'),
            'overdue_count' => PlayerSubscription::where('tenant_id', $tenantId)->where('season', $this->season)->where('status', 'overdue')->count(),
            'paid_count'    => PlayerSubscription::where('tenant_id', $tenantId)->where('season', $this->season)->where('status', 'paid')->count(),
        ];

        $totalActivePlayers  = Player::where('tenant_id', $tenantId)->whereIn('status', ['active','injured','suspended'])->count();
        $subscribedPlayers   = PlayerSubscription::where('tenant_id', $tenantId)->where('season', $this->season)->distinct('player_id')->count('player_id');
        $unsubscribedPlayers = max(0, $totalActivePlayers - $subscribedPlayers);

        $plans = SubscriptionPlan::where('tenant_id', $tenantId)->orderByDesc('created_at')->get();

        $seasons = PlayerSubscription::where('tenant_id', $tenantId)->distinct()->orderByDesc('season')->pluck('season')->unique();

        return view('livewire.club.finance.subscriptions.index', compact(
            'subscriptions', 'stats', 'unsubscribedPlayers', 'plans', 'seasons'
        ));
    }
}

<?php

namespace App\Livewire\SuperAdmin\Clubs;

use App\Mail\ClubNotificationMail;
use App\Models\Document;
use App\Models\Event;
use App\Models\Expense;
use App\Models\Injury;
use App\Models\Plan;
use App\Models\Player;
use App\Models\Tenant;
use App\Models\TenantActivityLog;
use App\Models\Transfer;
use App\Notifications\UserAuthActivity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.superadmin')]
class Show extends Component
{
    public Tenant $tenant;

    // Modale de suspension
    public bool   $showSuspendModal = false;
    public string $suspendReason    = '';

    // Modale de changement de plan
    public bool $showPlanModal = false;
    public int  $newPlanId    = 0;

    // Modale d'envoi d'email
    public bool   $showEmailModal = false;
    public string $emailSubject   = '';
    public string $emailBody      = '';

    public function mount(Tenant $tenant): void
    {
        $this->tenant = $tenant->load(['plan', 'users.roles', 'payments']);

        // Enregistrement automatique du premier log si absent
        if (! $tenant->activityLogs()->exists()) {
            TenantActivityLog::log(
                $tenant->id,
                'created',
                'Club créé sur la plateforme.',
                [],
                null
            );
            // Forcer la date du log à celle de la création du tenant
            $tenant->activityLogs()->latest()->first()?->update(['created_at' => $tenant->created_at]);
        }
    }

    // ── Suspension ───────────────────────────────────────────────────────────

    public function openSuspendModal(): void
    {
        $this->suspendReason   = '';
        $this->showSuspendModal = true;
    }

    public function doSuspend(): void
    {
        $this->validate(['suspendReason' => 'required|string|max:500']);

        $this->tenant->update(['status' => 'suspended', 'suspended_at' => now()]);

        TenantActivityLog::log(
            $this->tenant->id,
            'suspended',
            'Club suspendu. Motif : '.$this->suspendReason,
            ['reason' => $this->suspendReason],
        );

        $this->showSuspendModal = false;
        $this->tenant->refresh();
        $this->dispatch('toast', message: 'Club suspendu.', type: 'success');
    }

    public function activate(): void
    {
        $this->tenant->update(['status' => 'active', 'suspended_at' => null]);

        TenantActivityLog::log($this->tenant->id, 'activated', 'Club réactivé.');

        $this->tenant->refresh();
        $this->dispatch('toast', message: 'Club réactivé.', type: 'success');
    }

    // ── Changement de plan ───────────────────────────────────────────────────

    public function openPlanModal(): void
    {
        $this->newPlanId     = $this->tenant->plan_id ?? 0;
        $this->showPlanModal = true;
    }

    public function doChangePlan(): void
    {
        $this->validate(['newPlanId' => 'required|exists:plans,id']);

        $oldPlan = $this->tenant->plan?->name ?? '—';
        $newPlan = Plan::find($this->newPlanId)?->name ?? '—';

        $this->tenant->update(['plan_id' => $this->newPlanId]);

        TenantActivityLog::log(
            $this->tenant->id,
            'plan_changed',
            "Plan modifié : {$oldPlan} → {$newPlan}",
            ['old_plan' => $oldPlan, 'new_plan' => $newPlan],
        );

        $this->showPlanModal = false;
        $this->tenant->refresh()->load('plan');
        $this->dispatch('toast', message: 'Plan mis à jour.', type: 'success');
    }

    // ── Envoi d'email ────────────────────────────────────────────────────────

    public function openEmailModal(): void
    {
        $this->emailSubject   = '';
        $this->emailBody      = '';
        $this->showEmailModal = true;
    }

    public function doSendEmail(): void
    {
        $this->validate([
            'emailSubject' => 'required|string|max:200',
            'emailBody'    => 'required|string|max:5000',
        ]);

        Mail::to($this->tenant->email)->send(new ClubNotificationMail(
            clubName: $this->tenant->name,
            subject:  $this->emailSubject,
            body:     $this->emailBody,
        ));

        TenantActivityLog::log(
            $this->tenant->id,
            'email_sent',
            'Email envoyé : '.$this->emailSubject,
            ['subject' => $this->emailSubject, 'to' => $this->tenant->email],
        );

        $this->showEmailModal = false;
        $this->dispatch('toast', message: 'Email envoyé.', type: 'success');
    }

    // ── Données calculées ────────────────────────────────────────────────────

    #[Computed]
    public function stats(): array
    {
        $id = $this->tenant->id;
        return [
            'joueurs'      => Player::where('tenant_id', $id)->count(),
            'categories'   => $this->tenant->categories()->count(),
            'equipes'      => $this->tenant->teams()->count(),
            'utilisateurs' => $this->tenant->users()->count(),
            'events'       => Event::where('tenant_id', $id)->count(),
            'documents'    => Document::where('tenant_id', $id)->count(),
        ];
    }

    #[Computed]
    public function usageStats(): array
    {
        $id = $this->tenant->id;

        // Connexions ce mois via la table notifications
        $userIds = $this->tenant->users()->pluck('id');
        $loginsThisMonth = \DB::table('notifications')
            ->where('type', UserAuthActivity::class)
            ->whereIn('notifiable_id', $userIds)
            ->whereJsonContains('data->event', 'login')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Activité récente par module (30 derniers jours)
        $since = now()->subDays(30);
        $modules = [
            'Joueurs'    => Player::where('tenant_id', $id)->where('created_at', '>=', $since)->count(),
            'Planning'   => Event::where('tenant_id', $id)->where('created_at', '>=', $since)->count(),
            'Finances'   => Expense::where('tenant_id', $id)->where('created_at', '>=', $since)->count(),
            'Transferts' => Transfer::where('tenant_id', $id)->where('created_at', '>=', $since)->count(),
            'Médical'    => Injury::where('tenant_id', $id)->where('created_at', '>=', $since)->count(),
            'Documents'  => Document::where('tenant_id', $id)->where('created_at', '>=', $since)->count(),
        ];

        arsort($modules);

        return [
            'logins_this_month'  => $loginsThisMonth,
            'players_this_month' => Player::where('tenant_id', $id)->where('created_at', '>=', $since)->count(),
            'matches_this_month' => Event::where('tenant_id', $id)->where('type', 'match')->where('starts_at', '>=', $since)->count(),
            'modules'            => $modules,
            'total_logins'       => \DB::table('notifications')
                ->where('type', UserAuthActivity::class)
                ->whereIn('notifiable_id', $userIds)
                ->whereJsonContains('data->event', 'login')
                ->count(),
        ];
    }

    #[Computed]
    public function payments(): Collection
    {
        return $this->tenant->payments()->latest()->get();
    }

    #[Computed]
    public function users(): Collection
    {
        return $this->tenant->users()->with('roles')->orderBy('name')->get();
    }

    #[Computed]
    public function plans(): Collection
    {
        return Plan::orderBy('price')->get();
    }

    #[Computed]
    public function timeline(): Collection
    {
        // Logs d'activité + paiements fusionnés et triés par date desc
        $logs = $this->tenant->activityLogs()
            ->with('createdBy')
            ->latest('created_at')
            ->get()
            ->map(fn($log) => [
                'date'        => $log->created_at,
                'description' => $log->description,
                'icon'        => $log->icon(),
                'color'       => $log->color(),
                'by'          => $log->createdBy?->name,
                'meta'        => $log->meta,
            ]);

        $paymentLogs = $this->tenant->payments()
            ->whereNotNull('paid_at')
            ->latest('paid_at')
            ->get()
            ->map(fn($p) => [
                'date'        => $p->paid_at,
                'description' => 'Paiement reçu : '.number_format($p->amount / 100, 0, ',', ' ').' '
                                 .strtoupper($p->currency ?? 'XOF'),
                'icon'        => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                'color'       => 'text-emerald-600 bg-emerald-50',
                'by'          => null,
                'meta'        => null,
            ]);

        return $logs->concat($paymentLogs)->sortByDesc('date')->values();
    }

    #[Computed]
    public function emailHistory(): Collection
    {
        return $this->tenant->activityLogs()
            ->where('event_type', 'email_sent')
            ->with('createdBy')
            ->latest('created_at')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.super-admin.clubs.show', [
            'pageTitle'    => $this->tenant->name,
            'stats'        => $this->stats,
            'usageStats'   => $this->usageStats,
            'payments'     => $this->payments,
            'users'        => $this->users,
            'plans'        => $this->plans,
            'timeline'     => $this->timeline,
            'emailHistory' => $this->emailHistory,
        ]);
    }
}

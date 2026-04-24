<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionReminderMail;
use App\Models\PlayerSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionReminders extends Command
{
    protected $signature   = 'finance:send-reminders {--tenant= : Restreindre à un tenant spécifique par son ID}';
    protected $description = 'Envoie des relances par e-mail pour les cotisations en retard ou bientôt échues';

    public function handle(): void
    {
        $query = PlayerSubscription::with(['player', 'player.tenant'])
            ->whereNotIn('status', ['paid', 'exempted'])
            ->where(function ($q) {
                $q->where('status', 'overdue')
                  ->orWhere('due_date', '<', now());
            })
            ->where(function ($q) {
                $q->whereNull('last_reminder_at')
                  ->orWhere('last_reminder_at', '<', now()->subDays(7));
            })
            ->whereHas('player', fn ($p) => $p->whereNotNull('email'));

        if ($tenantId = $this->option('tenant')) {
            $query->where('tenant_id', $tenantId);
        }

        $subscriptions = $query->get();

        $this->info("Sending reminders for {$subscriptions->count()} subscription(s)…");

        foreach ($subscriptions as $sub) {
            // Passer en retard si toujours en attente
            if ($sub->status === 'pending' && $sub->due_date->isPast()) {
                $sub->update(['status' => 'overdue']);
                $sub->refresh();
            }

            try {
                Mail::to($sub->player->email)->send(new SubscriptionReminderMail($sub));
                $sub->update(['last_reminder_at' => now()]);
                $this->line("  ✓ {$sub->player->email}");
            } catch (\Throwable $e) {
                $this->error("  ✗ {$sub->player->email}: " . $e->getMessage());
            }
        }

        $this->info('Done.');
    }
}

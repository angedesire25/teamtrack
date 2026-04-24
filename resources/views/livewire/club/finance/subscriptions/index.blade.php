<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('club.finance.dashboard') }}" wire:navigate class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-extrabold text-gray-900">Cotisations</h1>
            <p class="text-gray-500 text-sm mt-0.5">Suivi des paiements joueurs</p>
        </div>
        <div class="flex gap-2">
            @if(session('message'))
            <span class="px-3 py-2 text-xs font-semibold bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200">{{ session('message') }}</span>
            @endif
            <button wire:click="sendReminders" wire:confirm="Envoyer les relances aux joueurs en retard ?"
                    class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Relances
            </button>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 mb-5 border-b border-gray-200">
        @foreach(['subscriptions' => 'Cotisations', 'plans' => 'Plans de cotisation'] as $key => $label)
        <button wire:click="$set('tab','{{ $key }}')"
                class="px-4 py-2.5 text-sm font-semibold border-b-2 -mb-px transition-colors
                       {{ $tab === $key ? 'border-[--club-primary] text-[#1E3A5F]' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
                style="{{ $tab === $key ? 'border-color: var(--club-primary);' : '' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    @if($tab === 'subscriptions')
    {{-- ── KPIs ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
        @php
            $rate = $stats['total_due'] > 0 ? round($stats['total_paid']/$stats['total_due']*100) : 0;
        @endphp
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-xl font-extrabold text-gray-900">{{ number_format($stats['total_due'],0,'.',' ') }} F</p>
            <p class="text-xs text-gray-500 font-semibold mt-0.5">Total dû</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-xl font-extrabold text-emerald-600">{{ number_format($stats['total_paid'],0,'.',' ') }} F</p>
            <p class="text-xs text-gray-500 font-semibold mt-0.5">Collecté · {{ $rate }}%</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-xl font-extrabold text-red-600">{{ $stats['overdue_count'] }}</p>
            <p class="text-xs text-gray-500 font-semibold mt-0.5">En retard</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-xl font-extrabold text-emerald-600">{{ $stats['paid_count'] }}</p>
            <p class="text-xs text-gray-500 font-semibold mt-0.5">Payé intégralement</p>
        </div>
    </div>

    @if($unsubscribedPlayers > 0)
    <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-4 flex items-center justify-between gap-3">
        <p class="text-sm text-amber-700 font-semibold">{{ $unsubscribedPlayers }} joueur(s) actif(s) sans cotisation pour {{ $season }}</p>
        <div class="flex items-center gap-2 flex-shrink-0">
            <select wire:model="bulkPlanId" class="text-xs border border-amber-300 rounded-lg px-2 py-1.5 bg-white text-gray-700">
                <option value="">Choisir un plan…</option>
                @foreach($plans->where('season', $season)->where('is_active', true) as $plan)
                <option value="{{ $plan->id }}">{{ $plan->name }} — {{ number_format($plan->amount,0,'.',' ') }} F</option>
                @endforeach
            </select>
            <button wire:click="bulkAssign" wire:confirm="Assigner ce plan à tous les joueurs actifs sans cotisation ?"
                    class="text-xs font-bold px-3 py-1.5 bg-amber-600 text-white rounded-lg hover:bg-amber-700">
                Assigner
            </button>
        </div>
    </div>
    @endif

    {{-- Filtres --}}
    <div class="flex flex-wrap gap-3 mb-4">
        <div class="relative flex-1 min-w-48">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Rechercher un joueur…"
                   class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
        </div>
        <select wire:model.live="statusFilter" class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none bg-white">
            <option value="">Tous les statuts</option>
            <option value="overdue">En retard</option>
            <option value="partial">Partiel</option>
            <option value="pending">En attente</option>
            <option value="paid">Payé</option>
            <option value="exempted">Exonéré</option>
        </select>
        <select wire:model.live="season" class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none bg-white font-semibold text-gray-700">
            @foreach($seasons->prepend(\App\Models\SubscriptionPlan::currentSeason())->unique() as $s)
            <option value="{{ $s }}">{{ $s }}</option>
            @endforeach
        </select>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden" x-data="{ expanded: null }">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Joueur</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Plan</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Dû</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Payé</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Reste</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Échéance</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($subscriptions as $sub)
                <tr class="hover:bg-gray-50 cursor-pointer" @click="expanded === {{ $sub->id }} ? expanded = null : expanded = {{ $sub->id }}">
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-gray-900">{{ $sub->player->fullName() }}</p>
                        <p class="text-xs text-gray-400">{{ $sub->player->position ?? '' }}</p>
                    </td>
                    <td class="px-4 py-3.5 hidden sm:table-cell text-xs text-gray-500">{{ $sub->plan?->name ?? '—' }}</td>
                    <td class="px-4 py-3.5">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                            {{ $sub->status === 'overdue'  ? 'bg-red-50 text-red-700' : '' }}
                            {{ $sub->status === 'partial'  ? 'bg-amber-50 text-amber-700' : '' }}
                            {{ $sub->status === 'pending'  ? 'bg-gray-100 text-gray-600' : '' }}
                            {{ $sub->status === 'paid'     ? 'bg-emerald-50 text-emerald-700' : '' }}
                            {{ $sub->status === 'exempted' ? 'bg-blue-50 text-blue-700' : '' }}">
                            {{ $sub->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-right font-semibold text-gray-700">{{ number_format($sub->amount_due,0,'.',' ') }} F</td>
                    <td class="px-4 py-3.5 text-right font-semibold text-emerald-600 hidden md:table-cell">{{ number_format($sub->amount_paid,0,'.',' ') }} F</td>
                    <td class="px-4 py-3.5 text-right font-bold {{ $sub->amountRemaining() > 0 ? 'text-red-600' : 'text-gray-400' }} hidden md:table-cell">
                        {{ number_format($sub->amountRemaining(),0,'.',' ') }} F
                    </td>
                    <td class="px-4 py-3.5 text-xs text-gray-400 hidden lg:table-cell">{{ $sub->due_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3.5" @click.stop>
                        <div class="flex gap-1.5 justify-end">
                            @if($sub->status !== 'paid' && $sub->status !== 'exempted')
                            <button wire:click="openPayment({{ $sub->id }})"
                                    class="p-1.5 text-emerald-500 hover:bg-emerald-50 rounded-lg" title="Enregistrer un paiement">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                            @endif
                            @if($sub->status !== 'paid' && $sub->status !== 'exempted')
                            <button wire:click="markExempted({{ $sub->id }})" title="Exonérer"
                                    class="p-1.5 text-blue-400 hover:bg-blue-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </button>
                            @endif
                            <button wire:click="deleteSubscription({{ $sub->id }})" wire:confirm="Supprimer cette cotisation ?"
                                    class="p-1.5 text-gray-300 hover:text-red-400 hover:bg-red-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                {{-- Développé : historique des paiements --}}
                <tr x-show="expanded === {{ $sub->id }}" x-cloak>
                    <td colspan="8" class="bg-gray-50 px-6 py-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Historique des paiements</p>
                        @if($sub->payments->isEmpty())
                        <p class="text-sm text-gray-400">Aucun paiement enregistré</p>
                        @else
                        <div class="space-y-1.5">
                            @foreach($sub->payments->sortByDesc('payment_date') as $pay)
                            <div class="flex items-center gap-3 text-sm">
                                <span class="text-gray-400 text-xs w-20 flex-shrink-0">{{ $pay->payment_date->format('d/m/Y') }}</span>
                                <span class="font-bold text-emerald-600">{{ number_format($pay->amount,0,'.',' ') }} F</span>
                                <span class="text-xs text-gray-400">{{ $pay->methodLabel() }}</span>
                                @if($pay->reference)<span class="text-xs text-gray-300">{{ $pay->reference }}</span>@endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400">Aucune cotisation pour cette saison</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($subscriptions->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $subscriptions->links() }}</div>
        @endif
    </div>

    @elseif($tab === 'plans')
    {{-- ── Plans tab ── --}}
    <div class="flex justify-end mb-4">
        <button wire:click="openCreatePlan"
                class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl"
                style="background-color: var(--club-primary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau plan
        </button>
    </div>

    <div class="space-y-3">
        @forelse($plans as $plan)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 {{ !$plan->is_active ? 'opacity-60' : '' }}">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-bold text-gray-900">{{ $plan->name }}</h3>
                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $plan->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $plan->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-violet-50 text-violet-700 font-semibold">{{ $plan->frequencyLabel() }}</span>
                    </div>
                    <p class="text-sm text-gray-500">
                        Saison {{ $plan->season }} · <strong class="text-gray-900">{{ number_format($plan->amount,0,'.',' ') }} F</strong>
                        @if($plan->description) · {{ $plan->description }}@endif
                    </p>
                    <p class="text-xs text-gray-400 mt-1">{{ $plan->playerSubscriptions()->count() }} joueur(s) assigné(s)</p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <button wire:click="openEditPlan({{ $plan->id }})" class="p-1.5 text-gray-400 hover:text-[#1E3A5F] hover:bg-gray-100 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                    <button wire:click="deletePlan({{ $plan->id }})" wire:confirm="Supprimer ce plan ?" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center">
            <p class="text-gray-500">Aucun plan créé</p>
            <button wire:click="openCreatePlan" class="mt-2 text-sm font-semibold text-[#1E3A5F] hover:underline">Créer le premier →</button>
        </div>
        @endforelse
    </div>
    @endif

    {{-- Modal Paiement --}}
    @if($showPaymentModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Enregistrer un paiement</h3>
                <button wire:click="$set('showPaymentModal',false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="savePayment" class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Montant (F) *</label>
                        <input wire:model="payAmount" type="number" min="1" autofocus
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        @error('payAmount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Date *</label>
                        <input wire:model="payDate" type="date"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mode de paiement</label>
                    <select wire:model="payMethod" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F] bg-white">
                        <option value="cash">Espèces</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="bank_transfer">Virement bancaire</option>
                        <option value="cheque">Chèque</option>
                        <option value="online">En ligne (Stripe)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Référence (optionnel)</label>
                    <input wire:model="payReference" type="text" placeholder="N° de transaction, reçu…"
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Notes</label>
                    <textarea wire:model="payNotes" rows="2"
                              class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"></textarea>
                </div>

                @if(config('services.stripe.secret'))
                <div class="border-t border-gray-100 pt-4">
                    @if($stripeUrl)
                    <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                        <p class="text-xs font-semibold text-blue-700 mb-1.5">Lien Stripe généré :</p>
                        <div class="flex gap-2">
                            <input type="text" readonly value="{{ $stripeUrl }}"
                                   class="flex-1 text-xs px-2 py-1.5 border border-gray-200 rounded-lg bg-white text-gray-600 truncate">
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $stripeUrl }}')"
                                    class="px-2.5 py-1.5 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Copier
                            </button>
                        </div>
                    </div>
                    @else
                    <button type="button" wire:click="generateStripeLink({{ $payingSubscriptionId }})"
                            class="w-full py-2.5 text-xs font-semibold text-[#1E3A5F] border border-[#1E3A5F]/30 rounded-xl hover:bg-blue-50">
                        🔗 Générer un lien de paiement Stripe
                    </button>
                    @endif
                </div>
                @endif

                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showPaymentModal',false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal Plan --}}
    @if($showPlanModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">{{ $editingPlanId ? 'Modifier le plan' : 'Nouveau plan de cotisation' }}</h3>
                <button wire:click="$set('showPlanModal',false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="savePlan" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom du plan *</label>
                    <input wire:model="planName" type="text" autofocus placeholder="Ex : Cotisation annuelle 2025-26"
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    @error('planName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Saison *</label>
                        <input wire:model="planSeason" type="text" placeholder="2025-26"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Montant (F) *</label>
                        <input wire:model="planAmount" type="number" min="1" step="500"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        @error('planAmount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Fréquence</label>
                    <select wire:model="planFrequency" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F] bg-white">
                        <option value="one_time">Unique</option>
                        <option value="monthly">Mensuel</option>
                        <option value="quarterly">Trimestriel</option>
                        <option value="annual">Annuel</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                    <textarea wire:model="planDescription" rows="2"
                              class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"></textarea>
                </div>
                <div class="flex items-center gap-3">
                    <input wire:model="planIsActive" id="plan_active" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#1E3A5F] focus:ring-[#1E3A5F]/30">
                    <label for="plan_active" class="text-sm font-semibold text-gray-700">Plan actif</label>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showPlanModal',false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">{{ $editingPlanId ? 'Enregistrer' : 'Créer' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

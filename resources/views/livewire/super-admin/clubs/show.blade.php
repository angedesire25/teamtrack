{{-- Fiche détaillée d'un club — Super Admin --}}

<x-slot:pageTitle>{{ $tenant->name }}</x-slot:pageTitle>

<div x-data="{ tab: 'info' }" class="space-y-6">

    {{-- ── Barre de navigation + actions ───────────────────────────────────── --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <a href="{{ route('superadmin.clubs.index') }}"
           class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour à la liste
        </a>

        <div class="flex items-center gap-2 flex-wrap">
            {{-- Changer de plan --}}
            <button wire:click="openPlanModal"
                    class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Changer de plan
            </button>

            {{-- Envoyer un email --}}
            <button wire:click="openEmailModal"
                    class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Envoyer un email
            </button>

            {{-- Suspendre / Réactiver --}}
            @if ($tenant->isActive())
                <button wire:click="openSuspendModal"
                        class="px-3 py-1.5 text-sm font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    Suspendre
                </button>
            @else
                <button wire:click="activate"
                        class="px-3 py-1.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Réactiver
                </button>
            @endif

            {{-- Impersonation --}}
            <form method="POST" action="{{ route('superadmin.clubs.impersonate', $tenant) }}">
                @csrf
                <button type="submit"
                        class="px-3 py-1.5 text-sm font-medium text-white bg-[#1E3A5F] rounded-lg hover:bg-[#162d4a] transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Accéder au club
                </button>
            </form>
        </div>
    </div>

    {{-- ── En-tête club ─────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0"
             style="background-color: {{ $tenant->primary_color ?? '#1E3A5F' }}">
            {{ strtoupper(substr($tenant->name, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-lg font-bold text-gray-900">{{ $tenant->name }}</h2>
                @php
                    [$badgeClass, $badgeLabel] = match($tenant->status) {
                        'active'    => ['bg-emerald-100 text-emerald-700', 'Actif'],
                        'trial'     => ['bg-blue-100 text-blue-700', 'Essai'],
                        'suspended' => ['bg-red-100 text-red-700', 'Suspendu'],
                        'cancelled' => ['bg-gray-100 text-gray-600', 'Annulé'],
                        default     => ['bg-gray-100 text-gray-600', $tenant->status],
                    };
                @endphp
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $badgeClass }}">{{ $badgeLabel }}</span>
                @if ($tenant->plan)
                    <span class="text-xs font-medium text-purple-700 bg-purple-50 px-2.5 py-0.5 rounded-full">{{ $tenant->plan->name }}</span>
                @endif
            </div>
            <p class="text-sm text-gray-400 mt-0.5">{{ $tenant->subdomain }}.teamtrack.app · Inscrit le {{ $tenant->created_at->format('d/m/Y') }}</p>
        </div>
    </div>

    {{-- ── Onglets ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Navigation onglets --}}
        <div class="border-b border-gray-100 flex overflow-x-auto">
            @foreach ([
                ['info',   'Informations'],
                ['usage',  'Statistiques d\'usage'],
                ['pay',    'Paiements'],
                ['comms',  'Communications'],
                ['timeline','Timeline'],
            ] as [$key, $label])
                <button @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}'
                            ? 'border-b-2 border-[#1E3A5F] text-[#1E3A5F] font-semibold'
                            : 'text-gray-500 hover:text-gray-700'"
                        class="px-5 py-3.5 text-sm whitespace-nowrap transition-colors">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- ── Onglet : Informations ──────────────────────────────────────────── --}}
        <div x-show="tab === 'info'" class="p-6 grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Coordonnées --}}
            <div class="xl:col-span-2 space-y-4">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Coordonnées</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-gray-400">Email</span>
                        <p class="mt-0.5 text-gray-800">{{ $tenant->email }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400">Téléphone</span>
                        <p class="mt-0.5 text-gray-800">{{ $tenant->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400">Ville / Pays</span>
                        <p class="mt-0.5 text-gray-800">{{ $tenant->city ?? '—' }}, {{ $tenant->country ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400">Sous-domaine</span>
                        <p class="mt-0.5 text-gray-800">{{ $tenant->subdomain }}.teamtrack.app</p>
                    </div>
                    @if ($tenant->trial_ends_at)
                        <div>
                            <span class="text-xs text-gray-400">Fin d'essai</span>
                            <p class="mt-0.5 text-gray-800">{{ $tenant->trial_ends_at->format('d/m/Y') }}</p>
                        </div>
                    @endif
                    @if ($tenant->suspended_at)
                        <div>
                            <span class="text-xs text-gray-400">Suspendu le</span>
                            <p class="mt-0.5 text-red-600 font-medium">{{ $tenant->suspended_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>

                {{-- KPIs du club --}}
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide pt-2">Données du club</h3>
                <div class="grid grid-cols-3 gap-3">
                    @foreach ([
                        ['Joueurs',      $stats['joueurs'],      'text-purple-600', 'bg-purple-50'],
                        ['Catégories',   $stats['categories'],   'text-blue-600',   'bg-blue-50'],
                        ['Équipes',      $stats['equipes'],      'text-emerald-600','bg-emerald-50'],
                        ['Utilisateurs', $stats['utilisateurs'], 'text-gray-700',   'bg-gray-50'],
                        ['Événements',   $stats['events'],       'text-orange-600', 'bg-orange-50'],
                        ['Documents',    $stats['documents'],    'text-indigo-600', 'bg-indigo-50'],
                    ] as [$label, $value, $tc, $bg])
                        <div class="{{ $bg }} rounded-lg p-3 text-center">
                            <p class="text-xl font-bold {{ $tc }}">{{ $value }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Utilisateurs --}}
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Utilisateurs</h3>
                @if ($users->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-6">Aucun utilisateur.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($users as $user)
                            <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                                <div class="w-8 h-8 rounded-full bg-[#2E75B6] flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                                </div>
                                <span class="text-xs text-gray-400">{{ $user->roles->first()?->name ?? '—' }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- ── Onglet : Statistiques d'usage ─────────────────────────────────── --}}
        <div x-show="tab === 'usage'" x-cloak class="p-6 space-y-6">

            {{-- KPI cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-xl p-5 text-center">
                    <p class="text-3xl font-bold text-blue-700">{{ $usageStats['logins_this_month'] }}</p>
                    <p class="text-sm text-blue-600 mt-1">Connexions ce mois</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-5 text-center">
                    <p class="text-3xl font-bold text-purple-700">{{ $usageStats['players_this_month'] }}</p>
                    <p class="text-sm text-purple-600 mt-1">Joueurs ajoutés (30 j)</p>
                </div>
                <div class="bg-emerald-50 rounded-xl p-5 text-center">
                    <p class="text-3xl font-bold text-emerald-700">{{ $usageStats['matches_this_month'] }}</p>
                    <p class="text-sm text-emerald-600 mt-1">Matchs planifiés (30 j)</p>
                </div>
            </div>

            {{-- Activité par module --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Activité par module — 30 derniers jours</h3>
                @php $maxActivity = max(1, max($usageStats['modules'])); @endphp
                <div class="space-y-3">
                    @foreach ($usageStats['modules'] as $module => $count)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">{{ $module }}</span>
                                <span class="font-semibold text-gray-800">{{ $count }}</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#1E3A5F] rounded-full transition-all"
                                     style="width: {{ round($count / $maxActivity * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Total connexions --}}
            <p class="text-xs text-gray-400">
                Total historique des connexions : <span class="font-semibold text-gray-600">{{ $usageStats['total_logins'] }}</span>
            </p>

        </div>

        {{-- ── Onglet : Paiements ─────────────────────────────────────────────── --}}
        <div x-show="tab === 'pay'" x-cloak class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Historique des paiements</h3>
                <a href="{{ route('superadmin.finance.invoices') }}?tenant={{ $tenant->id }}"
                   class="text-xs text-[#1E3A5F] hover:underline">Voir tout</a>
            </div>

            @if ($payments->isEmpty())
                <p class="text-sm text-gray-400 text-center py-12">Aucun paiement enregistré.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs font-medium text-gray-400 uppercase tracking-wide border-b border-gray-100">
                                <th class="pb-2 text-left">Référence</th>
                                <th class="pb-2 text-left">Montant</th>
                                <th class="pb-2 text-left">Date</th>
                                <th class="pb-2 text-left">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="py-3 text-gray-600 font-mono text-xs">{{ $payment->reference }}</td>
                                    <td class="py-3 font-semibold text-gray-800">
                                        {{ number_format($payment->amount / 100, 0, ',', ' ') }}
                                        <span class="text-xs text-gray-400">{{ strtoupper($payment->currency ?? 'XOF') }}</span>
                                    </td>
                                    <td class="py-3 text-gray-500">{{ $payment->paid_at?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="py-3">
                                        @php
                                            [$pc, $pl] = match($payment->status) {
                                                'paid'    => ['bg-emerald-100 text-emerald-700', 'Payé'],
                                                'pending' => ['bg-amber-100 text-amber-700', 'En attente'],
                                                'failed'  => ['bg-red-100 text-red-700', 'Échoué'],
                                                default   => ['bg-gray-100 text-gray-600', $payment->status],
                                            };
                                        @endphp
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $pc }}">{{ $pl }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ── Onglet : Communications ────────────────────────────────────────── --}}
        <div x-show="tab === 'comms'" x-cloak class="p-6 space-y-6">

            {{-- Bouton envoyer --}}
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Emails envoyés à ce club</h3>
                <button wire:click="openEmailModal"
                        class="px-3 py-1.5 text-sm font-medium text-white bg-[#1E3A5F] rounded-lg hover:bg-[#162d4a] transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nouvel email
                </button>
            </div>

            @if ($emailHistory->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-400">Aucun email envoyé pour l'instant.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($emailHistory as $log)
                        <div class="border border-gray-100 rounded-lg p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">
                                        {{ $log->meta['subject'] ?? $log->description }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        À : {{ $log->meta['to'] ?? '—' }}
                                        @if ($log->createdBy)
                                            · Par {{ $log->createdBy->name }}
                                        @endif
                                    </p>
                                </div>
                                <time class="text-xs text-gray-400 whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </time>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Onglet : Timeline ──────────────────────────────────────────────── --}}
        <div x-show="tab === 'timeline'" x-cloak class="p-6">
            @if ($timeline->isEmpty())
                <p class="text-sm text-gray-400 text-center py-12">Aucune activité enregistrée.</p>
            @else
                <ol class="relative border-l border-gray-100 space-y-0 ml-3">
                    @foreach ($timeline as $entry)
                        <li class="mb-6 ml-6">
                            <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full {{ $entry['color'] }} ring-4 ring-white">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $entry['icon'] }}"/>
                                </svg>
                            </span>
                            <div class="ml-2">
                                <p class="text-sm text-gray-800">{{ $entry['description'] }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($entry['date'])->format('d/m/Y H:i') }}
                                    @if ($entry['by'])
                                        · <span class="font-medium">{{ $entry['by'] }}</span>
                                    @endif
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>

    </div>{{-- /.bg-white onglets --}}

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL : Suspension                                                     --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @teleport('body')
    <div x-data x-show="$wire.showSuspendModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div @click.outside="$wire.showSuspendModal = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
            <h3 class="text-base font-bold text-gray-900">Suspendre le club</h3>
            <p class="text-sm text-gray-500">Indiquez le motif de suspension. Le club ne pourra plus se connecter.</p>
            <div>
                <textarea wire:model="suspendReason" rows="4"
                          placeholder="Motif de suspension…"
                          class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent resize-none"></textarea>
                @error('suspendReason') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex justify-end gap-3 pt-1">
                <button wire:click="$set('showSuspendModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Annuler
                </button>
                <button wire:click="doSuspend" wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 transition-colors">
                    <span wire:loading.remove wire:target="doSuspend">Suspendre</span>
                    <span wire:loading wire:target="doSuspend">En cours…</span>
                </button>
            </div>
        </div>
    </div>
    @endteleport

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL : Changement de plan                                             --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @teleport('body')
    <div x-data x-show="$wire.showPlanModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div @click.outside="$wire.showPlanModal = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 space-y-4">
            <h3 class="text-base font-bold text-gray-900">Changer de plan</h3>
            <div>
                <label class="text-xs font-medium text-gray-500">Nouveau plan</label>
                <select wire:model="newPlanId"
                        class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent">
                    <option value="0">— Sélectionner —</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}">{{ $plan->name }} — {{ number_format($plan->price / 100, 0, ',', ' ') }} XOF</option>
                    @endforeach
                </select>
                @error('newPlanId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex justify-end gap-3 pt-1">
                <button wire:click="$set('showPlanModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Annuler
                </button>
                <button wire:click="doChangePlan" wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-medium text-white bg-[#1E3A5F] rounded-lg hover:bg-[#162d4a] transition-colors">
                    <span wire:loading.remove wire:target="doChangePlan">Confirmer</span>
                    <span wire:loading wire:target="doChangePlan">En cours…</span>
                </button>
            </div>
        </div>
    </div>
    @endteleport

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL : Envoi d'email                                                  --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @teleport('body')
    <div x-data x-show="$wire.showEmailModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div @click.outside="$wire.showEmailModal = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4">
            <h3 class="text-base font-bold text-gray-900">Envoyer un email à {{ $tenant->name }}</h3>
            <div>
                <label class="text-xs font-medium text-gray-500">Objet</label>
                <input wire:model="emailSubject" type="text"
                       placeholder="Objet de l'email…"
                       class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent">
                @error('emailSubject') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500">Message</label>
                <textarea wire:model="emailBody" rows="6"
                          placeholder="Contenu de l'email…"
                          class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent resize-none"></textarea>
                @error('emailBody') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <p class="text-xs text-gray-400">Destinataire : {{ $tenant->email }}</p>
            <div class="flex justify-end gap-3 pt-1">
                <button wire:click="$set('showEmailModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Annuler
                </button>
                <button wire:click="doSendEmail" wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-medium text-white bg-[#1E3A5F] rounded-lg hover:bg-[#162d4a] transition-colors flex items-center gap-2">
                    <span wire:loading.remove wire:target="doSendEmail">Envoyer</span>
                    <span wire:loading wire:target="doSendEmail">Envoi en cours…</span>
                </button>
            </div>
        </div>
    </div>
    @endteleport

</div>

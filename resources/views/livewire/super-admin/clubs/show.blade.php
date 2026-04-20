{{--
    Vue : Fiche détaillée d'un club — Super Admin
    Infos, statistiques, paiements, utilisateurs, impersonation
--}}

<x-slot:pageTitle>{{ $tenant->name }}</x-slot:pageTitle>

<div class="space-y-6">

    {{-- Navigation retour --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('superadmin.clubs.index') }}"
           class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour à la liste
        </a>

        {{-- Actions principales --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('superadmin.clubs.edit', $tenant) }}"
               class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Éditer
            </a>

            @if ($tenant->status !== 'active')
                <button wire:click="activate" wire:confirm="Activer ce club ?"
                        class="px-3 py-1.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                    Activer
                </button>
            @endif

            @if ($tenant->status === 'active')
                <button wire:click="suspend" wire:confirm="Suspendre ce club ?"
                        class="px-3 py-1.5 text-sm font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 transition-colors">
                    Suspendre
                </button>
            @endif

            {{-- Bouton impersonation --}}
            <form method="POST" action="{{ route('superadmin.clubs.impersonate', $tenant) }}">
                @csrf
                <button type="submit"
                        class="px-3 py-1.5 text-sm font-medium text-white bg-[#1E3A5F] rounded-lg hover:bg-[#162d4a] transition-colors flex items-center gap-1.5"
                        title="Se connecter en tant qu'admin de ce club">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Se connecter en tant qu'admin
                </button>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- INFORMATIONS PRINCIPALES                                      --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Carte infos --}}
        <div class="xl:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0"
                     style="background-color: {{ $tenant->primary_color }}">
                    {{ strtoupper(substr($tenant->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3">
                        <h2 class="text-lg font-bold text-gray-900">{{ $tenant->name }}</h2>
                        @php
                            $badge = match($tenant->status) {
                                'active'    => ['bg-emerald-100 text-emerald-700', 'Actif'],
                                'trial'     => ['bg-blue-100 text-blue-700', 'Essai'],
                                'suspended' => ['bg-red-100 text-red-700', 'Suspendu'],
                                'cancelled' => ['bg-gray-100 text-gray-600', 'Annulé'],
                                default     => ['bg-gray-100 text-gray-600', $tenant->status],
                            };
                        @endphp
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $badge[0] }}">{{ $badge[1] }}</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $tenant->subdomain }}.teamtrack.test</p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Email</span>
                    <p class="mt-0.5 text-gray-700">{{ $tenant->email }}</p>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Téléphone</span>
                    <p class="mt-0.5 text-gray-700">{{ $tenant->phone ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Ville / Pays</span>
                    <p class="mt-0.5 text-gray-700">{{ $tenant->city ?? '—' }}, {{ $tenant->country }}</p>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Plan</span>
                    <p class="mt-0.5 text-gray-700">{{ $tenant->plan->name ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Inscription</span>
                    <p class="mt-0.5 text-gray-700">{{ $tenant->created_at->format('d/m/Y') }}</p>
                </div>
                @if ($tenant->trial_ends_at)
                    <div>
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Fin d'essai</span>
                        <p class="mt-0.5 text-gray-700">{{ $tenant->trial_ends_at->format('d/m/Y') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Statistiques --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Statistiques</h3>
            <div class="space-y-3">
                @foreach ([
                    ['Joueurs', $stats['joueurs'], 'text-purple-600', 'bg-purple-50'],
                    ['Catégories', $stats['categories'], 'text-blue-600', 'bg-blue-50'],
                    ['Équipes', $stats['equipes'], 'text-emerald-600', 'bg-emerald-50'],
                    ['Utilisateurs', $stats['utilisateurs'], 'text-gray-600', 'bg-gray-50'],
                ] as [$label, $value, $textColor, $bgColor])
                    <div class="flex items-center justify-between p-3 {{ $bgColor }} rounded-lg">
                        <span class="text-sm text-gray-600">{{ $label }}</span>
                        <span class="text-sm font-bold {{ $textColor }}">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- PAIEMENTS + UTILISATEURS (colonnes)                          --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Historique des paiements --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Historique des paiements</h3>
                <a href="{{ route('superadmin.payments.index') }}?tenant={{ $tenant->id }}"
                   class="text-xs text-[#1E3A5F] hover:underline">Voir tout</a>
            </div>

            @if ($payments->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">Aucun paiement enregistré.</p>
            @else
                <div class="space-y-2">
                    @foreach ($payments as $payment)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</p>
                                <p class="text-xs text-gray-400">{{ $payment->reference }} · {{ $payment->paid_at?->format('d/m/Y') ?? '—' }}</p>
                            </div>
                            @php
                                $pb = match($payment->status) {
                                    'paid'    => ['bg-emerald-100 text-emerald-700', 'Payé'],
                                    'pending' => ['bg-amber-100 text-amber-700', 'En attente'],
                                    'failed'  => ['bg-red-100 text-red-700', 'Échoué'],
                                    default   => ['bg-gray-100 text-gray-600', $payment->status],
                                };
                            @endphp
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $pb[0] }}">{{ $pb[1] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Liste des utilisateurs --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Utilisateurs du club</h3>

            @if ($users->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">Aucun utilisateur.</p>
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
                            @if (! $user->is_active)
                                <span class="text-xs text-red-600 font-medium">Inactif</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>

{{--
    Vue : Gestion des plans d'abonnement — Super Admin
--}}

<x-slot:pageTitle>Plans d'abonnement</x-slot:pageTitle>

<div class="space-y-6">

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl px-4 py-3">{{ session('error') }}</div>
    @endif

    {{-- En-tête avec bouton créer --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $plans->count() }} plan(s) configuré(s)</p>
        <a href="{{ route('superadmin.plans.create') }}"
           class="flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white text-sm font-medium rounded-lg hover:bg-[#162d4a] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau plan
        </a>
    </div>

    {{-- Grille des plans --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse ($plans as $plan)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex flex-col gap-4">

                {{-- En-tête du plan --}}
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">{{ $plan->name }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $plan->billing_cycle === 'monthly' ? 'Mensuel' : 'Annuel' }}
                        </p>
                    </div>
                    @if ($plan->is_active)
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Actif</span>
                    @else
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-500">Désactivé</span>
                    @endif
                </div>

                {{-- Prix --}}
                <div>
                    <span class="text-3xl font-bold text-[#1E3A5F]">{{ number_format($plan->price, 0, ',', ' ') }}</span>
                    <span class="text-sm text-gray-400 ml-1">FCFA / mois</span>
                </div>

                {{-- Limites --}}
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <span>👤 {{ $plan->max_users }} utilisateurs</span>
                    <span>⚽ {{ $plan->max_players }} joueurs</span>
                </div>

                {{-- Fonctionnalités --}}
                @if ($plan->features)
                    <ul class="space-y-1">
                        @foreach ($plan->features as $feature)
                            <li class="flex items-center gap-1.5 text-xs text-gray-600">
                                <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- Clubs abonnés --}}
                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-xs text-gray-400">
                        <span class="font-semibold text-gray-700">{{ $plan->tenants_count }}</span> club(s) abonné(s)
                    </span>

                    <div class="flex items-center gap-1">
                        {{-- Activer / Désactiver --}}
                        <button wire:click="toggleActive({{ $plan->id }})"
                                class="p-1.5 rounded-md text-gray-400 hover:bg-gray-100 transition-colors"
                                title="{{ $plan->is_active ? 'Désactiver' : 'Activer' }}">
                            @if ($plan->is_active)
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </button>

                        {{-- Éditer --}}
                        <a href="{{ route('superadmin.plans.edit', $plan) }}"
                           class="p-1.5 rounded-md text-gray-400 hover:text-[#1E3A5F] hover:bg-gray-100 transition-colors"
                           title="Éditer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>

                        {{-- Supprimer --}}
                        <button wire:click="delete({{ $plan->id }})"
                                wire:confirm="Supprimer ce plan ?"
                                class="p-1.5 rounded-md text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                title="Supprimer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center text-sm text-gray-400 py-16">
                Aucun plan configuré. <a href="{{ route('superadmin.plans.create') }}" class="text-[#1E3A5F] underline">Créer le premier plan</a>
            </div>
        @endforelse
    </div>

</div>

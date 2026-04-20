{{--
    Vue : Liste des clubs — Super Admin
    Tableau filtrable avec actions rapides (activer, suspendre, supprimer)
--}}

<x-slot:pageTitle>Gestion des clubs</x-slot:pageTitle>

<div class="space-y-6">

    {{-- ============================================================ --}}
    {{-- BARRE D'OUTILS : recherche + filtres + bouton créer          --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex flex-wrap items-center gap-3">

            {{-- Recherche --}}
            <div class="relative flex-1 min-w-48">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text"
                       placeholder="Rechercher un club…"
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30 focus:border-[#1E3A5F]">
            </div>

            {{-- Filtre statut --}}
            <select wire:model.live="filterStatus"
                    class="py-2 pl-3 pr-8 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30">
                <option value="">Tous statuts</option>
                <option value="active">Actif</option>
                <option value="trial">Essai</option>
                <option value="suspended">Suspendu</option>
                <option value="cancelled">Annulé</option>
            </select>

            {{-- Filtre plan --}}
            <select wire:model.live="filterPlan"
                    class="py-2 pl-3 pr-8 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30">
                <option value="">Tous plans</option>
                @foreach ($plans as $plan)
                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                @endforeach
            </select>

            {{-- Filtre pays --}}
            <select wire:model.live="filterCountry"
                    class="py-2 pl-3 pr-8 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30">
                <option value="">Tous pays</option>
                @foreach ($countries as $country)
                    <option value="{{ $country }}">{{ $country }}</option>
                @endforeach
            </select>

            {{-- Bouton créer --}}
            <a href="{{ route('superadmin.clubs.create') }}"
               class="ml-auto flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white text-sm font-medium rounded-lg hover:bg-[#162d4a] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau club
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TABLEAU DES CLUBS                                            --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Club</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Plan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pays</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Inscrit le</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($tenants as $tenant)
                    <tr class="hover:bg-gray-50/60 transition-colors">

                        {{-- Club --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 text-white text-sm font-bold"
                                     style="background-color: {{ $tenant->primary_color }}">
                                    {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('superadmin.clubs.show', $tenant) }}"
                                       class="text-sm font-semibold text-gray-900 hover:text-[#1E3A5F] transition-colors">
                                        {{ $tenant->name }}
                                    </a>
                                    <p class="text-xs text-gray-400">{{ $tenant->subdomain }}.teamtrack.test</p>
                                </div>
                            </div>
                        </td>

                        {{-- Plan --}}
                        <td class="px-4 py-4 text-sm text-gray-600">
                            {{ $tenant->plan->name ?? '—' }}
                        </td>

                        {{-- Statut --}}
                        <td class="px-4 py-4">
                            @php
                                $badge = match($tenant->status) {
                                    'active'    => ['bg-emerald-100 text-emerald-700', 'Actif'],
                                    'trial'     => ['bg-blue-100 text-blue-700', 'Essai'],
                                    'suspended' => ['bg-red-100 text-red-700', 'Suspendu'],
                                    'cancelled' => ['bg-gray-100 text-gray-600', 'Annulé'],
                                    default     => ['bg-gray-100 text-gray-600', $tenant->status],
                                };
                            @endphp
                            <span class="inline-flex text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $badge[0] }}">
                                {{ $badge[1] }}
                            </span>
                        </td>

                        {{-- Pays --}}
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $tenant->country }}</td>

                        {{-- Date --}}
                        <td class="px-4 py-4 text-sm text-gray-500">
                            {{ $tenant->created_at->format('d/m/Y') }}
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">

                                {{-- Voir --}}
                                <a href="{{ route('superadmin.clubs.show', $tenant) }}"
                                   class="p-1.5 text-gray-400 hover:text-[#1E3A5F] rounded-md hover:bg-gray-100 transition-colors"
                                   title="Voir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                {{-- Éditer --}}
                                <a href="{{ route('superadmin.clubs.edit', $tenant) }}"
                                   class="p-1.5 text-gray-400 hover:text-[#1E3A5F] rounded-md hover:bg-gray-100 transition-colors"
                                   title="Éditer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                {{-- Activer --}}
                                @if ($tenant->status !== 'active')
                                    <button wire:click="activate({{ $tenant->id }})"
                                            wire:confirm="Activer ce club ?"
                                            class="p-1.5 text-gray-400 hover:text-emerald-600 rounded-md hover:bg-emerald-50 transition-colors"
                                            title="Activer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                @endif

                                {{-- Suspendre --}}
                                @if ($tenant->status === 'active')
                                    <button wire:click="suspend({{ $tenant->id }})"
                                            wire:confirm="Suspendre ce club ?"
                                            class="p-1.5 text-gray-400 hover:text-amber-600 rounded-md hover:bg-amber-50 transition-colors"
                                            title="Suspendre">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                @endif

                                {{-- Supprimer --}}
                                <button wire:click="delete({{ $tenant->id }})"
                                        wire:confirm="Supprimer ce club ? Cette action est réversible (soft delete)."
                                        class="p-1.5 text-gray-400 hover:text-red-600 rounded-md hover:bg-red-50 transition-colors"
                                        title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-sm text-gray-400">
                            Aucun club trouvé avec ces critères.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($tenants->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>

</div>

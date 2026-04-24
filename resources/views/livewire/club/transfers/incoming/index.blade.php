<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('club.transfers.dashboard') }}" wire:navigate class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-extrabold text-gray-900">Transferts entrants</h1>
            <p class="text-gray-500 text-sm mt-0.5">Recrutements et profils recherchés</p>
        </div>
        <button wire:click="openCreate"
                class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl shadow-sm"
                style="background-color: var(--club-primary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau recrutement
        </button>
    </div>

    @if(!$currentWindow)
    <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-4 text-sm text-amber-700 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        Hors fenêtre de transfert — les dossiers peuvent être préparés en avance.
    </div>
    @endif

    {{-- Filtres --}}
    <div class="flex flex-wrap gap-3 mb-5">
        <div class="relative flex-1 min-w-48">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Rechercher un joueur ou club…"
                   class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
        </div>
        <select wire:model.live="statusFilter" class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F] bg-white">
            <option value="">Tous les statuts</option>
            <option value="listed">Profil recherché</option>
            <option value="negotiating">En négociation</option>
            <option value="offer_received">Offre envoyée</option>
            <option value="agreed">Accord trouvé</option>
            <option value="finalized">Finalisé</option>
            <option value="cancelled">Annulé</option>
        </select>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Joueur / Profil</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Type</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Club vendeur</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Budget max</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Négos</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($transfers as $t)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-gray-900">{{ $t->playerDisplayName() }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $t->search_position ?? $t->player?->position ?? '' }}
                            @if($t->search_age_min || $t->search_age_max)
                                · {{ $t->search_age_min ?? '?' }}–{{ $t->search_age_max ?? '?' }} ans
                            @endif
                        </p>
                    </td>
                    <td class="px-4 py-3.5 hidden sm:table-cell">
                        <span class="text-xs font-medium text-gray-600 bg-gray-100 px-2 py-1 rounded-full">{{ $t->typeLabel() }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-gray-600 hidden md:table-cell">{{ $t->counterpart_club ?? '—' }}</td>
                    <td class="px-4 py-3.5">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                            {{ $t->status === 'listed'         ? 'bg-gray-100 text-gray-600' : '' }}
                            {{ $t->status === 'negotiating'    ? 'bg-blue-50 text-blue-700' : '' }}
                            {{ $t->status === 'offer_received' ? 'bg-amber-50 text-amber-700' : '' }}
                            {{ $t->status === 'agreed'         ? 'bg-emerald-50 text-emerald-700' : '' }}
                            {{ $t->status === 'finalized'      ? 'bg-green-100 text-green-700' : '' }}
                            {{ $t->status === 'cancelled'      ? 'bg-red-50 text-red-600' : '' }}">
                            {{ $t->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell font-semibold text-gray-700">
                        {{ $t->search_budget_max ? number_format($t->search_budget_max,0,'.',' ').' F' : '—' }}
                    </td>
                    <td class="px-4 py-3.5 hidden lg:table-cell text-center">
                        <span class="text-xs font-bold text-gray-500 bg-gray-100 w-6 h-6 rounded-full inline-flex items-center justify-center">{{ $t->negotiations_count }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <a href="{{ route('club.transfers.show', $t->id) }}" wire:navigate
                           class="text-xs font-semibold text-[#1E3A5F] hover:underline">Ouvrir →</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">Aucun recrutement en cours</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($transfers->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $transfers->links() }}</div>
        @endif
    </div>

    {{-- Modal création --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg my-auto" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Nouveau recrutement entrant</h3>
                <button wire:click="$set('showModal',false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom du joueur <span class="font-normal text-gray-400">(ou description du profil recherché)</span></label>
                    <input wire:model="player_name" type="text" autofocus placeholder="Ex : Mamadou Diallo ou 'Attaquant de pointe'"
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Type de transfert</label>
                    <div class="flex gap-2">
                        @foreach(['permanent' => 'Transfert définitif', 'loan' => 'Prêt'] as $val => $label)
                        <button type="button" wire:click="$set('type','{{ $val }}')"
                                class="flex-1 py-2.5 rounded-xl border-2 text-sm font-semibold transition-all {{ $type === $val ? 'text-white' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}"
                                style="{{ $type === $val ? 'background-color: var(--club-primary); border-color: var(--club-primary);' : '' }}">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Club vendeur (optionnel)</label>
                    <input wire:model="counterpart_club" type="text" placeholder="Club d'origine du joueur"
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                </div>
                <div class="grid sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Poste recherché</label>
                        <input wire:model="search_position" type="text" placeholder="Attaquant…"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Âge min</label>
                        <input wire:model="search_age_min" type="number" min="10" max="50" placeholder="18"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Âge max</label>
                        <input wire:model="search_age_max" type="number" min="10" max="50" placeholder="28"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Budget maximum (F CFA)</label>
                    <input wire:model="search_budget_max" type="number" min="0" step="1000" placeholder="Ex : 30 000 000"
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Critères / Notes</label>
                    <textarea wire:model="search_criteria" rows="2" placeholder="Critères spécifiques, notes de scouting…"
                              class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal',false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">Créer</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

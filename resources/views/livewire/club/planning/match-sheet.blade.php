<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('club.planning.edit', $event->id) }}" wire:navigate class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Feuille de match</h1>
            <p class="text-gray-500 text-sm mt-0.5">
                {{ $event->title }}
                @if($event->opponent) · vs {{ $event->opponent }} @endif
                · {{ $event->starts_at->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>

    {{-- Résultat --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-6"
         x-data="{ scoreHome: '{{ $event->score_home ?? '' }}', scoreAway: '{{ $event->score_away ?? '' }}', report: @js($event->match_report ?? '') }">
        <h2 class="font-bold text-gray-900 mb-4">Résultat</h2>
        <div class="flex items-center gap-4 mb-4">
            <div class="flex-1 text-center">
                <p class="text-xs font-semibold text-gray-500 mb-1">{{ $event->home_away === 'home' ? 'Nous (domicile)' : 'Nous (extérieur)' }}</p>
                <input x-model="scoreHome" type="number" min="0"
                       class="w-20 text-center text-2xl font-extrabold border-2 border-gray-200 rounded-xl py-2 focus:outline-none focus:border-[#1E3A5F]">
            </div>
            <span class="text-2xl font-extrabold text-gray-400">—</span>
            <div class="flex-1 text-center">
                <p class="text-xs font-semibold text-gray-500 mb-1">{{ $event->opponent ?? 'Adversaire' }}</p>
                <input x-model="scoreAway" type="number" min="0"
                       class="w-20 text-center text-2xl font-extrabold border-2 border-gray-200 rounded-xl py-2 focus:outline-none focus:border-[#1E3A5F]">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Rapport d'après-match</label>
            <textarea x-model="report" rows="3"
                      class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                      placeholder="Analyse tactique, points positifs/négatifs…"></textarea>
        </div>
        <button type="button"
                @click="$wire.saveResult(scoreHome, scoreAway, report)"
                class="px-5 py-2.5 text-sm font-bold text-white rounded-xl"
                style="background-color: var(--club-primary);">
            Enregistrer le résultat
        </button>
    </div>

    {{-- Composition --}}
    <form wire:submit="save">
        <div class="grid lg:grid-cols-2 gap-6">
            {{-- Titulaires --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-green-50">
                    <h2 class="font-bold text-green-800">Titulaires</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($event->eventPlayers as $ep)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <div class="w-8 h-8 rounded-full bg-[#1E3A5F]/10 flex items-center justify-center text-xs font-bold text-[#1E3A5F] flex-shrink-0">
                            {{ $ep->player->jersey_number ?? '?' }}
                        </div>
                        <p class="text-sm font-semibold text-gray-800 flex-1">{{ $ep->player->first_name }} {{ $ep->player->last_name }}</p>
                        <select wire:model="lineup.{{ $ep->player_id }}.lineup"
                                class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-[#1E3A5F]/20">
                            <option value="none">—</option>
                            <option value="starter">Titulaire</option>
                            <option value="substitute">Remplaçant</option>
                        </select>
                        <input wire:model="lineup.{{ $ep->player_id }}.position" type="text"
                               placeholder="Poste" class="w-24 text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-[#1E3A5F]/20">
                        <input wire:model="lineup.{{ $ep->player_id }}.minutes" type="number" min="0" max="120"
                               placeholder="min" class="w-16 text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-[#1E3A5F]/20">
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Résumé composition --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-900">Récapitulatif</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Titulaires</p>
                        @php
                            $starters = $event->eventPlayers->where('lineup', 'starter');
                            $subs     = $event->eventPlayers->where('lineup', 'substitute');
                        @endphp
                        @forelse($starters as $ep)
                        <div class="flex items-center gap-2 text-sm py-1">
                            <span class="w-6 text-center text-xs font-bold text-gray-400">{{ $ep->player->jersey_number ?? '?' }}</span>
                            <span class="text-gray-800">{{ $ep->player->first_name }} {{ $ep->player->last_name }}</span>
                            @if($ep->position_played) <span class="text-xs text-gray-400">· {{ $ep->position_played }}</span> @endif
                        </div>
                        @empty
                        <p class="text-sm text-gray-400">Aucun titulaire défini</p>
                        @endforelse
                    </div>
                    @if($subs->isNotEmpty())
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Remplaçants</p>
                        @foreach($subs as $ep)
                        <div class="flex items-center gap-2 text-sm py-1">
                            <span class="w-6 text-center text-xs font-bold text-gray-400">{{ $ep->player->jersey_number ?? '?' }}</span>
                            <span class="text-gray-800">{{ $ep->player->first_name }} {{ $ep->player->last_name }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white rounded-xl shadow-sm"
                    style="background-color: var(--club-primary);">
                <span wire:loading.remove wire:target="save">Enregistrer la composition</span>
                <span wire:loading wire:target="save">Enregistrement…</span>
            </button>
        </div>
    </form>
</div>

<div x-data="{ tab: 'info' }">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('club.planning.calendar') }}" wire:navigate class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">
                {{ $eventId ? 'Modifier l\'événement' : 'Nouvel événement' }}
            </h1>
            <p class="text-gray-500 text-sm mt-0.5">Remplissez les informations ci-dessous</p>
        </div>
    </div>

    {{-- Onglets --}}
    <div class="flex gap-1 mb-6 bg-gray-100 rounded-xl p-1 w-fit">
        <button type="button" @click="tab='info'"
                :class="tab==='info' ? 'bg-white shadow text-gray-900 font-bold' : 'text-gray-500'"
                class="px-4 py-2 text-sm rounded-lg transition-all">Informations</button>
        <button type="button" @click="tab='players'"
                :class="tab==='players' ? 'bg-white shadow text-gray-900 font-bold' : 'text-gray-500'"
                class="px-4 py-2 text-sm rounded-lg transition-all">
            Joueurs
            @if(count($this->convokedPlayerIds) > 0)
                <span class="ml-1 bg-blue-100 text-blue-700 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ count($this->convokedPlayerIds) }}</span>
            @endif
        </button>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{-- Onglet Informations --}}
        <div x-show="tab==='info'" class="space-y-5">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">

                {{-- Type + Titre --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Type *</label>
                        <select wire:model.live="type"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                            <option value="training">Entraînement</option>
                            <option value="match">Match</option>
                            <option value="meeting">Réunion</option>
                            <option value="travel">Déplacement</option>
                        </select>
                        @error('type') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Titre *</label>
                        <input wire:model="title" type="text"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                               placeholder="Ex : Entraînement technique">
                        @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Dates --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Début *</label>
                        <input wire:model="starts_at" type="datetime-local"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        @error('starts_at') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Fin *</label>
                        <input wire:model="ends_at" type="datetime-local"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        @error('ends_at') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Terrain + Équipe --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Terrain</label>
                        <select wire:model="field_id"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                            <option value="">— Aucun —</option>
                            @foreach($fields as $f)
                                <option value="{{ $f->id }}">{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Équipe</label>
                        <select wire:model.live="team_id"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                            <option value="">— Toutes —</option>
                            @foreach($teams as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Récurrence (entraînements) --}}
                @if($type === 'training')
                <div class="border-t border-gray-100 pt-4">
                    <div class="flex items-center gap-3 mb-3">
                        <input wire:model.live="is_recurring" id="is_recurring" type="checkbox"
                               class="w-4 h-4 rounded border-gray-300 text-[#1E3A5F] focus:ring-[#1E3A5F]/30">
                        <label for="is_recurring" class="text-sm font-semibold text-gray-700">Récurrence hebdomadaire</label>
                    </div>
                    @if($is_recurring)
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Répéter jusqu'au</label>
                        <input wire:model="recurrence_until" type="date"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        @error('recurrence_until') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    @endif
                </div>
                @endif

                {{-- Match-spécifique --}}
                @if($type === 'match')
                <div class="border-t border-gray-100 pt-4 space-y-4">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Informations du match</p>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Compétition</label>
                            <input wire:model="competition" type="text" placeholder="Championnat, Coupe…"
                                   class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Adversaire</label>
                            <input wire:model="opponent" type="text" placeholder="Nom du club adverse"
                                   class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lieu</label>
                        <div class="flex gap-3">
                            @foreach(['home' => 'Domicile', 'away' => 'Extérieur', 'neutral' => 'Terrain neutre'] as $val => $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input wire:model="home_away" type="radio" value="{{ $val }}"
                                       class="w-4 h-4 text-[#1E3A5F] border-gray-300 focus:ring-[#1E3A5F]/30">
                                <span class="text-sm text-gray-700">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @if($eventId)
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Score (nous)</label>
                            <input wire:model="score_home" type="number" min="0"
                                   class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Score (eux)</label>
                            <input wire:model="score_away" type="number" min="0"
                                   class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Rapport d'après-match</label>
                        <textarea wire:model="match_report" rows="3"
                                  class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                                  placeholder="Bilan tactique, points positifs/négatifs…"></textarea>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Notes</label>
                    <textarea wire:model="notes" rows="2"
                              class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                              placeholder="Informations complémentaires…"></textarea>
                </div>
            </div>
        </div>

        {{-- Onglet Joueurs --}}
        <div x-show="tab==='players'" class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-semibold text-gray-700">
                        Sélectionnez les joueurs convoqués ({{ count($this->convokedPlayerIds) }} sélectionnés)
                    </p>
                    <div class="flex gap-2">
                        <button type="button" wire:click="selectAllPlayers"
                                class="text-xs font-semibold text-[#1E3A5F] hover:underline">Tout sélectionner</button>
                        <span class="text-gray-300">|</span>
                        <button type="button" wire:click="clearPlayers"
                                class="text-xs font-semibold text-gray-400 hover:underline">Effacer</button>
                    </div>
                </div>

                @if($players->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-8">
                        Aucun joueur{{ $team_id ? ' dans cette équipe' : '' }}.
                        @if(!$team_id) Sélectionnez une équipe pour filtrer. @endif
                    </p>
                @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach($players as $player)
                    @php $selected = in_array((string)$player->id, $this->convokedPlayerIds); @endphp
                    <button type="button" wire:click="togglePlayer({{ $player->id }})"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl border transition-colors text-left
                                   {{ $selected ? 'border-[#1E3A5F] bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                                    {{ $selected ? 'bg-[#1E3A5F] text-white' : 'bg-gray-100 text-gray-600' }}">
                            {{ $player->jersey_number ?? strtoupper(substr($player->first_name,0,1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $player->first_name }} {{ $player->last_name }}</p>
                            <p class="text-xs text-gray-400">{{ $player->position ?? '—' }}</p>
                        </div>
                        @if($selected)
                        <svg class="w-4 h-4 text-[#1E3A5F] ml-auto flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        @endif
                    </button>
                    @endforeach
                </div>
                @endif

                @if($type === 'match' && count($this->convokedPlayerIds) > 0)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-3">
                        <input wire:model="sendConvocationEmails" id="send_emails" type="checkbox"
                               class="w-4 h-4 rounded border-gray-300 text-[#1E3A5F] focus:ring-[#1E3A5F]/30">
                        <label for="send_emails" class="text-sm font-semibold text-gray-700">
                            Envoyer les convocations par email
                            @if($eventId)
                                <span class="text-xs text-gray-400 font-normal ml-1">(uniquement si pas déjà envoyées)</span>
                            @endif
                        </label>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            @if($eventId)
            <button type="button" wire:click="delete" wire:confirm="Supprimer cet événement ?"
                    class="px-4 py-2.5 text-sm font-semibold text-red-600 bg-white border border-red-200 rounded-xl hover:bg-red-50">
                Supprimer
            </button>
            @endif
            <a href="{{ route('club.planning.calendar') }}" wire:navigate
               class="px-4 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                Annuler
            </a>
            <button type="submit"
                    class="flex-1 sm:flex-none px-6 py-2.5 text-sm font-bold text-white rounded-xl"
                    style="background-color: var(--club-primary);">
                <span wire:loading.remove wire:target="save">{{ $eventId ? 'Enregistrer' : 'Créer l\'événement' }}</span>
                <span wire:loading wire:target="save">Enregistrement…</span>
            </button>
        </div>
    </form>
</div>

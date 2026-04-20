<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Équipes</h1>
            <p class="text-gray-500 text-sm mt-0.5">{{ $teams->count() }} équipe(s)</p>
        </div>
        <div class="flex gap-3 items-center">
            <select wire:model.live="filterCat" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20">
                <option value="">Toutes catégories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
            <button wire:click="openCreate"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl shadow-sm"
                    style="background-color: var(--club-primary);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle équipe
            </button>
        </div>
    </div>

    @if($teams->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center shadow-sm">
            <p class="text-gray-500 font-medium">Aucune équipe trouvée</p>
            <button wire:click="openCreate" class="mt-2 text-sm font-semibold text-[#1E3A5F] hover:underline">Créer la première →</button>
        </div>
    @else
        <div class="space-y-4">
            @foreach($teams as $team)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-white text-sm font-bold"
                             style="background-color: var(--club-primary);">
                            {{ strtoupper(substr($team->name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">{{ $team->name }}</h3>
                            <p class="text-xs text-gray-400">
                                {{ $team->category?->name ?? 'Aucune catégorie' }}
                                @if($team->coach)
                                    · Coach : {{ $team->coach->name }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-bold text-gray-700">{{ $team->players_count }} <span class="text-gray-400 font-normal text-xs">joueurs</span></span>
                        <button wire:click="toggleRoster({{ $team->id }})"
                                class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors
                                       {{ $rosterTeamId === $team->id ? 'border-[#1E3A5F] text-[#1E3A5F] bg-blue-50' : 'border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                            {{ $rosterTeamId === $team->id ? 'Masquer' : 'Effectif' }}
                        </button>
                        <button wire:click="openEdit({{ $team->id }})" class="p-1.5 text-gray-400 hover:text-[#1E3A5F] hover:bg-gray-100 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </button>
                        <button wire:click="delete({{ $team->id }})" wire:confirm="Supprimer l'équipe {{ $team->name }} ?"
                                class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Effectif déplié --}}
                @if($rosterTeamId === $team->id)
                <div class="border-t border-gray-100 px-6 py-4 bg-gray-50">
                    @if($roster->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-2">Aucun joueur dans cette équipe</p>
                    @else
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($roster as $p)
                            <div class="flex items-center gap-3 bg-white rounded-xl px-4 py-2.5 border border-gray-100">
                                <div class="w-7 h-7 rounded-full bg-[#1E3A5F]/10 flex items-center justify-center text-xs font-bold text-[#1E3A5F] flex-shrink-0">
                                    {{ $p->jersey_number ?? '—' }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $p->first_name }} {{ $p->last_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $p->position ?? '—' }}</p>
                                </div>
                                <span class="ml-auto text-xs {{ $p->statusColor() }} px-2 py-0.5 rounded-full font-semibold flex-shrink-0">{{ $p->statusLabel() }}</span>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
    @endif

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">{{ $editingId ? 'Modifier l\'équipe' : 'Nouvelle équipe' }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form wire:submit="save" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom de l'équipe *</label>
                    <input wire:model="name" type="text" autofocus
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                           placeholder="Ex : Équipe Première, U17 A…">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catégorie</label>
                    <select wire:model="category_id" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        <option value="">— Aucune —</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Entraîneur principal</label>
                    <select wire:model="coach_id" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        <option value="">— Aucun —</option>
                        @foreach($coaches as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">
                        {{ $editingId ? 'Enregistrer' : 'Créer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

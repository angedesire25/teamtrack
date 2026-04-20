<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Catégories</h1>
            <p class="text-gray-500 text-sm mt-0.5">Gérez les tranches d'âge de votre club</p>
        </div>
        <button wire:click="openCreate"
                class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl transition-colors shadow-sm"
                style="background-color: var(--club-primary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle catégorie
        </button>
    </div>

    {{-- Grille des catégories --}}
    @if($categories->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center shadow-sm">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <p class="text-gray-500 font-medium">Aucune catégorie pour l'instant</p>
            <button wire:click="openCreate" class="mt-2 text-sm font-semibold text-[#1E3A5F] hover:underline">Créer la première →</button>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($categories as $cat)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="h-2" style="background-color: var(--club-primary);"></div>
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="font-bold text-gray-900 text-base">{{ $cat->name }}</h3>
                        <div class="flex gap-1">
                            <button wire:click="openEdit({{ $cat->id }})"
                                    class="p-1.5 text-gray-400 hover:text-[#1E3A5F] hover:bg-gray-100 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                            <button wire:click="delete({{ $cat->id }})"
                                    wire:confirm="Supprimer la catégorie {{ $cat->name }} ?"
                                    class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm text-gray-500">
                        @if($cat->min_age || $cat->max_age)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ $cat->min_age ?? '?' }} – {{ $cat->max_age ?? '?' }} ans</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $cat->gender === 'male' ? 'bg-blue-100 text-blue-600' : ($cat->gender === 'female' ? 'bg-pink-100 text-pink-600' : 'bg-purple-100 text-purple-600') }}">
                                {{ ['male' => 'Masculin', 'female' => 'Féminin', 'mixed' => 'Mixte'][$cat->gender] }}
                            </span>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-4 pt-4 border-t border-gray-100">
                        <div class="text-center">
                            <p class="text-2xl font-extrabold text-gray-900">{{ $cat->teams_count }}</p>
                            <p class="text-xs text-gray-400 font-medium">Équipes</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-extrabold text-gray-900">{{ $cat->players_count }}</p>
                            <p class="text-xs text-gray-400 font-medium">Joueurs</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- Modal création / édition --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-data x-init="$el.focus()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">{{ $editingId ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form wire:submit="save" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom *</label>
                    <input wire:model="name" type="text" autofocus
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                           placeholder="Ex : U15, Seniors A…">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Âge min</label>
                        <input wire:model="min_age" type="number" min="4" max="99"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Âge max</label>
                        <input wire:model="max_age" type="number" min="4" max="99"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Genre</label>
                        <select wire:model="gender" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                            <option value="male">Masculin</option>
                            <option value="female">Féminin</option>
                            <option value="mixed">Mixte</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ordre d'affichage</label>
                        <input wire:model="sort_order" type="number"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl transition-colors"
                            style="background-color: var(--club-primary);">
                        {{ $editingId ? 'Enregistrer' : 'Créer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

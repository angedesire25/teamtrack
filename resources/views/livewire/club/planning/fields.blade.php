<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('club.planning.calendar') }}" wire:navigate class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-extrabold text-gray-900">Terrains</h1>
            <p class="text-gray-500 text-sm mt-0.5">Répertoire des terrains du club</p>
        </div>
        <button wire:click="openCreate"
                class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl shadow-sm"
                style="background-color: var(--club-primary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter
        </button>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($fields as $field)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 {{ !$field->is_active ? 'opacity-60' : '' }}">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                         style="background-color: var(--club-primary);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 text-sm">{{ $field->name }}</p>
                        @if($field->address)
                        <p class="text-xs text-gray-400 truncate">{{ $field->address }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex gap-1">
                    <button wire:click="openEdit({{ $field->id }})" class="p-1.5 text-gray-400 hover:text-[#1E3A5F] hover:bg-gray-100 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </button>
                    <button wire:click="delete({{ $field->id }})" wire:confirm="Supprimer ce terrain ?"
                            class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex flex-wrap gap-1.5 mt-2">
                @if($field->surface)
                <span class="text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full font-medium">{{ $field->surface }}</span>
                @endif
                @if($field->capacity)
                <span class="text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full font-medium">{{ number_format($field->capacity) }} places</span>
                @endif
                <button wire:click="toggleActive({{ $field->id }})"
                        class="flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full border transition-colors
                               {{ $field->is_active ? 'border-emerald-200 text-emerald-600 bg-emerald-50' : 'border-gray-200 text-gray-500 bg-gray-50' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $field->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                    {{ $field->is_active ? 'Actif' : 'Inactif' }}
                </button>
            </div>
        </div>
        @empty
        <div class="sm:col-span-2 lg:col-span-3 bg-white rounded-2xl border border-gray-200 p-16 text-center shadow-sm">
            <p class="text-gray-500 font-medium">Aucun terrain enregistré</p>
            <button wire:click="openCreate" class="mt-2 text-sm font-semibold text-[#1E3A5F] hover:underline">Ajouter le premier →</button>
        </div>
        @endforelse
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">{{ $editingId ? 'Modifier le terrain' : 'Nouveau terrain' }}</h3>
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
                           placeholder="Stade Félix Houphouët-Boigny">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Adresse</label>
                    <input wire:model="address" type="text"
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                           placeholder="Abidjan, Côte d'Ivoire">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Surface</label>
                        <select wire:model="surface"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                            <option value="">— Choisir —</option>
                            <option value="Gazon naturel">Gazon naturel</option>
                            <option value="Gazon synthétique">Gazon synthétique</option>
                            <option value="Sable">Sable</option>
                            <option value="Salle">Salle</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Capacité</label>
                        <input wire:model="capacity" type="number" min="0"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                               placeholder="60000">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Notes</label>
                    <textarea wire:model="notes" rows="2"
                              class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"></textarea>
                </div>
                @if($editingId)
                <div class="flex items-center gap-3">
                    <input wire:model="is_active" id="field_active" type="checkbox"
                           class="w-4 h-4 rounded border-gray-300 text-[#1E3A5F] focus:ring-[#1E3A5F]/30">
                    <label for="field_active" class="text-sm font-semibold text-gray-700">Terrain actif</label>
                </div>
                @endif
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl"
                            style="background-color: var(--club-primary);">
                        {{ $editingId ? 'Enregistrer' : 'Ajouter' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

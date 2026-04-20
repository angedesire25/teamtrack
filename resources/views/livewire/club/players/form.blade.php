<div>
    {{-- En-tête --}}
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('club.players.index') }}" wire:navigate
           class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">{{ $isEdit ? 'Modifier le joueur' : 'Nouveau joueur' }}</h1>
            @if($isEdit)
                <p class="text-gray-400 text-sm mt-0.5">{{ $player->first_name }} {{ $player->last_name }}</p>
            @endif
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Colonne principale (2/3) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Identité --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h2 class="font-bold text-gray-800 mb-5 text-sm uppercase tracking-wide">Identité</h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Prénom *</label>
                            <input wire:model="first_name" type="text" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]" placeholder="Koné">
                            @error('first_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom *</label>
                            <input wire:model="last_name" type="text" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]" placeholder="Karim">
                            @error('last_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Date de naissance</label>
                            <input wire:model="birth_date" type="date" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                            @error('birth_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nationalité</label>
                            <input wire:model="nationality" type="text" maxlength="10" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]" placeholder="CI">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Téléphone</label>
                            <input wire:model="phone" type="tel" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]" placeholder="+225 07 00 00 00">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                            <input wire:model="email" type="email" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]" placeholder="joueur@email.com">
                            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Contact urgence --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h2 class="font-bold text-gray-800 mb-5 text-sm uppercase tracking-wide">Contact d'urgence</h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom du contact</label>
                            <input wire:model="emergency_contact" type="text" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Téléphone d'urgence</label>
                            <input wire:model="emergency_phone" type="tel" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        </div>
                    </div>
                </div>

                {{-- Licence --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h2 class="font-bold text-gray-800 mb-5 text-sm uppercase tracking-wide">Licence</h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Numéro de licence</label>
                            <input wire:model="license_number" type="text" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Date d'expiration</label>
                            <input wire:model="license_expires_at" type="date" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        </div>
                    </div>
                </div>

            </div>

            {{-- Colonne latérale (1/3) --}}
            <div class="space-y-6">

                {{-- Photo --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h2 class="font-bold text-gray-800 mb-5 text-sm uppercase tracking-wide">Photo</h2>
                    <div class="flex flex-col items-center gap-4">
                        @if($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="w-24 h-24 rounded-full object-cover shadow-md" alt="Aperçu">
                        @elseif($existingPhoto)
                            <img src="{{ Storage::url($existingPhoto) }}" class="w-24 h-24 rounded-full object-cover shadow-md" alt="Photo actuelle">
                        @else
                            <div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center text-2xl font-bold text-gray-400">
                                {{ strtoupper(substr($first_name ?: 'J', 0, 1) . substr($last_name ?: 'P', 0, 1)) }}
                            </div>
                        @endif
                        <label class="cursor-pointer text-sm font-semibold text-[#1E3A5F] hover:underline">
                            Choisir une photo
                            <input wire:model="photo" type="file" accept="image/*" class="hidden">
                        </label>
                        @error('photo') <p class="text-xs text-red-500 text-center">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-400 text-center">JPG, PNG — max 2 Mo</p>
                    </div>
                </div>

                {{-- Caractéristiques sportives --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h2 class="font-bold text-gray-800 mb-5 text-sm uppercase tracking-wide">Sportif</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catégorie</label>
                            <select wire:model.live="category_id" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                                <option value="">— Aucune —</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Équipe</label>
                            <select wire:model="team_id" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                                <option value="">— Aucune —</option>
                                @foreach($teams as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Poste</label>
                            <select wire:model="position" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                                <option value="">— Aucun —</option>
                                @foreach(['Gardien','Défenseur','Milieu','Attaquant'] as $pos)
                                    <option value="{{ $pos }}">{{ $pos }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">N° maillot</label>
                                <input wire:model="jersey_number" type="number" min="1" max="99"
                                       class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pied fort</label>
                                <select wire:model="foot" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                                    <option value="right">Droit</option>
                                    <option value="left">Gauche</option>
                                    <option value="both">Les deux</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Statut</label>
                            <select wire:model="status" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                                <option value="active">Actif</option>
                                <option value="injured">Blessé</option>
                                <option value="suspended">Suspendu</option>
                                <option value="loaned">Prêté</option>
                                <option value="transferred">Transféré</option>
                                <option value="former">Ancien joueur</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 justify-end pt-2">
            <a href="{{ route('club.players.index') }}" wire:navigate
               class="px-6 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                Annuler
            </a>
            <button type="submit"
                    class="px-6 py-2.5 text-sm font-bold text-white rounded-xl transition-colors shadow-sm"
                    style="background-color: var(--club-primary);">
                <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Enregistrer les modifications' : 'Créer le joueur' }}</span>
                <span wire:loading wire:target="save">Enregistrement…</span>
            </button>
        </div>
    </form>
</div>

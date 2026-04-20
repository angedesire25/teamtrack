<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Utilisateurs</h1>
            <p class="text-gray-500 text-sm mt-0.5">Gérez les accès à votre espace club</p>
        </div>
        <button wire:click="openInvite"
                class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl shadow-sm"
                style="background-color: var(--club-primary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Inviter un utilisateur
        </button>
    </div>

    {{-- Grille utilisateurs --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($users as $user)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5
                    {{ !$user->is_active ? 'opacity-60' : '' }}">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                         style="background-color: var(--club-primary);">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 truncate text-sm">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="flex gap-1">
                    <button wire:click="openEdit({{ $user->id }})" class="p-1.5 text-gray-400 hover:text-[#1E3A5F] hover:bg-gray-100 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full font-semibold">
                    {{ $user->primaryRoleLabel() }}
                </span>
                <button wire:click="toggleActive({{ $user->id }})"
                        class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors
                               {{ $user->is_active
                                    ? 'border-emerald-200 text-emerald-600 bg-emerald-50 hover:bg-emerald-100'
                                    : 'border-gray-200 text-gray-500 bg-gray-50 hover:bg-gray-100' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                </button>
            </div>
        </div>
        @empty
        <div class="sm:col-span-2 lg:col-span-3 bg-white rounded-2xl border border-gray-200 p-16 text-center shadow-sm">
            <p class="text-gray-500 font-medium">Aucun utilisateur</p>
            <button wire:click="openInvite" class="mt-2 text-sm font-semibold text-[#1E3A5F] hover:underline">Inviter le premier →</button>
        </div>
        @endforelse
    </div>

    {{-- Modal invitation / édition --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">{{ $editingId ? 'Modifier l\'utilisateur' : 'Inviter un utilisateur' }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Mot de passe généré (après création) --}}
            @if($generatedPassword)
            <div class="mx-6 mt-5 bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                <p class="text-sm font-bold text-emerald-700 mb-1">Utilisateur créé !</p>
                <p class="text-xs text-emerald-600 mb-2">Communiquez ce mot de passe temporaire à l'utilisateur :</p>
                <div class="flex items-center gap-2 bg-white border border-emerald-200 rounded-lg px-3 py-2">
                    <code class="text-sm font-mono text-gray-800 flex-1">{{ $generatedPassword }}</code>
                    <button onclick="navigator.clipboard.writeText('{{ $generatedPassword }}')"
                            class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold">Copier</button>
                </div>
                <button wire:click="$set('showModal', false)"
                        class="mt-3 w-full py-2 text-sm font-bold text-white rounded-xl"
                        style="background-color: var(--club-primary);">Fermer</button>
            </div>
            <div class="pb-5"></div>
            @else
            <form wire:submit="save" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom complet *</label>
                    <input wire:model="name" type="text" autofocus
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                           placeholder="Prénom Nom">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email *</label>
                    <input wire:model="email" type="email"
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                           placeholder="utilisateur@email.com">
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Rôle *</label>
                    <select wire:model="role" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        @foreach(\App\Livewire\Club\Users\Index::ROLES as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                @if($editingId)
                <div class="flex items-center gap-3">
                    <input wire:model="is_active" id="is_active" type="checkbox"
                           class="w-4 h-4 rounded border-gray-300 text-[#1E3A5F] focus:ring-[#1E3A5F]/30">
                    <label for="is_active" class="text-sm font-semibold text-gray-700">Compte actif</label>
                </div>
                @else
                <p class="text-xs text-gray-400 bg-gray-50 rounded-xl px-4 py-3">
                    Un mot de passe temporaire sera généré et affiché à l'écran après la création.
                </p>
                @endif
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">
                        {{ $editingId ? 'Enregistrer' : 'Créer l\'accès' }}
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
    @endif
</div>

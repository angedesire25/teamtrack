<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Personnel</h1>
            <p class="text-gray-500 text-sm mt-0.5">Staff technique et administratif du club</p>
        </div>
        <button wire:click="openCreate"
                class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl shadow-sm self-start sm:self-auto"
                style="background-color: var(--club-primary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter un membre
        </button>
    </div>

    {{-- Alerte contrats expirant --}}
    @if($expiring > 0)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-3.5 mb-5 flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p class="text-sm text-amber-700 font-medium">
            <strong>{{ $expiring }}</strong> contrat(s) expire(nt) dans les 30 prochains jours.
        </p>
    </div>
    @endif

    {{-- Recherche --}}
    <div class="relative mb-5 max-w-sm">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Nom, prénom, rôle…"
               class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide">Membre</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide hidden md:table-cell">Rôle</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide hidden lg:table-cell">Contrat</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide hidden lg:table-cell">Contact</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($staff as $s)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                     style="background-color: var(--club-primary);">
                                    {{ strtoupper(substr($s->first_name,0,1).substr($s->last_name,0,1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $s->first_name }} {{ $s->last_name }}</p>
                                    <p class="text-xs text-gray-400 md:hidden">{{ $s->role }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 hidden md:table-cell">
                            <span class="text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full font-medium">{{ $s->role }}</span>
                        </td>
                        <td class="px-4 py-3.5 hidden lg:table-cell">
                            @if($s->contract_end)
                                <p class="text-xs text-gray-500 font-medium">{{ $s->contract_type ?? 'Contrat' }}</p>
                                <p class="text-xs {{ $s->isContractExpiringSoon() ? 'text-amber-500 font-semibold' : 'text-gray-400' }}">
                                    Fin : {{ $s->contract_end->format('d/m/Y') }}
                                    @if($s->isContractExpiringSoon())
                                        · Dans {{ $s->daysUntilContractEnd() }} jours
                                    @endif
                                </p>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 hidden lg:table-cell">
                            @if($s->phone)
                                <p class="text-xs text-gray-600">{{ $s->phone }}</p>
                            @endif
                            @if($s->email)
                                <p class="text-xs text-gray-400">{{ $s->email }}</p>
                            @endif
                            @if(!$s->phone && !$s->email)
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1 justify-end">
                                <button wire:click="openEdit({{ $s->id }})" class="p-1.5 text-gray-400 hover:text-[#1E3A5F] hover:bg-gray-100 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $s->id }})" wire:confirm="Supprimer {{ $s->first_name }} {{ $s->last_name }} ?"
                                        class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-14 text-center">
                            <p class="text-gray-500 font-medium">Aucun membre du staff trouvé</p>
                            <button wire:click="openCreate" class="mt-2 text-sm font-semibold text-[#1E3A5F] hover:underline">Ajouter le premier →</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($staff->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $staff->links() }}</div>
        @endif
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg my-auto" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">{{ $editingId ? 'Modifier le membre' : 'Nouveau membre du staff' }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form wire:submit="save" class="px-6 py-5 space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Prénom *</label>
                        <input wire:model="first_name" type="text" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        @error('first_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom *</label>
                        <input wire:model="last_name" type="text" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        @error('last_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Rôle *</label>
                    <select wire:model="role" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        <option value="">— Choisir —</option>
                        @foreach(\App\Livewire\Club\Staff\Index::ROLES as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Téléphone</label>
                        <input wire:model="phone" type="tel" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <input wire:model="email" type="email" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Contrat (facultatif)</p>
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Type</label>
                            <input wire:model="contract_type" type="text" placeholder="CDI, CDD…" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Début</label>
                            <input wire:model="contract_start" type="date" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Fin</label>
                            <input wire:model="contract_end" type="date" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                            @error('contract_end') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal', false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">
                        {{ $editingId ? 'Enregistrer' : 'Ajouter' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

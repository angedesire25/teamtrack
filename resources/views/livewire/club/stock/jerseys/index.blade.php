<div>
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('club.stock.overview') }}" wire:navigate class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-extrabold text-gray-900">Maillots</h1>
            <p class="text-gray-500 text-sm mt-0.5">Catalogue et attributions aux joueurs</p>
        </div>
        <button wire:click="openCreate"
                class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl shadow-sm"
                style="background-color: var(--club-primary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajouter
        </button>
    </div>

    {{-- Onglets --}}
    <div class="flex gap-1 mb-5 bg-gray-100 rounded-xl p-1 w-fit">
        <button wire:click="$set('tab','stock')" class="px-4 py-2 text-sm rounded-lg transition-all {{ $tab==='stock' ? 'bg-white shadow font-bold text-gray-900' : 'text-gray-500' }}">Stock</button>
        <button wire:click="$set('tab','assignments')" class="px-4 py-2 text-sm rounded-lg transition-all {{ $tab==='assignments' ? 'bg-white shadow font-bold text-gray-900' : 'text-gray-500' }}">Attributions</button>
    </div>

    {{-- Filtres --}}
    <div class="flex flex-wrap gap-3 mb-5">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Rechercher…"
                   class="pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
        </div>
        <select wire:model.live="filterType" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20">
            <option value="">Tous types</option>
            @foreach(['home'=>'Domicile','away'=>'Extérieur','training'=>'Entraînement','keeper'=>'Gardien','other'=>'Autre'] as $val=>$label)
                <option value="{{ $val }}">{{ $label }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterSeason" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20">
            <option value="">Toutes saisons</option>
            @foreach($seasons as $s)
                <option value="{{ $s }}">{{ $s }}</option>
            @endforeach
        </select>
        @if($tab === 'assignments' && $filterSeason)
        <button wire:click="returnAll('{{ $filterSeason }}')" wire:confirm="Retourner tous les maillots de la saison {{ $filterSeason }} ?"
                class="px-3 py-2 text-sm font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100">
            Retour fin de saison
        </button>
        @endif
    </div>

    @if($tab === 'stock')
    {{-- Grille maillots --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($jerseys as $jersey)
        @php $isLow = $jersey->quantity_available <= $jersey->low_stock_threshold; @endphp
        <div class="bg-white rounded-2xl border {{ $isLow ? 'border-red-200' : 'border-gray-200' }} shadow-sm p-5">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <p class="font-bold text-gray-900 text-sm">{{ $jersey->name }}</p>
                        @if($isLow)
                        <span class="text-xs bg-red-50 text-red-600 border border-red-100 px-2 py-0.5 rounded-full font-semibold">Stock bas</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full font-medium">{{ $jersey->typeLabel() }}</span>
                        <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full font-medium">{{ $jersey->size }}</span>
                        @if($jersey->season)
                        <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full font-medium">{{ $jersey->season }}</span>
                        @endif
                        @if($jersey->color)
                        <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full font-medium">{{ $jersey->color }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-1">
                    <button wire:click="openEdit({{ $jersey->id }})" class="p-1.5 text-gray-400 hover:text-[#1E3A5F] hover:bg-gray-100 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                    <button wire:click="delete({{ $jersey->id }})" wire:confirm="Supprimer ce maillot ?" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                <div class="flex gap-4 text-center">
                    <div>
                        <p class="text-lg font-extrabold {{ $isLow ? 'text-red-600' : 'text-gray-900' }}">{{ $jersey->quantity_available }}</p>
                        <p class="text-xs text-gray-400">Dispo</p>
                    </div>
                    <div>
                        <p class="text-lg font-extrabold text-gray-400">{{ $jersey->quantity_total }}</p>
                        <p class="text-xs text-gray-400">Total</p>
                    </div>
                    <div>
                        <p class="text-lg font-extrabold text-gray-400">{{ $jersey->active_assignments_count }}</p>
                        <p class="text-xs text-gray-400">Attribués</p>
                    </div>
                </div>
                <button wire:click="openAssign({{ $jersey->id }})"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white rounded-lg"
                        style="background-color: var(--club-primary);"
                        {{ $jersey->quantity_available < 1 ? 'disabled' : '' }}>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Attribuer
                </button>
            </div>
            @if($jersey->supplier)
            <p class="text-xs text-gray-400 mt-2">Fournisseur : {{ $jersey->supplier->name }}</p>
            @endif
        </div>
        @empty
        <div class="sm:col-span-2 lg:col-span-3 bg-white rounded-2xl border border-gray-200 p-16 text-center shadow-sm">
            <p class="text-gray-500 font-medium">Aucun maillot trouvé</p>
            <button wire:click="openCreate" class="mt-2 text-sm font-semibold text-[#1E3A5F] hover:underline">Ajouter le premier →</button>
        </div>
        @endforelse
    </div>
    @if($jerseys->hasPages())
    <div class="mt-4">{{ $jerseys->links() }}</div>
    @endif

    @else
    {{-- Table attributions --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide">Joueur</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide">Maillot</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide hidden md:table-cell">N°</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide hidden md:table-cell">Saison</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide hidden lg:table-cell">Attribué le</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide">Statut</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($assignments as $a)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $a->player->first_name }} {{ $a->player->last_name }}</td>
                        <td class="px-4 py-3.5">
                            <p class="font-medium text-gray-800">{{ $a->jersey->name }}</p>
                            <p class="text-xs text-gray-400">{{ $a->jersey->size }}</p>
                        </td>
                        <td class="px-4 py-3.5 hidden md:table-cell text-gray-600">{{ $a->jersey_number ?? '—' }}</td>
                        <td class="px-4 py-3.5 hidden md:table-cell text-gray-600">{{ $a->season ?? '—' }}</td>
                        <td class="px-4 py-3.5 hidden lg:table-cell text-gray-500 text-xs">{{ $a->assigned_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3.5">
                            @if($a->returned_at)
                            <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full font-medium">Retourné le {{ $a->returned_at->format('d/m/Y') }}</span>
                            @else
                            <span class="text-xs bg-blue-50 text-blue-700 border border-blue-100 px-2.5 py-1 rounded-full font-semibold">En cours</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            @if(!$a->returned_at)
                            <button wire:click="openReturn({{ $a->id }})"
                                    class="text-xs font-semibold text-gray-500 hover:text-[#1E3A5F] px-2.5 py-1 rounded-lg hover:bg-gray-100">
                                Retour
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-14 text-center text-gray-400">Aucune attribution</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($assignments->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $assignments->links() }}</div>
        @endif
    </div>
    @endif

    {{-- Modal catalogue maillot --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg my-auto" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">{{ $editingId ? 'Modifier le maillot' : 'Ajouter un maillot' }}</h3>
                <button wire:click="$set('showModal',false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="saveJersey" class="px-6 py-5 space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom *</label>
                        <input wire:model="name" type="text" placeholder="Maillot domicile 2025"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Type *</label>
                        <select wire:model="type" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                            @foreach(['home'=>'Domicile','away'=>'Extérieur','training'=>'Entraînement','keeper'=>'Gardien','other'=>'Autre'] as $val=>$label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Taille *</label>
                        <select wire:model="size" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                            @foreach(\App\Models\Jersey::sizes() as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Saison</label>
                        <input wire:model="season" type="text" placeholder="2024-2025"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Couleur</label>
                        <input wire:model="color" type="text" placeholder="Bleu/Blanc"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                </div>
                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Qté totale *</label>
                        <input wire:model="quantity_total" type="number" min="0"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        @error('quantity_total') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Disponible *</label>
                        <input wire:model="quantity_available" type="number" min="0"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Seuil alerte</label>
                        <input wire:model="low_stock_threshold" type="number" min="0"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Prix unitaire (F CFA)</label>
                        <input wire:model="unit_price" type="number" min="0" step="100"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Fournisseur</label>
                        <select wire:model="supplier_id" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                            <option value="">— Aucun —</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Notes</label>
                    <textarea wire:model="notes" rows="2" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal',false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">
                        {{ $editingId ? 'Enregistrer' : 'Ajouter' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal attribution --}}
    @if($showAssignModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Attribuer un maillot</h3>
                <button wire:click="$set('showAssignModal',false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="saveAssignment" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Joueur *</label>
                    <select wire:model="assignPlayerId" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        <option value="">— Sélectionner —</option>
                        @foreach($players as $p)
                            <option value="{{ $p->id }}">{{ $p->first_name }} {{ $p->last_name }}</option>
                        @endforeach
                    </select>
                    @error('assignPlayerId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">N° maillot</label>
                        <input wire:model="assignNumber" type="text" placeholder="10"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Saison</label>
                        <input wire:model="assignSeason" type="text"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Date d'attribution *</label>
                    <input wire:model="assignDate" type="date" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showAssignModal',false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">Attribuer</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal retour maillot --}}
    @if($showReturnModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Enregistrer le retour</h3>
                <button wire:click="$set('showReturnModal',false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="saveReturn" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">État du maillot *</label>
                    <select wire:model="returnCondition" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        <option value="good">Bon état</option>
                        <option value="damaged">Endommagé</option>
                        <option value="lost">Perdu</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Date de retour *</label>
                    <input wire:model="returnDate" type="date" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showReturnModal',false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">Confirmer le retour</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

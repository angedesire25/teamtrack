<div>
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('club.stock.overview') }}" wire:navigate class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-extrabold text-gray-900">Matériel</h1>
            <p class="text-gray-500 text-sm mt-0.5">Catalogue, état et mouvements</p>
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
        <button wire:click="$set('tab','items')" class="px-4 py-2 text-sm rounded-lg transition-all {{ $tab==='items' ? 'bg-white shadow font-bold text-gray-900' : 'text-gray-500' }}">Catalogue</button>
        <button wire:click="$set('tab','movements')" class="px-4 py-2 text-sm rounded-lg transition-all {{ $tab==='movements' ? 'bg-white shadow font-bold text-gray-900' : 'text-gray-500' }}">Mouvements</button>
    </div>

    @if($tab === 'items')
    {{-- Filtres --}}
    <div class="flex flex-wrap gap-3 mb-5">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Rechercher…"
                   class="pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
        </div>
        <select wire:model.live="filterCategory" class="text-sm border border-gray-200 rounded-xl px-3 py-2">
            <option value="">Toutes catégories</option>
            @foreach($categories as $c)
                <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterCondition" class="text-sm border border-gray-200 rounded-xl px-3 py-2">
            <option value="">Tous états</option>
            <option value="new">Neuf</option>
            <option value="good">Bon état</option>
            <option value="repair">À réparer</option>
            <option value="out_of_service">Hors service</option>
        </select>
    </div>

    {{-- Table articles --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide">Article</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide hidden md:table-cell">Catégorie</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide">État</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide">Stock</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide hidden lg:table-cell">Fournisseur</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-500 text-xs uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($items as $item)
                    @php $isLow = $item->quantity_available <= $item->low_stock_threshold; @endphp
                    <tr class="hover:bg-gray-50 {{ $isLow ? 'bg-red-50/30' : '' }}">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $item->name }}</p>
                                    @if($item->reference)
                                    <p class="text-xs text-gray-400">Réf : {{ $item->reference }}</p>
                                    @endif
                                </div>
                                @if($isLow)
                                <span class="text-xs bg-red-50 text-red-600 border border-red-100 px-2 py-0.5 rounded-full font-semibold flex-shrink-0">Alerte</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3.5 hidden md:table-cell">
                            <span class="text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full font-medium">{{ $item->category }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-xs {{ $item->conditionColor() }} border border-current/20 px-2.5 py-1 rounded-full font-semibold">{{ $item->conditionLabel() }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div>
                                    <p class="font-bold {{ $isLow ? 'text-red-600' : 'text-gray-900' }}">{{ $item->quantity_available }}</p>
                                    <p class="text-xs text-gray-400">/ {{ $item->quantity_total }}</p>
                                </div>
                                <div class="flex gap-1">
                                    <button wire:click="openMovement({{ $item->id }},'out')" title="Sortie"
                                            class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    </button>
                                    <button wire:click="openMovement({{ $item->id }},'in')" title="Entrée"
                                            class="p-1.5 text-emerald-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18m-6 4v1a3 3 0 003 3h-4a3 3 0 003-3v-1"/></svg>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 hidden lg:table-cell text-xs text-gray-500">{{ $item->supplier?->name ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="flex justify-end gap-1">
                                <button wire:click="openEdit({{ $item->id }})" class="p-1.5 text-gray-400 hover:text-[#1E3A5F] hover:bg-gray-100 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <button wire:click="delete({{ $item->id }})" wire:confirm="Supprimer {{ $item->name }} ?" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-14 text-center text-gray-400 font-medium">Aucun article trouvé</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $items->links() }}</div>
        @endif
    </div>

    @else
    {{-- Table mouvements --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide">Date</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide">Article</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide">Type</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide">Qté</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide hidden md:table-cell">Raison</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide hidden lg:table-cell">Retour prévu</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($movements as $m)
                    <tr class="hover:bg-gray-50 {{ $m->isOverdue() ? 'bg-red-50/30' : '' }}">
                        <td class="px-5 py-3.5 text-xs text-gray-500">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3.5 font-semibold text-gray-900">{{ $m->item?->name ?? '—' }}</td>
                        <td class="px-4 py-3.5">
                            <span class="text-xs {{ $m->typeColor() }} px-2.5 py-1 rounded-full font-semibold">{{ $m->typeLabel() }}</span>
                            @if($m->isOverdue())
                            <span class="ml-1 text-xs bg-red-50 text-red-600 border border-red-100 px-2 py-0.5 rounded-full font-semibold">En retard</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 font-bold text-gray-900">{{ $m->quantity }}</td>
                        <td class="px-4 py-3.5 hidden md:table-cell text-xs text-gray-500">{{ $m->reason ?? '—' }}</td>
                        <td class="px-4 py-3.5 hidden lg:table-cell text-xs text-gray-500">
                            {{ $m->expected_return_at?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5">
                            @if($m->type === 'out' && !$m->returned_at)
                            <button wire:click="markReturned({{ $m->id }})"
                                    class="text-xs font-semibold text-gray-500 hover:text-[#1E3A5F] px-2.5 py-1 rounded-lg hover:bg-gray-100">
                                Retour
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-14 text-center text-gray-400">Aucun mouvement</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($movements->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $movements->links() }}</div>
        @endif
    </div>
    @endif

    {{-- Modal article --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg my-auto" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">{{ $editingId ? 'Modifier l\'article' : 'Ajouter un article' }}</h3>
                <button wire:click="$set('showModal',false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="saveItem" class="px-6 py-5 space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom *</label>
                        <input wire:model="name" type="text" autofocus placeholder="Ballon match T5"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catégorie *</label>
                        <select wire:model="category" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                            <option value="">— Choisir —</option>
                            @foreach($categories as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                        @error('category') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">État *</label>
                    <select wire:model="condition" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        <option value="new">Neuf</option>
                        <option value="good">Bon état</option>
                        <option value="repair">À réparer</option>
                        <option value="out_of_service">Hors service</option>
                    </select>
                </div>
                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Qté totale *</label>
                        <input wire:model="quantity_total" type="number" min="0"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
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
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Réf. fournisseur</label>
                        <input wire:model="reference" type="text" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Prix unitaire (F CFA)</label>
                        <input wire:model="unit_price" type="number" min="0" step="100"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
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

    {{-- Modal mouvement --}}
    @if($showMovementModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Enregistrer un mouvement</h3>
                <button wire:click="$set('showMovementModal',false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="saveMovement" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Type *</label>
                    <select wire:model="movementType" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        <option value="out">Sortie</option>
                        <option value="in">Entrée</option>
                        <option value="return">Retour</option>
                        <option value="adjustment">Ajustement inventaire</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Quantité *</label>
                        <input wire:model="movementQty" type="number" min="1"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        @error('movementQty') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Retour prévu</label>
                        <input wire:model="movementExpReturn" type="date"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Raison / Destination</label>
                    <input wire:model="movementReason" type="text" placeholder="Entraînement U17, achat fournisseur…"
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showMovementModal',false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

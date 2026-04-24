<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('club.finance.dashboard') }}" wire:navigate class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-extrabold text-gray-900">Dépenses</h1>
            <p class="text-gray-500 text-sm mt-0.5">Suivi des sorties de trésorerie</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="openCreateCat"
                    class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Catégorie
            </button>
            <button wire:click="openCreate"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl shadow-sm"
                    style="background-color: var(--club-primary);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouvelle dépense
            </button>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="flex flex-wrap gap-3 mb-5">
        <div class="relative flex-1 min-w-48">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Rechercher…"
                   class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
        </div>
        <select wire:model.live="categoryFilter" class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none bg-white">
            <option value="">Toutes catégories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="monthFilter" class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none bg-white">
            <option value="">Tous les mois</option>
            @foreach(range(1,12) as $m)
            <option value="{{ $m }}">{{ \Carbon\Carbon::create(null,$m)->translatedFormat('F') }}</option>
            @endforeach
        </select>
        <select wire:model.live="yearFilter" class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none bg-white font-semibold text-gray-700">
            @foreach(range(now()->year, now()->year - 4) as $y)
            <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>
    </div>

    {{-- Total --}}
    @if($totalFiltered > 0)
    <div class="bg-rose-50 border border-rose-100 rounded-xl px-4 py-2.5 mb-4 flex justify-between text-sm">
        <span class="text-rose-600">Total sur la sélection</span>
        <span class="font-extrabold text-rose-700">{{ number_format($totalFiltered,0,'.',' ') }} F</span>
    </div>
    @endif

    {{-- Catégories --}}
    @if($categories->isNotEmpty())
    <div class="flex flex-wrap gap-2 mb-5">
        @foreach($categories as $cat)
        <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-gray-700">
            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $cat->color }}"></span>
            {{ $cat->name }}
            <button wire:click="openEditCat({{ $cat->id }})" class="text-gray-300 hover:text-gray-600">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </button>
            <button wire:click="deleteCat({{ $cat->id }})" wire:confirm="Supprimer cette catégorie ?" class="text-gray-300 hover:text-red-400">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endforeach
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Catégorie</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Payé par</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($expenses as $e)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3.5 text-xs text-gray-400">{{ $e->date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3.5">
                        <p class="font-semibold text-gray-900">{{ $e->description }}</p>
                        @if($e->reference)<p class="text-xs text-gray-400">Réf : {{ $e->reference }}</p>@endif
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        @if($e->category)
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-700">
                            <span class="w-2 h-2 rounded-full" style="background-color:{{ $e->category->color }}"></span>
                            {{ $e->category->name }}
                        </span>
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-xs text-gray-500 hidden lg:table-cell">{{ $e->paid_by ?? '—' }}</td>
                    <td class="px-4 py-3.5 text-right font-bold text-rose-600">{{ number_format($e->amount,0,'.',' ') }} F</td>
                    <td class="px-4 py-3.5">
                        <div class="flex gap-1.5 justify-end">
                            <button wire:click="openEdit({{ $e->id }})" class="p-1.5 text-gray-400 hover:text-[#1E3A5F] hover:bg-gray-100 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <button wire:click="delete({{ $e->id }})" wire:confirm="Supprimer cette dépense ?" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Aucune dépense</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($expenses->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $expenses->links() }}</div>
        @endif
    </div>

    {{-- Modal Dépense --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg my-auto" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">{{ $editingId ? 'Modifier la dépense' : 'Nouvelle dépense' }}</h3>
                <button wire:click="$set('showModal',false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description *</label>
                    <input wire:model="description" type="text" autofocus
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                           placeholder="Ex : Achat de maillots saison 2025-26">
                    @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Montant (F) *</label>
                        <input wire:model="amount" type="number" min="1" step="100"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        @error('amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Date *</label>
                        <input wire:model="date" type="date"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catégorie</label>
                        <select wire:model="categoryId" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F] bg-white">
                            <option value="">Non catégorisée</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Payé par</label>
                        <input wire:model="paid_by" type="text" placeholder="Nom ou service"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Référence</label>
                    <input wire:model="reference" type="text" placeholder="N° facture, bon de commande…"
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Notes</label>
                    <textarea wire:model="notes" rows="2"
                              class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal',false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">{{ $editingId ? 'Enregistrer' : 'Créer' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal Catégorie --}}
    @if($showCatModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">{{ $editingCatId ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}</h3>
                <button wire:click="$set('showCatModal',false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="saveCat" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom *</label>
                    <input wire:model="catName" type="text" autofocus placeholder="Ex : Déplacements"
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    @error('catName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Couleur</label>
                    <div class="flex items-center gap-3">
                        <input wire:model.live="catColor" type="color"
                               class="w-10 h-10 border border-gray-200 rounded-lg cursor-pointer">
                        <span class="text-xs text-gray-500 font-mono">{{ $catColor }}</span>
                        <div class="flex gap-1.5 ml-2">
                            @foreach(['#7C3AED','#2563EB','#EA580C','#16A34A','#0891B2','#DC2626','#D97706','#6B7280'] as $preset)
                            <button type="button" wire:click="$set('catColor','{{ $preset }}')"
                                    class="w-5 h-5 rounded-full border-2 {{ $catColor === $preset ? 'border-gray-900' : 'border-transparent' }}"
                                    style="background-color:{{ $preset }}"></button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showCatModal',false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">{{ $editingCatId ? 'Enregistrer' : 'Créer' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

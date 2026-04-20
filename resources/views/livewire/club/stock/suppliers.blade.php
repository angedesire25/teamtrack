<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('club.stock.overview') }}" wire:navigate class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-extrabold text-gray-900">Fournisseurs</h1>
            <p class="text-gray-500 text-sm mt-0.5">Répertoire et bons de commande</p>
        </div>
        <button wire:click="openCreate"
                class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl shadow-sm"
                style="background-color: var(--club-primary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajouter
        </button>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($suppliers as $supplier)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 {{ !$supplier->is_active ? 'opacity-60' : '' }}">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                         style="background-color: var(--club-primary);">
                        {{ strtoupper(substr($supplier->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 text-sm">{{ $supplier->name }}</p>
                        @if($supplier->contact_name)
                        <p class="text-xs text-gray-400">{{ $supplier->contact_name }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex gap-1">
                    <button wire:click="openEdit({{ $supplier->id }})" class="p-1.5 text-gray-400 hover:text-[#1E3A5F] hover:bg-gray-100 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                    <button wire:click="delete({{ $supplier->id }})" wire:confirm="Supprimer ce fournisseur ?" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>

            <div class="space-y-1 text-xs text-gray-500 mb-4">
                @if($supplier->email)
                <p class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    {{ $supplier->email }}
                </p>
                @endif
                @if($supplier->phone)
                <p class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    {{ $supplier->phone }}
                </p>
                @endif
                @if($supplier->address)
                <p class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    {{ $supplier->address }}
                </p>
                @endif
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <div class="flex gap-3 text-xs text-gray-500">
                    <span>{{ $supplier->jerseys_count }} maillot(s)</span>
                    <span>{{ $supplier->equipment_items_count }} article(s)</span>
                </div>
                <a href="{{ route('club.stock.purchase-order-pdf', ['supplier_id' => $supplier->id]) }}"
                   class="flex items-center gap-1 text-xs font-semibold text-[#1E3A5F] hover:underline">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Bon de commande
                </a>
            </div>
        </div>
        @empty
        <div class="sm:col-span-2 lg:col-span-3 bg-white rounded-2xl border border-gray-200 p-16 text-center shadow-sm">
            <p class="text-gray-500 font-medium">Aucun fournisseur</p>
            <button wire:click="openCreate" class="mt-2 text-sm font-semibold text-[#1E3A5F] hover:underline">Ajouter le premier →</button>
        </div>
        @endforelse
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">{{ $editingId ? 'Modifier le fournisseur' : 'Nouveau fournisseur' }}</h3>
                <button wire:click="$set('showModal',false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom *</label>
                    <input wire:model="name" type="text" autofocus class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Contact</label>
                        <input wire:model="contact_name" type="text" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Téléphone</label>
                        <input wire:model="phone" type="tel" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                    <input wire:model="email" type="email" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Adresse</label>
                    <input wire:model="address" type="text" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Notes</label>
                    <textarea wire:model="notes" rows="2" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"></textarea>
                </div>
                @if($editingId)
                <div class="flex items-center gap-3">
                    <input wire:model="is_active" id="sup_active" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#1E3A5F] focus:ring-[#1E3A5F]/30">
                    <label for="sup_active" class="text-sm font-semibold text-gray-700">Fournisseur actif</label>
                </div>
                @endif
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal',false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">{{ $editingId ? 'Enregistrer' : 'Ajouter' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

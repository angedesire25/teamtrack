{{-- Vue : Édition d'un plan --}}

<x-slot:pageTitle>Éditer — {{ $this->plan->name }}</x-slot:pageTitle>

<div class="max-w-2xl space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('superadmin.plans.index') }}"
           class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour aux plans
        </a>
    </div>

    <form wire:submit="save" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">

        <h2 class="text-sm font-semibold text-gray-900 border-b border-gray-100 pb-3">Modifier le plan</h2>

        <div class="grid grid-cols-2 gap-5">

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom <span class="text-red-500">*</span></label>
                <input wire:model="name" type="text"
                       class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30 focus:border-[#1E3A5F] @error('name') border-red-300 @else border-gray-200 @enderror">
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Prix (FCFA) <span class="text-red-500">*</span></label>
                <input wire:model="price" type="number" min="0"
                       class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30 focus:border-[#1E3A5F] @error('price') border-red-300 @else border-gray-200 @enderror">
                @error('price') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Cycle de facturation</label>
                <select wire:model="billing_cycle"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30">
                    <option value="monthly">Mensuel</option>
                    <option value="yearly">Annuel</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Max. joueurs <span class="text-red-500">*</span></label>
                <input wire:model="max_players" type="number" min="1"
                       class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30 focus:border-[#1E3A5F] @error('max_players') border-red-300 @else border-gray-200 @enderror">
                @error('max_players') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Max. utilisateurs <span class="text-red-500">*</span></label>
                <input wire:model="max_users" type="number" min="1"
                       class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30 focus:border-[#1E3A5F] @error('max_users') border-red-300 @else border-gray-200 @enderror">
                @error('max_users') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Fonctionnalités <span class="text-gray-400 font-normal">(une par ligne)</span></label>
                <textarea wire:model="features_raw" rows="4"
                          class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30 focus:border-[#1E3A5F] resize-none"></textarea>
            </div>

            <div class="col-span-2 flex items-center gap-3">
                <input wire:model="is_active" type="checkbox" id="is_active"
                       class="w-4 h-4 rounded border-gray-300 text-[#1E3A5F] focus:ring-[#1E3A5F]">
                <label for="is_active" class="text-sm text-gray-700">Plan actif</label>
            </div>

        </div>

        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
            <a href="{{ route('superadmin.plans.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                Annuler
            </a>
            <button type="submit"
                    class="px-5 py-2 text-sm font-medium text-white bg-[#1E3A5F] rounded-lg hover:bg-[#162d4a] transition-colors">
                <span wire:loading.remove>Enregistrer</span>
                <span wire:loading>Enregistrement…</span>
            </button>
        </div>

    </form>
</div>

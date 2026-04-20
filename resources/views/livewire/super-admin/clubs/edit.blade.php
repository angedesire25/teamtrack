{{--
    Vue : Édition d'un club — Super Admin
--}}

<x-slot:pageTitle>Éditer — {{ $this->tenant->name }}</x-slot:pageTitle>

<div class="max-w-3xl space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('superadmin.clubs.show', $this->tenant) }}"
           class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour au détail
        </a>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
            <h2 class="text-sm font-semibold text-gray-900 border-b border-gray-100 pb-3">
                Informations du club
            </h2>

            <div class="grid grid-cols-2 gap-5">

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom du club <span class="text-red-500">*</span></label>
                    <input wire:model="name" type="text"
                           class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30 focus:border-[#1E3A5F] @error('name') border-red-300 @else border-gray-200 @enderror">
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Sous-domaine <span class="text-red-500">*</span></label>
                    <div class="flex items-center border rounded-lg overflow-hidden @error('subdomain') border-red-300 @else border-gray-200 @enderror focus-within:ring-2 focus-within:ring-[#1E3A5F]/30">
                        <input wire:model="subdomain" type="text"
                               class="flex-1 px-3 py-2 text-sm focus:outline-none">
                        <span class="px-3 py-2 bg-gray-50 text-gray-500 text-sm border-l border-gray-200">.teamtrack.test</span>
                    </div>
                    @error('subdomain') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email du club <span class="text-red-500">*</span></label>
                    <input wire:model="email" type="email"
                           class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30 focus:border-[#1E3A5F] @error('email') border-red-300 @else border-gray-200 @enderror">
                    @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                    <input wire:model="phone" type="text"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ville</label>
                    <input wire:model="city" type="text"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pays <span class="text-red-500">*</span></label>
                    <input wire:model="country" type="text"
                           class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30 focus:border-[#1E3A5F] @error('country') border-red-300 @else border-gray-200 @enderror">
                    @error('country') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Plan <span class="text-red-500">*</span></label>
                    <select wire:model="plan_id"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30 focus:border-[#1E3A5F] @error('plan_id') border-red-300 @else border-gray-200 @enderror">
                        <option value="">Choisir un plan…</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }} — {{ number_format($plan->price, 0, ',', ' ') }} FCFA</option>
                        @endforeach
                    </select>
                    @error('plan_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut <span class="text-red-500">*</span></label>
                    <select wire:model.live="status"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30">
                        <option value="active">Actif</option>
                        <option value="trial">Période d'essai</option>
                        <option value="suspended">Suspendu</option>
                        <option value="cancelled">Annulé</option>
                    </select>
                </div>

                @if ($status === 'trial')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Fin de la période d'essai</label>
                        <input wire:model="trial_ends_at" type="date"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30">
                    </div>
                @endif

            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('superadmin.clubs.show', $this->tenant) }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                Annuler
            </a>
            <button type="submit"
                    class="px-5 py-2 text-sm font-medium text-white bg-[#1E3A5F] rounded-lg hover:bg-[#162d4a] transition-colors">
                <span wire:loading.remove>Enregistrer les modifications</span>
                <span wire:loading>Enregistrement…</span>
            </button>
        </div>

    </form>
</div>

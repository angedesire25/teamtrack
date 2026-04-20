<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('club.donations.dashboard') }}" wire:navigate class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-extrabold text-gray-900">Campagnes de dons</h1>
            <p class="text-gray-500 text-sm mt-0.5">Gérez vos objectifs de collecte</p>
        </div>
        <button wire:click="openCreate"
                class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl shadow-sm"
                style="background-color: var(--club-primary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvelle campagne
        </button>
    </div>

    <div class="space-y-4">
        @forelse($campaigns as $campaign)
        @php $pct = $campaign->progressPercent(); @endphp
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 {{ !$campaign->is_active ? 'opacity-60' : '' }}">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-bold text-gray-900">{{ $campaign->title }}</h3>
                        <span class="text-xs {{ $campaign->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-500' }} px-2 py-0.5 rounded-full font-semibold">
                            {{ $campaign->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($campaign->isExpired())
                        <span class="text-xs bg-red-50 text-red-600 border border-red-200 px-2 py-0.5 rounded-full font-semibold">Expirée</span>
                        @endif
                    </div>
                    @if($campaign->description)
                    <p class="text-sm text-gray-500 mb-3 line-clamp-2">{{ $campaign->description }}</p>
                    @endif

                    <div class="flex flex-wrap gap-4 text-sm">
                        <div>
                            <p class="text-xs text-gray-400">Collecté</p>
                            <p class="font-bold text-emerald-600">{{ number_format($campaign->collected ?? 0, 0, '.', ' ') }} F</p>
                        </div>
                        @if($campaign->goal_amount)
                        <div>
                            <p class="text-xs text-gray-400">Objectif</p>
                            <p class="font-bold text-gray-700">{{ number_format($campaign->goal_amount, 0, '.', ' ') }} F</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-400">Dons</p>
                            <p class="font-bold text-gray-700">{{ $campaign->completed_donations_count }}</p>
                        </div>
                        @if($campaign->end_date)
                        <div>
                            <p class="text-xs text-gray-400">Fin</p>
                            <p class="font-bold text-gray-700">{{ $campaign->end_date->format('d/m/Y') }}</p>
                        </div>
                        @endif
                    </div>

                    @if($campaign->goal_amount)
                    <div class="mt-3">
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all" style="width:{{ $pct }}%; background-color: var(--club-primary);"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">{{ $pct }}% de l'objectif atteint</p>
                    </div>
                    @endif
                </div>

                <div class="flex gap-2 items-center flex-shrink-0">
                    <a href="{{ url('/dons/'.$campaign->id) }}" target="_blank"
                       class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-[#1E3A5F] bg-blue-50 border border-blue-100 rounded-lg hover:bg-blue-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Page publique
                    </a>
                    <button wire:click="toggleActive({{ $campaign->id }})"
                            class="p-1.5 text-gray-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $campaign->is_active ? 'M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z' : 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z' }}"/></svg>
                    </button>
                    <button wire:click="openEdit({{ $campaign->id }})" class="p-1.5 text-gray-400 hover:text-[#1E3A5F] hover:bg-gray-100 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                    <button wire:click="delete({{ $campaign->id }})" wire:confirm="Supprimer cette campagne ?" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center shadow-sm">
            <p class="text-gray-500 font-medium">Aucune campagne créée</p>
            <button wire:click="openCreate" class="mt-2 text-sm font-semibold text-[#1E3A5F] hover:underline">Créer la première →</button>
        </div>
        @endforelse
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg my-auto" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">{{ $editingId ? 'Modifier la campagne' : 'Nouvelle campagne' }}</h3>
                <button wire:click="$set('showModal',false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Titre *</label>
                    <input wire:model="title" type="text" autofocus placeholder="Ex : Rénovation du terrain d'entraînement"
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                    <textarea wire:model="description" rows="3"
                              class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                              placeholder="Décrivez l'objectif de cette collecte…"></textarea>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Objectif (F CFA)</label>
                        <input wire:model="goal_amount" type="number" min="0" step="1000"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                               placeholder="1 000 000">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Montants suggérés</label>
                        <input wire:model="suggested_amounts" type="text"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                               placeholder="5000,10000,25000,50000">
                        <p class="text-xs text-gray-400 mt-1">Séparés par des virgules</p>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Date de début</label>
                        <input wire:model="start_date" type="date" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Date de fin</label>
                        <input wire:model="end_date" type="date" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                        @error('end_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="space-y-2 border-t border-gray-100 pt-4">
                    @foreach(['is_active'=>'Campagne active','allow_recurring'=>'Autoriser les dons récurrents','allow_anonymous'=>'Autoriser les dons anonymes'] as $field=>$label)
                    <div class="flex items-center gap-3">
                        <input wire:model="{{ $field }}" id="{{ $field }}" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#1E3A5F] focus:ring-[#1E3A5F]/30">
                        <label for="{{ $field }}" class="text-sm font-semibold text-gray-700">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal',false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">
                        {{ $editingId ? 'Enregistrer' : 'Créer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

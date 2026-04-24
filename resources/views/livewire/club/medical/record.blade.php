<x-slot name="header">
    <a href="{{ route('club.medical.overview') }}" wire:navigate class="text-gray-400 hover:text-gray-600 mr-2">←</a>
    Dossier médical — {{ $player->last_name }} {{ $player->first_name }}
</x-slot>

<div>
    {{-- En-tête joueur --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6 flex items-center gap-5">
        <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
            <span class="text-blue-700 font-bold text-lg">{{ strtoupper(substr($player->first_name,0,1).substr($player->last_name,0,1)) }}</span>
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="text-lg font-bold text-gray-900">{{ $player->first_name }} {{ $player->last_name }}</h2>
            <p class="text-sm text-gray-500">{{ $player->position }} · {{ $player->age() }} ans · N°{{ $player->jersey_number }}</p>
        </div>

        {{-- Aptitude actuelle --}}
        @if($latestClearance)
            @php
                $c = $latestClearance;
                $colors = ['fit' => 'bg-emerald-100 text-emerald-700 border-emerald-200', 'unfit' => 'bg-red-100 text-red-700 border-red-200', 'conditional' => 'bg-amber-100 text-amber-700 border-amber-200'];
            @endphp
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold border {{ $colors[$c->status] }}">
                    {{ $c->statusLabel() }}
                </span>
                <p class="text-xs text-gray-400 mt-1">Depuis le {{ $c->effective_date->isoFormat('D MMM YYYY') }}</p>
            </div>
        @else
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold border bg-gray-100 text-gray-500 border-gray-200">
                    Non évalué
                </span>
            </div>
        @endif

        <button wire:click="openClearanceModal"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Mettre à jour l'aptitude
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ═══════════════ BLESSURES ═══════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Blessures</h3>
                <button wire:click="openInjuryModal()"
                        class="inline-flex items-center gap-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter
                </button>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($injuries as $inj)
                    @php
                        $statusColors = ['active' => 'bg-red-100 text-red-700', 'recovering' => 'bg-amber-100 text-amber-700', 'recovered' => 'bg-emerald-100 text-emerald-700'];
                    @endphp
                    <div class="px-5 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-medium text-sm text-gray-900">{{ $inj->typeLabel() }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$inj->status] }}">
                                        {{ $inj->statusLabel() }}
                                    </span>
                                </div>
                                @if($inj->description)
                                    <p class="text-xs text-gray-500 mt-1">{{ $inj->description }}</p>
                                @endif
                                <div class="flex items-center gap-4 mt-1.5 text-xs text-gray-400 flex-wrap">
                                    <span>Début : {{ $inj->start_date->isoFormat('D MMM YYYY') }}</span>
                                    @if($inj->estimated_return_date)
                                        <span>Retour estimé : {{ $inj->estimated_return_date->isoFormat('D MMM YYYY') }}</span>
                                    @endif
                                    @if($inj->actual_return_date)
                                        <span class="text-emerald-600">Retour réel : {{ $inj->actual_return_date->isoFormat('D MMM YYYY') }}</span>
                                    @endif
                                </div>
                                @if($inj->treatment)
                                    <p class="text-xs text-gray-500 mt-1"><span class="font-medium">Traitement :</span> {{ $inj->treatment }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <button wire:click="openInjuryModal({{ $inj->id }})"
                                        class="text-gray-400 hover:text-blue-600 transition-colors p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="deleteInjury({{ $inj->id }})"
                                        wire:confirm="Supprimer cette blessure ?"
                                        class="text-gray-400 hover:text-red-600 transition-colors p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-gray-400 text-sm">Aucune blessure enregistrée.</div>
                @endforelse
            </div>
        </div>

        {{-- ═══════════════ CERTIFICATS MÉDICAUX ═══════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Certificats médicaux</h3>
                <button wire:click="openCertModal()"
                        class="inline-flex items-center gap-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter
                </button>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($certificates as $cert)
                    @php
                        $es = $cert->expiryStatus();
                        $expiryClasses = ['expired' => 'text-red-500', 'soon' => 'text-amber-500', 'valid' => 'text-emerald-600', 'none' => 'text-gray-400'];
                    @endphp
                    <div class="px-5 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-medium text-sm text-gray-900">{{ $cert->typeLabel() }}</span>
                                    @if($es === 'expired')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Expiré</span>
                                    @elseif($es === 'soon')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Expire bientôt</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4 mt-1 text-xs text-gray-400 flex-wrap">
                                    <span>Émis le {{ $cert->issued_at->isoFormat('D MMM YYYY') }}</span>
                                    @if($cert->expires_at)
                                        <span class="{{ $expiryClasses[$es] }}">Expire le {{ $cert->expires_at->isoFormat('D MMM YYYY') }}</span>
                                    @endif
                                </div>
                                @if($cert->notes)
                                    <p class="text-xs text-gray-500 mt-1">{{ $cert->notes }}</p>
                                @endif
                                @if($cert->file_path)
                                    <a href="{{ Storage::url($cert->file_path) }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline mt-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                        Voir le fichier
                                    </a>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <button wire:click="openCertModal({{ $cert->id }})"
                                        class="text-gray-400 hover:text-blue-600 transition-colors p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="deleteCert({{ $cert->id }})"
                                        wire:confirm="Supprimer ce certificat ?"
                                        class="text-gray-400 hover:text-red-600 transition-colors p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-gray-400 text-sm">Aucun certificat enregistré.</div>
                @endforelse
            </div>
        </div>

        {{-- ═══════════════ HISTORIQUE APTITUDE ═══════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200 lg:col-span-2">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Historique des aptitudes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Motif</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Révision</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Établi par</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($clearanceHistory as $cl)
                            @php
                                $clColors = ['fit' => 'bg-emerald-100 text-emerald-700', 'unfit' => 'bg-red-100 text-red-700', 'conditional' => 'bg-amber-100 text-amber-700'];
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-gray-700">{{ $cl->effective_date->isoFormat('D MMM YYYY') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $clColors[$cl->status] }}">
                                        {{ $cl->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs max-w-xs truncate">{{ $cl->reason ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $cl->review_date?->isoFormat('D MMM YYYY') ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $cl->setBy?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">Aucun historique.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════ MODAL APTITUDE ═══════════════ --}}
    @if($showClearanceModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-data x-cloak>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">Aptitude à jouer</h2>
                <button wire:click="$set('showClearanceModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut d'aptitude <span class="text-red-500">*</span></label>
                    <select wire:model="clearanceStatus" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="fit">Apte</option>
                        <option value="unfit">Inapte</option>
                        <option value="conditional">Sous réserve</option>
                    </select>
                    @error('clearanceStatus') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date effective <span class="text-red-500">*</span></label>
                        <input wire:model="clearanceDate" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('clearanceDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de révision</label>
                        <input wire:model="clearanceReview" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('clearanceReview') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif / Remarques</label>
                    <textarea wire:model="clearanceReason" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                              placeholder="Précisez le motif ou les conditions d'aptitude…"></textarea>
                    @error('clearanceReason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="$set('showClearanceModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Annuler
                </button>
                <button wire:click="saveClearance"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    Enregistrer
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════ MODAL BLESSURE ═══════════════ --}}
    @if($showInjuryModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-data x-cloak>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 sticky top-0 bg-white">
                <h2 class="text-base font-semibold text-gray-900">{{ $editingInjuryId ? 'Modifier la blessure' : 'Nouvelle blessure' }}</h2>
                <button wire:click="$set('showInjuryModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de blessure <span class="text-red-500">*</span></label>
                        <select wire:model="injuryType" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="musculaire">Musculaire</option>
                            <option value="osseuse">Osseuse</option>
                            <option value="ligamentaire">Ligamentaire</option>
                            <option value="articulaire">Articulaire</option>
                            <option value="tendon">Tendon</option>
                            <option value="autre">Autre</option>
                        </select>
                        @error('injuryType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut <span class="text-red-500">*</span></label>
                        <select wire:model="injuryStatus" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="active">Active</option>
                            <option value="recovering">En rééducation</option>
                            <option value="recovered">Guérie</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea wire:model="injuryDescription" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                              placeholder="Description détaillée de la blessure…"></textarea>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de début <span class="text-red-500">*</span></label>
                        <input wire:model="injuryStartDate" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('injuryStartDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Retour estimé</label>
                        <input wire:model="injuryReturnEst" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('injuryReturnEst') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Retour réel</label>
                        <input wire:model="injuryReturnActual" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Traitement / Protocole</label>
                    <textarea wire:model="injuryTreatment" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                              placeholder="Traitement prescrit, protocole de rééducation…"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="$set('showInjuryModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Annuler
                </button>
                <button wire:click="saveInjury"
                        class="px-4 py-2 text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 rounded-lg transition-colors">
                    Enregistrer
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════ MODAL CERTIFICAT ═══════════════ --}}
    @if($showCertModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-data x-cloak>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">{{ $editingCertId ? 'Modifier le certificat' : 'Nouveau certificat' }}</h2>
                <button wire:click="$set('showCertModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de certificat <span class="text-red-500">*</span></label>
                    <select wire:model="certType" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="aptitude">Aptitude</option>
                        <option value="contre-indication">Contre-indication</option>
                        <option value="specialiste">Spécialiste</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'émission <span class="text-red-500">*</span></label>
                        <input wire:model="certIssuedAt" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('certIssuedAt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                        <input wire:model="certExpiresAt" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('certExpiresAt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fichier (PDF, JPG, PNG — max 5Mo)</label>
                    <input wire:model="certFile" type="file" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('certFile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea wire:model="certNotes" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                              placeholder="Observations, recommandations du médecin…"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="$set('showCertModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Annuler
                </button>
                <button wire:click="saveCert"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    <span wire:loading.remove wire:target="certFile">Enregistrer</span>
                    <span wire:loading wire:target="certFile">Chargement…</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

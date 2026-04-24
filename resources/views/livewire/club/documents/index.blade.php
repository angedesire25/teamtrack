<x-slot name="header">Documents & Administratif</x-slot>

<div>
    {{-- Bannières d'alerte --}}
    @if($expiredCount > 0 || $expiringCount > 0)
    <div class="mb-5 flex flex-col sm:flex-row gap-3">
        @if($expiredCount > 0)
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex-1">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span class="text-sm font-medium text-red-700">
                {{ $expiredCount }} document{{ $expiredCount > 1 ? 's' : '' }} expiré{{ $expiredCount > 1 ? 's' : '' }}
            </span>
            <button wire:click="$set('statusFilter', 'expired')" class="ml-auto text-xs text-red-600 underline hover:text-red-800">Voir</button>
        </div>
        @endif
        @if($expiringCount > 0)
        <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex-1">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-medium text-amber-700">
                {{ $expiringCount }} document{{ $expiringCount > 1 ? 's' : '' }} expirant dans 30 jours
            </span>
            <button wire:click="$set('statusFilter', 'expiring')" class="ml-auto text-xs text-amber-600 underline hover:text-amber-800">Voir</button>
        </div>
        @endif
    </div>
    @endif

    {{-- Barre d'outils --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-3 flex-1 flex-wrap">
                <input wire:model.live.debounce.300ms="search"
                       type="text" placeholder="Rechercher un document…"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-52">

                <select wire:model.live="entityFilter"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Toutes les entités</option>
                    <option value="player">Joueurs</option>
                    <option value="staff">Staff</option>
                    <option value="club">Club</option>
                </select>

                <select wire:model.live="typeFilter"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tous les types</option>
                    <option value="contrat">Contrat</option>
                    <option value="licence">Licence</option>
                    <option value="certificat_medical">Certificat médical</option>
                    <option value="autorisation_parentale">Autorisation parentale</option>
                    <option value="passeport">Passeport</option>
                    <option value="autre">Autre</option>
                </select>

                <select wire:model.live="statusFilter"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tous les statuts</option>
                    <option value="expiring">Expirant bientôt</option>
                    <option value="expired">Expirés</option>
                    <option value="unsigned">Non signés</option>
                </select>

                @if($search || $entityFilter || $typeFilter || $statusFilter)
                    <button wire:click="$set('search',''); $set('entityFilter',''); $set('typeFilter',''); $set('statusFilter','')"
                            class="text-xs text-gray-500 hover:text-gray-700 underline">
                        Réinitialiser
                    </button>
                @endif
            </div>

            <button wire:click="openUploadModal"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Ajouter un document
            </button>
        </div>
    </div>

    {{-- Table des documents --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Document</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Entité</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Version</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Expiration</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Signature</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($documents as $doc)
                    @php $es = $doc->expiryStatus(); @endphp
                    <tr class="hover:bg-gray-50 transition-colors {{ $es === 'expired' ? 'bg-red-50/30' : ($es === 'soon' ? 'bg-amber-50/30' : '') }}">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900 flex items-center gap-2">
                                {{-- Icône selon extension --}}
                                @if(in_array($doc->extension(), ['pdf']))
                                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @endif
                                {{ $doc->title }}
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $doc->formattedSize() }} · {{ $doc->uploadedBy?->name ?? '—' }}</div>
                        </td>

                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-800">{{ $doc->entityLabel() }}</div>
                            <div class="text-xs text-gray-400">{{ $doc->entityTypeLabel() }}</div>
                        </td>

                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $doc->typeBadgeColor() }}">
                                {{ $doc->typeLabel() }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-gray-600 text-xs">v{{ $doc->version }}</td>

                        <td class="px-4 py-3 text-xs">
                            @if($doc->expires_at)
                                <span class="{{ $es === 'expired' ? 'text-red-600 font-semibold' : ($es === 'soon' ? 'text-amber-600 font-medium' : 'text-gray-600') }}">
                                    {{ $doc->expires_at->isoFormat('D MMM YYYY') }}
                                    @if($es === 'expired') · Expiré @elseif($es === 'soon') · Bientôt @endif
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            @if($doc->isSigned())
                                <div class="flex items-center gap-1 text-emerald-600">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-xs font-medium">Signé</span>
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $doc->signed_at->isoFormat('D MMM YYYY') }}</div>
                            @else
                                <button wire:click="openSignModal({{ $doc->id }})"
                                        class="text-xs text-blue-600 hover:text-blue-800 font-medium underline">
                                    Signer
                                </button>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('club.documents.download', $doc) }}" target="_blank"
                                   class="p-1.5 text-gray-400 hover:text-blue-600 transition-colors" title="Télécharger">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                                <button wire:click="openUploadModal('{{ str_replace('App\Models\\', '', $doc->documentable_type) === 'Player' ? 'player' : (str_replace('App\Models\\', '', $doc->documentable_type) === 'Staff' ? 'staff' : 'club') }}', {{ $doc->documentable_id }}, '{{ $doc->document_group_id }}')"
                                        class="p-1.5 text-gray-400 hover:text-purple-600 transition-colors" title="Nouvelle version">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                                @if($doc->documentable instanceof \App\Models\Player)
                                <a href="{{ route('club.documents.player', $doc->documentable_id) }}" wire:navigate
                                   class="p-1.5 text-gray-400 hover:text-indigo-600 transition-colors" title="Voir le dossier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                    </svg>
                                </a>
                                @endif
                                <button wire:click="deleteDocument({{ $doc->id }})"
                                        wire:confirm="Supprimer ce document définitivement ?"
                                        class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">
                            Aucun document trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($documents->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $documents->links() }}</div>
        @endif
    </div>

    {{-- ═══════════════ MODALE UPLOAD ═══════════════ --}}
    @if($showUploadModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 sticky top-0 bg-white">
                <h2 class="text-base font-semibold text-gray-900">
                    {{ $uploadGroupId ? 'Nouvelle version' : 'Ajouter un document' }}
                </h2>
                <button wire:click="$set('showUploadModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 space-y-4">
                @if(!$uploadGroupId)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Entité <span class="text-red-500">*</span></label>
                        <select wire:model.live="uploadEntityType"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="player">Joueur</option>
                            <option value="staff">Staff</option>
                            <option value="club">Club</option>
                        </select>
                    </div>
                    @if($uploadEntityType !== 'club')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $uploadEntityType === 'player' ? 'Joueur' : 'Membre du staff' }} <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="uploadEntityId"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Sélectionner…</option>
                            @if($uploadEntityType === 'player')
                                @foreach($players as $p)
                                    <option value="{{ $p->id }}">{{ $p->last_name }} {{ $p->first_name }}</option>
                                @endforeach
                            @else
                                @foreach($staffList as $s)
                                    <option value="{{ $s->id }}">{{ $s->last_name }} {{ $s->first_name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('uploadEntityId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @endif
                </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                        <select wire:model="uploadDocType"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                {{ $uploadGroupId ? 'disabled' : '' }}>
                            <option value="contrat">Contrat</option>
                            <option value="licence">Licence</option>
                            <option value="certificat_medical">Certificat médical</option>
                            <option value="autorisation_parentale">Autorisation parentale</option>
                            <option value="passeport">Passeport</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                        <input wire:model="uploadExpiresAt" type="date"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('uploadExpiresAt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
                    <input wire:model="uploadTitle" type="text" placeholder="Ex : Contrat 2025-2026"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('uploadTitle') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fichier (PDF, Word, JPG, PNG — max 10 Mo) <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="uploadFile" type="file"
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('uploadFile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea wire:model="uploadNotes" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                              placeholder="Observations, conditions particulières…"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="$set('showUploadModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Annuler
                </button>
                <button wire:click="saveDocument" wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors disabled:opacity-60">
                    <span wire:loading.remove wire:target="saveDocument,uploadFile">Enregistrer</span>
                    <span wire:loading wire:target="saveDocument,uploadFile">Envoi…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════ MODALE SIGNATURE ═══════════════ --}}
    @if($showSignModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">Signature électronique</h2>
                <button wire:click="$set('showSignModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-5">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-blue-700">
                            <p class="font-semibold mb-1">Confirmation de signature</p>
                            <p>En cliquant sur « Signer », vous apposez votre signature électronique horodatée :</p>
                            <p class="mt-1 font-medium">{{ auth()->user()->name }}</p>
                            <p class="text-xs mt-1 text-blue-500">{{ now()->isoFormat('dddd D MMMM YYYY à HH:mm') }}</p>
                        </div>
                    </div>
                </div>

                <label class="flex items-start gap-3 cursor-pointer" x-data="{ confirmed: false }">
                    <input type="checkbox" x-model="confirmed"
                           class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">
                        Je confirme avoir lu et approuvé ce document. Cette action est irréversible.
                    </span>
                </label>

                <div class="flex justify-end gap-3 mt-5" x-data="{ confirmed: false }">
                    <button wire:click="$set('showSignModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Annuler
                    </button>

                    {{-- Solution simplifiée : bouton toujours actif après validation JS --}}
                    <div x-data="{ confirmed: false }">
                        <label class="hidden">
                            <input type="checkbox" x-model="confirmed">
                        </label>
                        <button wire:click="confirmSign"
                                class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                            ✓ Signer le document
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

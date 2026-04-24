<x-slot name="header">
    <a href="{{ route('club.documents.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 mr-2">←</a>
    Dossier — {{ $player->last_name }} {{ $player->first_name }}
</x-slot>

<div>
    {{-- En-tête joueur --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-5 flex items-center gap-5 flex-wrap">
        <div class="w-14 h-14 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
            <span class="text-indigo-700 font-bold text-lg">
                {{ strtoupper(substr($player->first_name, 0, 1).substr($player->last_name, 0, 1)) }}
            </span>
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="text-lg font-bold text-gray-900">{{ $player->first_name }} {{ $player->last_name }}</h2>
            <p class="text-sm text-gray-500">{{ $player->position }} · {{ $player->age() }} ans · N°{{ $player->jersey_number }}</p>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0 flex-wrap">
            <a href="{{ route('club.documents.player-zip', $player) }}" target="_blank"
               class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exporter ZIP
            </a>
            <button wire:click="openUploadModal()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Ajouter un document
            </button>
        </div>
    </div>

    {{-- Alertes d'expiration --}}
    @if($expiredDocs->isNotEmpty() || $expiringSoon->isNotEmpty())
    <div class="mb-5 space-y-3">
        @if($expiredDocs->isNotEmpty())
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="text-sm text-red-700">
                <strong>{{ $expiredDocs->count() }} document(s) expiré(s) :</strong>
                {{ $expiredDocs->map(fn($d) => $d->title)->join(', ') }}
            </div>
        </div>
        @endif
        @if($expiringSoon->isNotEmpty())
        <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-amber-700">
                <strong>{{ $expiringSoon->count() }} document(s) expirant bientôt :</strong>
                {{ $expiringSoon->map(fn($d) => $d->title.' ('.$d->expires_at->isoFormat('D MMM').')')->join(', ') }}
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Documents groupés par version --}}
    @if($groups->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
            </svg>
            <p class="text-gray-400 text-sm">Aucun document dans ce dossier.</p>
            <button wire:click="openUploadModal()" class="mt-3 text-sm text-blue-600 underline">Ajouter le premier document</button>
        </div>
    @else
        <div class="space-y-4">
            @foreach($groups as $group)
                @php
                    $latest = $group['latest'];
                    $history = $group['history'];
                    $es = $latest->expiryStatus();
                @endphp
                <div class="bg-white rounded-xl border {{ $es === 'expired' ? 'border-red-200' : ($es === 'soon' ? 'border-amber-200' : 'border-gray-200') }} overflow-hidden"
                     x-data="{ showHistory: false }">

                    {{-- Version courante --}}
                    <div class="p-5">
                        <div class="flex items-start gap-4 flex-wrap">
                            {{-- Badge type --}}
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $latest->typeBadgeColor() }} flex-shrink-0">
                                {{ $latest->typeLabel() }}
                            </span>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-semibold text-gray-900">{{ $latest->title }}</h3>
                                    <span class="text-xs text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">v{{ $latest->version }}</span>

                                    {{-- Statut signature --}}
                                    @if($latest->isSigned())
                                        <span class="inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Signé le {{ $latest->signed_at->isoFormat('D MMM YYYY') }} par {{ $latest->signedBy?->name }}
                                        </span>
                                    @endif

                                    {{-- Expiration --}}
                                    @if($latest->expires_at)
                                        <span class="text-xs {{ $es === 'expired' ? 'text-red-600 font-semibold' : ($es === 'soon' ? 'text-amber-600' : 'text-gray-400') }}">
                                            Exp. {{ $latest->expires_at->isoFormat('D MMM YYYY') }}
                                            @if($es === 'expired') · Expiré @elseif($es === 'soon') · Bientôt @endif
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-400 flex-wrap">
                                    <span>{{ $latest->file_name }}</span>
                                    <span>{{ $latest->formattedSize() }}</span>
                                    <span>Ajouté le {{ $latest->created_at->isoFormat('D MMM YYYY') }}</span>
                                    @if($latest->uploadedBy)
                                        <span>par {{ $latest->uploadedBy->name }}</span>
                                    @endif
                                </div>

                                @if($latest->notes)
                                    <p class="mt-1.5 text-xs text-gray-500 italic">{{ $latest->notes }}</p>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @if(!$latest->isSigned())
                                    <button wire:click="openSignModal({{ $latest->id }})"
                                            class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                        Signer
                                    </button>
                                @endif

                                <button wire:click="openUploadModal('{{ $latest->document_group_id }}')"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors"
                                        title="Nouvelle version">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Nouvelle version
                                </button>

                                <a href="{{ route('club.documents.download', $latest) }}" target="_blank"
                                   class="p-1.5 text-gray-400 hover:text-blue-600 transition-colors" title="Télécharger">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>

                                <button wire:click="deleteDocument({{ $latest->id }})"
                                        wire:confirm="Supprimer cette version ?"
                                        class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Historique des versions (accordéon) --}}
                    @if($history->isNotEmpty())
                    <div class="border-t border-gray-100">
                        <button @click="showHistory = !showHistory"
                                class="w-full flex items-center gap-2 px-5 py-2.5 text-xs text-gray-500 hover:bg-gray-50 transition-colors">
                            <svg class="w-3.5 h-3.5 transition-transform" :class="showHistory ? 'rotate-90' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            {{ $history->count() }} version(s) précédente(s)
                        </button>

                        <div x-show="showHistory" x-cloak class="divide-y divide-gray-50">
                            @foreach($history as $old)
                                <div class="flex items-center gap-4 px-5 py-3 bg-gray-50 opacity-70">
                                    <span class="text-xs text-gray-400 bg-white border border-gray-200 px-1.5 py-0.5 rounded flex-shrink-0">v{{ $old->version }}</span>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs text-gray-600">{{ $old->file_name }} · {{ $old->formattedSize() }}</div>
                                        <div class="text-xs text-gray-400">{{ $old->created_at->isoFormat('D MMM YYYY') }}</div>
                                    </div>
                                    <a href="{{ route('club.documents.download', $old) }}" target="_blank"
                                       class="text-xs text-gray-400 hover:text-blue-600 underline">Télécharger</a>
                                    <button wire:click="deleteDocument({{ $old->id }})"
                                            wire:confirm="Supprimer la v{{ $old->version }} ?"
                                            class="text-gray-300 hover:text-red-500 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- ═══════════════ MODALE UPLOAD ═══════════════ --}}
    @if($showUploadModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">
                    {{ $uploadGroupId ? 'Nouvelle version du document' : 'Ajouter un document' }}
                </h2>
                <button wire:click="$set('showUploadModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 space-y-4">
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
                    <input wire:model="uploadTitle" type="text" placeholder="Ex : Licence FFF 2025-2026"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('uploadTitle') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fichier (PDF, Word, JPG, PNG — max 10 Mo) <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="uploadFile" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('uploadFile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea wire:model="uploadNotes" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                              placeholder="Observations…"></textarea>
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
                    <span wire:loading wire:target="saveDocument,uploadFile">Envoi en cours…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════ MODALE SIGNATURE ═══════════════ --}}
    @if($showSignModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm" x-data="{ confirmed: false }">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">Signature électronique</h2>
                <button wire:click="$set('showSignModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-5 text-sm text-blue-700">
                    <p class="font-semibold mb-1">Confirmation de signature</p>
                    <p class="font-medium mt-2">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-blue-500 mt-0.5">{{ now()->isoFormat('dddd D MMMM YYYY à HH:mm') }}</p>
                </div>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" x-model="confirmed"
                           class="mt-0.5 h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-sm text-gray-700">
                        Je confirme avoir lu et approuvé ce document. Cette signature électronique est juridiquement engageante.
                    </span>
                </label>

                <div class="flex justify-end gap-3 mt-5">
                    <button wire:click="$set('showSignModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Annuler
                    </button>
                    <button wire:click="confirmSign"
                            :disabled="!confirmed"
                            class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                        ✓ Signer le document
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

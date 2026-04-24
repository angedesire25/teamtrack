<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ $transfer->direction === 'outgoing' ? route('club.transfers.outgoing') : route('club.transfers.incoming') }}"
           wire:navigate class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-2xl font-extrabold text-gray-900 truncate">{{ $transfer->playerDisplayName() }}</h1>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                    {{ $transfer->direction === 'outgoing' ? 'bg-orange-50 text-orange-700 border border-orange-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                    {{ $transfer->directionLabel() }}
                </span>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600">{{ $transfer->typeLabel() }}</span>
            </div>
            <p class="text-gray-500 text-sm mt-0.5">Dossier #{{ $transfer->id }} — Créé le {{ $transfer->created_at->format('d/m/Y') }}</p>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('club.transfers.file-pdf', $transfer->id) }}" target="_blank"
               class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Dossier PDF
            </a>
            <button wire:click="openEdit"
                    class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-white rounded-xl"
                    style="background-color: var(--club-primary);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                Modifier
            </button>
        </div>
    </div>

    {{-- Progression de statut --}}
    @php
        $flow = ['listed', 'negotiating', 'offer_received', 'agreed', 'finalized'];
        $flowLabels = ['Sur liste', 'Négociation', 'Offre', 'Accord', 'Finalisé'];
        $currentIdx = array_search($transfer->status, $flow);
    @endphp
    @if($transfer->status !== 'cancelled')
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 mb-6">
        <div class="flex items-center gap-0">
            @foreach($flow as $i => $step)
            <div class="flex-1 flex flex-col items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                    {{ $currentIdx !== false && $i <= $currentIdx ? 'text-white' : 'bg-gray-100 text-gray-400' }}"
                    style="{{ $currentIdx !== false && $i <= $currentIdx ? 'background-color: var(--club-primary);' : '' }}">
                    @if($currentIdx !== false && $i < $currentIdx)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @else
                    {{ $i + 1 }}
                    @endif
                </div>
                <span class="text-xs mt-1.5 font-medium {{ $currentIdx !== false && $i <= $currentIdx ? 'text-gray-900' : 'text-gray-400' }}">{{ $flowLabels[$i] }}</span>
            </div>
            @if($i < count($flow) - 1)
            <div class="flex-1 h-0.5 -mt-5 {{ $currentIdx !== false && $i < $currentIdx ? '' : 'bg-gray-200' }}"
                 style="{{ $currentIdx !== false && $i < $currentIdx ? 'background-color: var(--club-primary);' : '' }}"></div>
            @endif
            @endforeach
        </div>

        {{-- Boutons de progression --}}
        @if($transfer->nextStatuses())
        <div class="flex gap-2 mt-5 justify-center">
            @foreach($transfer->nextStatuses() as $next)
            <button wire:click="advanceStatus('{{ $next }}')"
                    wire:confirm="{{ $next === 'cancelled' ? 'Annuler ce transfert ?' : 'Faire passer le statut à : '.($transfer->statusLabel()) }}"
                    class="px-4 py-2 text-sm font-semibold rounded-xl transition-colors
                        {{ $next === 'cancelled' ? 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-100' : 'text-white' }}"
                    style="{{ $next !== 'cancelled' ? 'background-color: var(--club-primary);' : '' }}">
                @if($next === 'cancelled') Annuler le transfert
                @elseif($next === 'negotiating') → Passer en négociation
                @elseif($next === 'offer_received') → Marquer offre reçue
                @elseif($next === 'agreed') → Accord trouvé
                @elseif($next === 'finalized') ✓ Finaliser le transfert
                @endif
            </button>
            @endforeach
        </div>
        @endif
    </div>
    @else
    <div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4 mb-6">
        <p class="font-bold text-red-700">Transfert annulé</p>
    </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Infos principales --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Fiche joueur --}}
            @if($transfer->player)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h2 class="font-bold text-gray-900 mb-4">Fiche joueur</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Nom</p>
                        <p class="font-semibold text-gray-900 text-sm">{{ $transfer->player->fullName() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Poste</p>
                        <p class="font-semibold text-gray-900 text-sm">{{ $transfer->player->position ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Âge</p>
                        <p class="font-semibold text-gray-900 text-sm">{{ $transfer->player->age() ? $transfer->player->age().' ans' : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Nationalité</p>
                        <p class="font-semibold text-gray-900 text-sm">{{ $transfer->player->nationality ?? '—' }}</p>
                    </div>
                </div>
            </div>
            @elseif($transfer->search_position || $transfer->search_criteria)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h2 class="font-bold text-gray-900 mb-4">Profil recherché</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @if($transfer->search_position)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Poste</p>
                        <p class="font-semibold text-gray-900 text-sm">{{ $transfer->search_position }}</p>
                    </div>
                    @endif
                    @if($transfer->search_age_min || $transfer->search_age_max)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Tranche d'âge</p>
                        <p class="font-semibold text-gray-900 text-sm">{{ $transfer->search_age_min ?? '?' }} – {{ $transfer->search_age_max ?? '?' }} ans</p>
                    </div>
                    @endif
                    @if($transfer->search_budget_max)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Budget max</p>
                        <p class="font-semibold text-emerald-600 text-sm">{{ number_format($transfer->search_budget_max,0,'.',' ') }} F</p>
                    </div>
                    @endif
                </div>
                @if($transfer->search_criteria)
                <div class="mt-3 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-600">{{ $transfer->search_criteria }}</div>
                @endif
            </div>
            @endif

            {{-- Conditions financières --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h2 class="font-bold text-gray-900 mb-4">Conditions du transfert</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Club {{ $transfer->direction === 'outgoing' ? 'acheteur' : 'vendeur' }}</p>
                        <p class="font-semibold text-gray-900 text-sm">{{ $transfer->counterpart_club ?? '—' }}</p>
                    </div>
                    @if($transfer->counterpart_contact)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Contact</p>
                        <p class="font-semibold text-gray-900 text-sm">{{ $transfer->counterpart_contact }}</p>
                    </div>
                    @endif
                    @if($transfer->asking_price)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Prix demandé</p>
                        <p class="font-bold text-gray-900 text-sm">{{ number_format($transfer->asking_price,0,'.',' ') }} F</p>
                    </div>
                    @endif
                    @if($transfer->agreed_fee)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Montant accordé</p>
                        <p class="font-bold text-emerald-600 text-sm">{{ number_format($transfer->agreed_fee,0,'.',' ') }} F</p>
                    </div>
                    @endif
                    @if($transfer->type === 'loan')
                    @if($transfer->loan_duration_months)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Durée du prêt</p>
                        <p class="font-semibold text-gray-900 text-sm">{{ $transfer->loan_duration_months }} mois</p>
                    </div>
                    @endif
                    @if($transfer->loan_start_date)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Période</p>
                        <p class="font-semibold text-gray-900 text-sm">{{ $transfer->loan_start_date->format('d/m/Y') }} → {{ $transfer->loan_end_date?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    @endif
                    @endif
                    @if($transfer->finalized_at)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Finalisé le</p>
                        <p class="font-semibold text-gray-900 text-sm">{{ $transfer->finalized_at->format('d/m/Y') }}</p>
                    </div>
                    @endif
                </div>

                {{-- Clauses --}}
                @if(!empty($transfer->clauses) && collect($transfer->clauses)->filter()->isNotEmpty())
                <div class="mt-4">
                    <p class="text-xs text-gray-400 mb-2">Clauses</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($transfer->clauses as $key => $active)
                        @if($active)
                        <span class="text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-100 px-2.5 py-1 rounded-full">
                            {{ str_replace('_', ' ', ucfirst($key)) }}
                        </span>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif

                @if($transfer->notes)
                <div class="mt-4 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-600">
                    <p class="text-xs text-gray-400 mb-1">Notes</p>
                    {{ $transfer->notes }}
                </div>
                @endif
            </div>
        </div>

        {{-- Timeline négociations --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-gray-900">Négociations</h2>
                    @if(!in_array($transfer->status, ['finalized','cancelled']))
                    <button wire:click="openNeg"
                            class="flex items-center gap-1 text-xs font-bold text-white px-2.5 py-1.5 rounded-lg"
                            style="background-color: var(--club-primary);">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Ajouter
                    </button>
                    @endif
                </div>

                <div class="px-5 py-4">
                    @forelse($transfer->negotiations as $neg)
                    <div class="flex gap-3 mb-4 last:mb-0">
                        <div class="flex flex-col items-center flex-shrink-0">
                            <div class="w-2.5 h-2.5 rounded-full mt-0.5 flex-shrink-0" style="background-color: var(--club-primary);"></div>
                            @if(!$loop->last)
                            <div class="w-px flex-1 mt-1" style="background-color: var(--club-primary); opacity:0.2;"></div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0 pb-4">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-xs text-gray-400">{{ $neg->date->translatedFormat('d F Y') }}</p>
                                    @if($neg->amount_proposed)
                                    <p class="text-xs font-bold text-emerald-600">{{ number_format($neg->amount_proposed,0,'.',' ') }} F proposés</p>
                                    @endif
                                    @if($neg->status_after)
                                    <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">→ {{ \App\Models\Transfer::make(['status'=>$neg->status_after])->statusLabel() }}</span>
                                    @endif
                                </div>
                                <button wire:click="deleteNeg({{ $neg->id }})" wire:confirm="Supprimer cette entrée ?" class="text-gray-300 hover:text-red-400 flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <p class="text-sm text-gray-700 mt-1">{{ $neg->note }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-6">Aucune entrée</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Modifier --}}
    @if($showEditModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg my-auto" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Modifier le dossier</h3>
                <button wire:click="$set('showEditModal',false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="saveEdit" class="px-6 py-5 space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Club {{ $transfer->direction === 'outgoing' ? 'acheteur' : 'vendeur' }}</label>
                        <input wire:model="counterpart_club" type="text" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Contact</label>
                        <input wire:model="counterpart_contact" type="text" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Prix demandé (F)</label>
                        <input wire:model="asking_price" type="number" min="0" step="1000" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Montant accordé (F)</label>
                        <input wire:model="agreed_fee" type="number" min="0" step="1000" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                </div>
                @if($transfer->type === 'loan')
                <div class="grid sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Durée (mois)</label>
                        <input wire:model="loan_duration_months" type="number" min="1" max="60" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Début prêt</label>
                        <input wire:model="loan_start_date" type="date" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Fin prêt</label>
                        <input wire:model="loan_end_date" type="date" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    </div>
                </div>
                @endif

                {{-- Clauses --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Clauses contractuelles</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['option_achat' => "Option d'achat", 'clause_rappel' => 'Clause de rappel', 'pourcentage_revente' => '% à la revente', 'prime_performance' => 'Prime de performance', 'exclusivite' => 'Exclusivité', 'priorite_rachat' => 'Priorité de rachat'] as $key => $label)
                        <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                            <input type="checkbox"
                                   wire:click="toggleClause('{{ $key }}')"
                                   {{ ($clauses[$key] ?? false) ? 'checked' : '' }}
                                   id="clause_{{ $key }}"
                                   class="w-4 h-4 rounded border-gray-300 text-[#1E3A5F] focus:ring-[#1E3A5F]/30">
                            <label for="clause_{{ $key }}" class="text-xs font-semibold text-gray-700 cursor-pointer">{{ $label }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Notes</label>
                    <textarea wire:model="notes" rows="3" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showEditModal',false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal Négociation --}}
    @if($showNegModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Ajouter une entrée de négociation</h3>
                <button wire:click="$set('showNegModal',false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="saveNeg" class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Date *</label>
                    <input wire:model="negDate" type="date" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                    @error('negDate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Note *</label>
                    <textarea wire:model="negNote" rows="3" autofocus
                              placeholder="Ex : Appel téléphonique avec le directeur sportif. Offre de 5M F discutée…"
                              class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"></textarea>
                    @error('negNote') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Montant proposé (F CFA, optionnel)</label>
                    <input wire:model="negAmount" type="number" min="0" step="1000" placeholder="Ex : 25 000 000"
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nouveau statut après cette entrée</label>
                    <select wire:model="negStatus" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F] bg-white">
                        <option value="">Pas de changement</option>
                        <option value="listed">Sur liste</option>
                        <option value="negotiating">En négociation</option>
                        <option value="offer_received">Offre reçue</option>
                        <option value="agreed">Accord trouvé</option>
                        <option value="finalized">Finalisé</option>
                        <option value="cancelled">Annulé</option>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showNegModal',false)" class="flex-1 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl" style="background-color: var(--club-primary);">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

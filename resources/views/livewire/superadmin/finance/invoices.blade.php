{{-- Super Admin — Factures --}}

<div x-data class="space-y-6">

    {{-- ── KPIs ──────────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 xl:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-2xl font-bold text-gray-900">{{ $this->kpis['total'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Total factures</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-2xl font-bold text-gray-500">{{ $this->kpis['draft'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Brouillons</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-2xl font-bold text-blue-600">{{ $this->kpis['sent'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Envoyées</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-2xl font-bold text-emerald-600">{{ $this->kpis['paid'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Payées</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-2xl font-bold text-emerald-700">
                {{ number_format($this->kpis['revenue'] / 100, 0, ',', ' ') }}
                <span class="text-sm font-normal text-gray-400">XOF</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">Chiffre d'affaires</p>
        </div>
    </div>

    {{-- ── Filtres + bouton générer ──────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        <div class="flex gap-3 flex-wrap flex-1">
            <div class="relative w-full sm:w-64">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="N° facture ou club…"
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent">
            </div>

            <div class="flex items-center gap-1.5 flex-wrap">
                @foreach ([
                    ['all',       'Toutes'],
                    ['draft',     'Brouillons'],
                    ['sent',      'Envoyées'],
                    ['paid',      'Payées'],
                    ['cancelled', 'Annulées'],
                ] as [$key, $label])
                    <button wire:click="$set('statusFilter', '{{ $key }}')"
                            @class([
                                'px-3 py-1.5 text-xs font-medium rounded-lg transition-colors',
                                'bg-gray-900 text-white'                        => $statusFilter === $key,
                                'bg-gray-100 text-gray-600 hover:bg-gray-200'  => $statusFilter !== $key,
                            ])>
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <button wire:click="openGenerateModal"
                class="px-4 py-2 text-sm font-medium text-white bg-[#1E3A5F] rounded-lg hover:bg-[#162d4a] transition-colors flex items-center gap-2 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Générer une facture
        </button>
    </div>

    {{-- ── Table factures ───────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if ($this->invoices->isEmpty())
            <div class="text-center py-16">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">Aucune facture trouvée</p>
                <p class="text-xs text-gray-400 mt-1">Générez la première facture pour un club.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs font-medium text-gray-400 uppercase tracking-wide border-b border-gray-100 bg-gray-50/50">
                            <th class="px-4 py-3 text-left">N° Facture</th>
                            <th class="px-4 py-3 text-left">Club</th>
                            <th class="px-4 py-3 text-left">Période</th>
                            <th class="px-4 py-3 text-left">Montant</th>
                            <th class="px-4 py-3 text-left">Statut</th>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($this->invoices as $invoice)
                            <tr class="hover:bg-gray-50/50 transition-colors">

                                {{-- Numéro --}}
                                <td class="px-4 py-3">
                                    <span class="font-mono font-bold text-gray-800 text-xs">{{ $invoice->number }}</span>
                                    @if ($invoice->plan_name)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $invoice->plan_name }}</p>
                                    @endif
                                </td>

                                {{-- Club --}}
                                <td class="px-4 py-3">
                                    <a href="{{ route('superadmin.clubs.show', $invoice->tenant) }}"
                                       class="font-medium text-gray-800 hover:text-[#1E3A5F] transition-colors">
                                        {{ $invoice->tenant->name }}
                                    </a>
                                    <p class="text-xs text-gray-400 truncate max-w-[140px]">{{ $invoice->tenant->email }}</p>
                                </td>

                                {{-- Période --}}
                                <td class="px-4 py-3 text-gray-500 text-xs">
                                    {{ $invoice->period_start->format('d/m/Y') }}<br>
                                    → {{ $invoice->period_end->format('d/m/Y') }}
                                </td>

                                {{-- Montant --}}
                                <td class="px-4 py-3 font-semibold text-gray-800">
                                    {{ number_format($invoice->amount / 100, 0, ',', ' ') }}
                                    <span class="text-xs font-normal text-gray-400">XOF</span>
                                </td>

                                {{-- Statut --}}
                                <td class="px-4 py-3">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $invoice->statusColor() }}">
                                        {{ $invoice->statusLabel() }}
                                    </span>
                                    @if ($invoice->sent_at)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $invoice->sent_at->format('d/m/Y') }}</p>
                                    @endif
                                </td>

                                {{-- Date création --}}
                                <td class="px-4 py-3 text-xs text-gray-400">
                                    {{ $invoice->created_at->format('d/m/Y') }}
                                    @if ($invoice->createdBy)
                                        <p class="text-gray-300">{{ $invoice->createdBy->name }}</p>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1.5">

                                        {{-- Télécharger PDF --}}
                                        <a href="{{ route('superadmin.invoices.download', $invoice) }}"
                                           target="_blank"
                                           title="Télécharger le PDF"
                                           class="p-1.5 rounded-lg text-gray-500 bg-gray-50 hover:bg-gray-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </a>

                                        {{-- Envoyer par email --}}
                                        @if (in_array($invoice->status, ['draft', 'sent']))
                                            <button wire:click="sendInvoice({{ $invoice->id }})"
                                                    wire:confirm="Envoyer la facture {{ $invoice->number }} à {{ $invoice->tenant->email }} ?"
                                                    title="Envoyer par email"
                                                    class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Marquer payée --}}
                                        @if (in_array($invoice->status, ['draft', 'sent']))
                                            <button wire:click="markAsPaid({{ $invoice->id }})"
                                                    wire:confirm="Marquer la facture {{ $invoice->number }} comme payée ?"
                                                    title="Marquer comme payée"
                                                    class="p-1.5 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Annuler --}}
                                        @if (! in_array($invoice->status, ['paid', 'cancelled']))
                                            <button wire:click="cancelInvoice({{ $invoice->id }})"
                                                    wire:confirm="Annuler la facture {{ $invoice->number }} ?"
                                                    title="Annuler"
                                                    class="p-1.5 rounded-lg text-red-500 bg-red-50 hover:bg-red-100 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($this->invoices->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $this->invoices->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL : Générer une facture                                             --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @teleport('body')
    <div x-data x-show="$wire.showGenerateModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div @click.outside="$wire.showGenerateModal = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 space-y-5">

            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#1E3A5F]/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Générer une facture</h3>
                    <p class="text-sm text-gray-400">La facture sera créée en brouillon. Envoyez-la ensuite par email.</p>
                </div>
            </div>

            {{-- Club --}}
            <div>
                <label class="text-xs font-medium text-gray-500">Club <span class="text-red-400">*</span></label>
                <select wire:model.live="tenantId"
                        class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent">
                    <option value="0">— Sélectionner un club —</option>
                    @foreach ($this->tenants as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
                @error('tenantId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Période --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-medium text-gray-500">Début de période <span class="text-red-400">*</span></label>
                    <input wire:model="periodStart" type="date"
                           class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent">
                    @error('periodStart') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500">Fin de période <span class="text-red-400">*</span></label>
                    <input wire:model="periodEnd" type="date"
                           class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent">
                    @error('periodEnd') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Plan + Montant --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-medium text-gray-500">Libellé abonnement</label>
                    <input wire:model="planName" type="text"
                           placeholder="ex : Plan Pro"
                           class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500">Montant (FCFA) <span class="text-red-400">*</span></label>
                    <input wire:model="amount" type="number" min="1"
                           class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent">
                    @error('amount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="text-xs font-medium text-gray-500">Notes <span class="text-gray-400">(optionnel — apparaît sur la facture)</span></label>
                <textarea wire:model="notes" rows="2"
                          placeholder="Informations complémentaires…"
                          class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent resize-none"></textarea>
                @error('notes') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-1 border-t border-gray-100">
                <button wire:click="$set('showGenerateModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Annuler
                </button>
                <button wire:click="generateInvoice" wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-medium text-white bg-[#1E3A5F] rounded-lg hover:bg-[#162d4a] transition-colors">
                    <span wire:loading.remove wire:target="generateInvoice">Générer la facture</span>
                    <span wire:loading wire:target="generateInvoice">Génération…</span>
                </button>
            </div>
        </div>
    </div>
    @endteleport

</div>

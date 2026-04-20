{{--
    Vue : Gestion des paiements — Super Admin
    Liste filtrée, enregistrement manuel et export CSV
--}}

<x-slot:pageTitle>Paiements</x-slot:pageTitle>

<div class="space-y-6">

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    {{-- ============================================================ --}}
    {{-- BARRE D'OUTILS                                               --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex flex-wrap items-center gap-3">

            {{-- Filtre statut --}}
            <select wire:model.live="filterStatus"
                    class="py-2 pl-3 pr-8 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30">
                <option value="">Tous statuts</option>
                <option value="paid">Payé</option>
                <option value="pending">En attente</option>
                <option value="failed">Échoué</option>
            </select>

            {{-- Filtre club --}}
            <select wire:model.live="filterTenant"
                    class="py-2 pl-3 pr-8 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30">
                <option value="">Tous les clubs</option>
                @foreach ($tenants as $tenant)
                    <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                @endforeach
            </select>

            {{-- Période --}}
            <input wire:model.live="filterFrom" type="date"
                   class="py-2 px-3 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30">
            <span class="text-gray-400 text-sm">→</span>
            <input wire:model.live="filterTo" type="date"
                   class="py-2 px-3 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30">

            {{-- Boutons --}}
            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('superadmin.payments.export') }}"
                   class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </a>
                <button wire:click="openModal"
                        class="flex items-center gap-2 px-4 py-2 bg-[#1E3A5F] text-white text-sm font-medium rounded-lg hover:bg-[#162d4a] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter un paiement
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TABLEAU DES PAIEMENTS                                        --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Référence</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Club</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Méthode</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($payments as $payment)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $payment->reference }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $payment->tenant->name ?? '—' }}</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">
                            {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-4 py-4">
                            @php
                                $badge = match($payment->status) {
                                    'paid'    => ['bg-emerald-100 text-emerald-700', 'Payé'],
                                    'pending' => ['bg-amber-100 text-amber-700', 'En attente'],
                                    'failed'  => ['bg-red-100 text-red-700', 'Échoué'],
                                    default   => ['bg-gray-100 text-gray-600', $payment->status],
                                };
                            @endphp
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $badge[0] }}">{{ $badge[1] }}</span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500">{{ $payment->method ?? '—' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-500">
                            {{ $payment->paid_at?->format('d/m/Y') ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-sm text-gray-400">
                            Aucun paiement trouvé avec ces critères.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($payments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL : Enregistrement manuel d'un paiement                  --}}
    {{-- ============================================================ --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="closeModal">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6 space-y-5">

                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900">Enregistrer un paiement</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Club <span class="text-red-500">*</span></label>
                        <select wire:model="newTenantId"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30 @error('newTenantId') border-red-300 @else border-gray-200 @enderror">
                            <option value="">Sélectionner un club…</option>
                            @foreach ($tenants as $tenant)
                                <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                            @endforeach
                        </select>
                        @error('newTenantId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                            <input wire:model="newAmount" type="number" min="1" placeholder="25000"
                                   class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30 @error('newAmount') border-red-300 @else border-gray-200 @enderror">
                            @error('newAmount') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut <span class="text-red-500">*</span></label>
                            <select wire:model="newStatus"
                                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30">
                                <option value="paid">Payé</option>
                                <option value="pending">En attente</option>
                                <option value="failed">Échoué</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Méthode</label>
                            <select wire:model="newMethod"
                                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30">
                                <option value="">—</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="virement">Virement</option>
                                <option value="especes">Espèces</option>
                                <option value="stripe">Stripe</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Date de paiement</label>
                            <input wire:model="newPaidAt" type="date"
                                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Note</label>
                        <textarea wire:model="newNote" rows="2" placeholder="Référence de virement, notes internes…"
                                  class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/30 resize-none"></textarea>
                    </div>

                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <button wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                        Annuler
                    </button>
                    <button wire:click="savePayment"
                            class="px-5 py-2 text-sm font-medium text-white bg-[#1E3A5F] rounded-lg hover:bg-[#162d4a] transition-colors">
                        <span wire:loading.remove wire:target="savePayment">Enregistrer</span>
                        <span wire:loading wire:target="savePayment">Enregistrement…</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>

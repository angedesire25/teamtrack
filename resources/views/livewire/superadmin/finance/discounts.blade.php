{{-- Super Admin — Remises & Coupons --}}

<div x-data class="space-y-6">

    {{-- ── KPIs ──────────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-2xl font-bold text-gray-900">{{ $this->kpis['total'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Coupons créés</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-2xl font-bold text-emerald-600">{{ $this->kpis['active'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Coupons actifs</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-2xl font-bold text-amber-500">{{ $this->kpis['expired'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Expirés</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-2xl font-bold text-indigo-600">{{ $this->kpis['uses'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Utilisations totales</p>
        </div>
    </div>

    {{-- ── Barre filtres + bouton créer ─────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        <div class="flex gap-3 flex-wrap flex-1">
            <div class="relative w-full sm:w-64">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Code ou description…"
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent">
            </div>

            <div class="flex items-center gap-1.5 flex-wrap">
                @foreach ([
                    ['all',      'Tous'],
                    ['active',   'Actifs'],
                    ['inactive', 'Désactivés'],
                    ['expired',  'Expirés'],
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

        <button wire:click="openCreateModal"
                class="px-4 py-2 text-sm font-medium text-white bg-[#1E3A5F] rounded-lg hover:bg-[#162d4a] transition-colors flex items-center gap-2 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau coupon
        </button>
    </div>

    {{-- ── Table coupons ─────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if ($this->coupons->isEmpty())
            <div class="text-center py-16">
                <div class="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">Aucun coupon trouvé</p>
                <p class="text-xs text-gray-400 mt-1">Créez votre premier code promo.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs font-medium text-gray-400 uppercase tracking-wide border-b border-gray-100 bg-gray-50/50">
                            <th class="px-4 py-3 text-left">Code</th>
                            <th class="px-4 py-3 text-left">Réduction</th>
                            <th class="px-4 py-3 text-left">Description</th>
                            <th class="px-4 py-3 text-left">Expiration</th>
                            <th class="px-4 py-3 text-left">Utilisations</th>
                            <th class="px-4 py-3 text-left">Statut</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($this->coupons as $coupon)
                            <tr class="hover:bg-gray-50/50 transition-colors">

                                {{-- Code --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-gray-900 tracking-wider bg-gray-100 px-2 py-0.5 rounded text-xs">
                                            {{ $coupon->code }}
                                        </span>
                                        <button
                                            x-data
                                            @click="navigator.clipboard.writeText('{{ $coupon->code }}').then(() => $dispatch('toast', { message: 'Code copié !', type: 'success' }))"
                                            class="text-gray-300 hover:text-gray-500 transition-colors"
                                            title="Copier">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>

                                {{-- Réduction --}}
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-purple-700">{{ $coupon->formattedValue() }}</span>
                                    <span class="text-xs text-gray-400 ml-1">{{ $coupon->type === 'percentage' ? '%' : 'fixe' }}</span>
                                </td>

                                {{-- Description --}}
                                <td class="px-4 py-3 text-gray-500 max-w-[180px] truncate">
                                    {{ $coupon->description ?? '—' }}
                                </td>

                                {{-- Expiration --}}
                                <td class="px-4 py-3">
                                    @if ($coupon->expires_at)
                                        <span @class([
                                            'text-xs font-medium',
                                            'text-red-600' => $coupon->isExpired(),
                                            'text-gray-600' => ! $coupon->isExpired(),
                                        ])>
                                            {{ $coupon->expires_at->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Illimitée</span>
                                    @endif
                                </td>

                                {{-- Utilisations --}}
                                <td class="px-4 py-3">
                                    <button wire:click="openUsesModal({{ $coupon->id }}, '{{ $coupon->code }}')"
                                            class="flex items-center gap-1.5 text-sm hover:text-[#1E3A5F] transition-colors group">
                                        <span class="font-semibold text-gray-800">{{ $coupon->uses_count }}</span>
                                        <span class="text-gray-400">
                                            / {{ $coupon->max_uses ?? '∞' }}
                                        </span>
                                        @if ($coupon->uses_count > 0)
                                            <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-[#1E3A5F] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        @endif
                                    </button>

                                    {{-- Barre de progression --}}
                                    @if ($coupon->max_uses)
                                        @php $pct = min(100, round($coupon->uses_count / $coupon->max_uses * 100)); @endphp
                                        <div class="mt-1 h-1 bg-gray-100 rounded-full w-20">
                                            <div class="h-full rounded-full {{ $pct >= 100 ? 'bg-red-400' : ($pct >= 75 ? 'bg-amber-400' : 'bg-emerald-400') }}"
                                                 style="width: {{ $pct }}%"></div>
                                        </div>
                                    @endif
                                </td>

                                {{-- Statut --}}
                                <td class="px-4 py-3">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $coupon->statusColor() }}">
                                        {{ $coupon->statusLabel() }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Toggle actif --}}
                                        <button wire:click="toggleActive({{ $coupon->id }})"
                                                title="{{ $coupon->is_active ? 'Désactiver' : 'Activer' }}"
                                                @class([
                                                    'p-1.5 rounded-lg transition-colors',
                                                    'text-emerald-600 bg-emerald-50 hover:bg-emerald-100' => $coupon->is_active,
                                                    'text-gray-400 bg-gray-50 hover:bg-gray-100' => ! $coupon->is_active,
                                                ])>
                                            @if ($coupon->is_active)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            @endif
                                        </button>

                                        {{-- Modifier --}}
                                        <button wire:click="openEditModal({{ $coupon->id }})"
                                                title="Modifier"
                                                class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        {{-- Supprimer --}}
                                        <button wire:click="delete({{ $coupon->id }})"
                                                wire:confirm="Supprimer le coupon {{ $coupon->code }} ? Cette action est irréversible."
                                                title="Supprimer"
                                                class="p-1.5 rounded-lg text-red-500 bg-red-50 hover:bg-red-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL : Créer / Modifier un coupon                                     --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @teleport('body')
    <div x-data x-show="$wire.showFormModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div @click.outside="$wire.showFormModal = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-5">

            <h3 class="text-base font-bold text-gray-900">
                {{ $editingId ? 'Modifier le coupon' : 'Nouveau coupon' }}
            </h3>

            {{-- Code --}}
            <div>
                <label class="text-xs font-medium text-gray-500">Code promo <span class="text-red-400">*</span></label>
                <div class="mt-1 flex gap-2">
                    <input wire:model="code" type="text"
                           placeholder="ex : SUMMER25"
                           class="flex-1 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono uppercase focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent"
                           style="text-transform:uppercase">
                    <button wire:click="generateCode" type="button"
                            class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors whitespace-nowrap">
                        Générer
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">Lettres majuscules, chiffres, tirets uniquement.</p>
                @error('code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Type + Valeur --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-medium text-gray-500">Type <span class="text-red-400">*</span></label>
                    <select wire:model.live="type"
                            class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent">
                        <option value="percentage">Pourcentage (%)</option>
                        <option value="fixed">Montant fixe (XOF)</option>
                    </select>
                    @error('type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500">
                        Valeur <span class="text-red-400">*</span>
                        <span class="text-gray-400">({{ $type === 'percentage' ? 'max 100' : 'XOF' }})</span>
                    </label>
                    <div class="mt-1 relative">
                        <input wire:model="value" type="number"
                               min="1" :max="$wire.type === 'percentage' ? 100 : 99999999"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 pr-10 text-sm focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">
                            {{ $type === 'percentage' ? '%' : 'XOF' }}
                        </span>
                    </div>
                    @error('value') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="text-xs font-medium text-gray-500">Description <span class="text-gray-400">(optionnel)</span></label>
                <input wire:model="description" type="text"
                       placeholder="ex : Promotion lancement 2026…"
                       class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent">
                @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Expiration + Utilisations max --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-medium text-gray-500">Expiration <span class="text-gray-400">(optionnel)</span></label>
                    <input wire:model="expiresAt" type="date"
                           class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent">
                    @error('expiresAt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500">Utilisations max <span class="text-gray-400">(∞ si vide)</span></label>
                    <input wire:model="maxUses" type="number" min="1"
                           placeholder="Illimité"
                           class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent">
                    @error('maxUses') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-1 border-t border-gray-100">
                <button wire:click="$set('showFormModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Annuler
                </button>
                <button wire:click="save" wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-medium text-white bg-[#1E3A5F] rounded-lg hover:bg-[#162d4a] transition-colors">
                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Enregistrer' : 'Créer le coupon' }}
                    </span>
                    <span wire:loading wire:target="save">Enregistrement…</span>
                </button>
            </div>
        </div>
    </div>
    @endteleport

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL : Historique des utilisations                                     --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @teleport('body')
    <div x-data x-show="$wire.showUsesModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div @click.outside="$wire.showUsesModal = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4 max-h-[80vh] flex flex-col">

            <div class="flex items-center justify-between flex-shrink-0">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Utilisations du coupon</h3>
                    <p class="text-sm font-mono font-semibold text-purple-700 mt-0.5">{{ $selectedCouponCode }}</p>
                </div>
                <button wire:click="$set('showUsesModal', false)"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="overflow-y-auto flex-1">
                @if ($this->couponUses->isEmpty())
                    <div class="text-center py-10">
                        <p class="text-sm text-gray-400">Aucune utilisation enregistrée pour ce coupon.</p>
                    </div>
                @else
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-gray-400 uppercase tracking-wide border-b border-gray-100">
                                <th class="pb-2 text-left">Club</th>
                                <th class="pb-2 text-left">Date</th>
                                <th class="pb-2 text-left">Note</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($this->couponUses as $use)
                                <tr>
                                    <td class="py-2.5">
                                        @if ($use->tenant)
                                            <a href="{{ route('superadmin.clubs.show', $use->tenant) }}"
                                               class="font-medium text-gray-800 hover:text-[#1E3A5F] transition-colors">
                                                {{ $use->tenant->name }}
                                            </a>
                                        @else
                                            <span class="text-gray-400 italic">Club supprimé</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 text-gray-500 text-xs">
                                        {{ $use->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-2.5 text-gray-400 text-xs">
                                        {{ $use->note ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="flex justify-end flex-shrink-0 pt-2 border-t border-gray-100">
                <button wire:click="$set('showUsesModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>
    @endteleport

</div>

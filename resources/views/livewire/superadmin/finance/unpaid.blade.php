{{-- Super Admin — Impayés --}}

<div x-data class="space-y-6">

    {{-- ── KPIs ──────────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-2xl font-bold text-gray-900">{{ $this->kpis['total_clubs'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Club(s) en retard</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-2xl font-bold text-red-600">
                {{ number_format($this->kpis['total_amount'] / 100, 0, ',', ' ') }}
                <span class="text-sm font-normal text-gray-400">XOF</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">Montant total dû</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex gap-4">
            <div>
                <p class="text-2xl font-bold text-red-500">{{ $this->kpis['critical_count'] }}</p>
                <p class="text-xs text-gray-400 mt-1">Critiques &gt;15 j</p>
            </div>
            <div class="border-l border-gray-100 pl-4">
                <p class="text-2xl font-bold text-amber-500">{{ $this->kpis['warning_count'] }}</p>
                <p class="text-xs text-gray-400 mt-1">Avertissement 7-15 j</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-2xl font-bold text-indigo-600">{{ $this->kpis['reminders_month'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Relances ce mois</p>
        </div>
    </div>

    {{-- ── Filtres ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        <div class="relative w-full sm:w-72">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search"
                   type="text"
                   placeholder="Rechercher un club…"
                   class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent">
        </div>

        <div class="flex items-center gap-1.5 flex-wrap">
            @foreach ([
                ['all',      'Tous',           'bg-gray-900 text-white', 'bg-gray-100 text-gray-600 hover:bg-gray-200'],
                ['critical', 'Critique (&gt;15 j)',  'bg-red-600 text-white',  'bg-red-50 text-red-600 hover:bg-red-100'],
                ['warning',  'Avertissement',  'bg-amber-500 text-white','bg-amber-50 text-amber-600 hover:bg-amber-100'],
                ['ok',       'Récent (&lt;7 j)',     'bg-emerald-600 text-white','bg-emerald-50 text-emerald-600 hover:bg-emerald-100'],
            ] as [$key, $label, $activeClass, $inactiveClass])
                <button wire:click="$set('urgencyFilter', '{{ $key }}')"
                        @class([
                            'px-3 py-1.5 text-xs font-medium rounded-lg transition-colors',
                            $activeClass   => $urgencyFilter === $key,
                            $inactiveClass => $urgencyFilter !== $key,
                        ])>
                    {!! $label !!}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── Table principale ─────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if ($this->overdueClubs->isEmpty())
            <div class="text-center py-16">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">Aucun impayé trouvé</p>
                <p class="text-xs text-gray-400 mt-1">Tous les paiements sont à jour.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs font-medium text-gray-400 uppercase tracking-wide border-b border-gray-100 bg-gray-50/50">
                            <th class="px-4 py-3 text-left">Club</th>
                            <th class="px-4 py-3 text-left">Montant dû</th>
                            <th class="px-4 py-3 text-left">Retard</th>
                            <th class="px-4 py-3 text-left">Dernière relance</th>
                            <th class="px-4 py-3 text-left">Statut club</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($this->overdueClubs as $row)
                            @php
                                [$urgencyBg, $urgencyText, $urgencyLabel] = match ($row->urgency) {
                                    'critical' => ['bg-red-100',    'text-red-700',    'Critique'],
                                    'warning'  => ['bg-amber-100',  'text-amber-700',  'Avertissement'],
                                    default    => ['bg-emerald-100','text-emerald-700','Récent'],
                                };
                                [$statusBg, $statusLabel] = match ($row->tenant->status) {
                                    'active'    => ['bg-emerald-100 text-emerald-700', 'Actif'],
                                    'suspended' => ['bg-red-100 text-red-700', 'Suspendu'],
                                    'trial'     => ['bg-blue-100 text-blue-700', 'Essai'],
                                    default     => ['bg-gray-100 text-gray-600', $row->tenant->status],
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                {{-- Club --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                             style="background-color: {{ $row->tenant->primary_color ?? '#1E3A5F' }}">
                                            {{ strtoupper(substr($row->tenant->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('superadmin.clubs.show', $row->tenant) }}"
                                               class="font-medium text-gray-900 hover:text-[#1E3A5F] transition-colors">
                                                {{ $row->tenant->name }}
                                            </a>
                                            <p class="text-xs text-gray-400">
                                                {{ $row->tenant->plan?->name ?? 'Sans plan' }} ·
                                                {{ $row->payments->count() }} paiement(s) en attente
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Montant --}}
                                <td class="px-4 py-3 font-semibold text-gray-800">
                                    {{ number_format($row->totalDue / 100, 0, ',', ' ') }}
                                    <span class="text-xs font-normal text-gray-400">XOF</span>
                                </td>

                                {{-- Retard --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $urgencyBg }} {{ $urgencyText }}">
                                            {{ $urgencyLabel }}
                                        </span>
                                        <span class="text-gray-600 text-xs">{{ $row->daysOverdue }} j</span>
                                    </div>
                                </td>

                                {{-- Dernière relance --}}
                                <td class="px-4 py-3">
                                    @if ($row->lastReminder)
                                        <div>
                                            <p class="text-xs text-gray-600">
                                                {{ $row->lastReminder->created_at->format('d/m/Y H:i') }}
                                            </p>
                                            @if ($row->lastReminder->sentBy)
                                                <p class="text-xs text-gray-400">par {{ $row->lastReminder->sentBy->name }}</p>
                                            @endif
                                        </div>
                                        @if ($row->remindersCount > 1)
                                            <button wire:click="openHistoryModal({{ $row->tenant->id }}, '{{ addslashes($row->tenant->name) }}')"
                                                    class="text-xs text-[#1E3A5F] hover:underline mt-0.5">
                                                +{{ $row->remindersCount - 1 }} autre(s)
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400 italic">Aucune relance</span>
                                    @endif
                                </td>

                                {{-- Statut club --}}
                                <td class="px-4 py-3">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $statusBg }}">{{ $statusLabel }}</span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Envoyer relance --}}
                                        <button wire:click="openReminderModal({{ $row->tenant->id }}, '{{ addslashes($row->tenant->name) }}', {{ $row->totalDue }})"
                                                title="Envoyer une relance"
                                                class="p-1.5 rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </button>

                                        {{-- Marquer comme payé --}}
                                        <button wire:click="markAsPaid({{ $row->tenant->id }})"
                                                wire:confirm="Marquer tous les paiements en attente de {{ $row->tenant->name }} comme réglés ?"
                                                title="Marquer comme payé"
                                                class="p-1.5 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>

                                        {{-- Suspendre --}}
                                        @if ($row->tenant->isActive())
                                            <button wire:click="suspendClub({{ $row->tenant->id }})"
                                                    wire:confirm="Suspendre {{ $row->tenant->name }} immédiatement pour impayé ?"
                                                    title="Suspendre"
                                                    class="p-1.5 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
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
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL : Envoyer une relance                                            --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @teleport('body')
    <div x-data x-show="$wire.showReminderModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div @click.outside="$wire.showReminderModal = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">

            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Envoyer une relance</h3>
                    <p class="text-sm text-gray-500 mt-0.5">
                        À : <span class="font-medium text-gray-700">{{ $selectedTenantName }}</span>
                        · <span class="text-red-600 font-semibold">{{ number_format($selectedAmountDue / 100, 0, ',', ' ') }} XOF</span>
                    </p>
                </div>
            </div>

            <div>
                <label class="text-xs font-medium text-gray-500">Message personnalisé <span class="text-gray-400">(optionnel)</span></label>
                <textarea wire:model="reminderNote" rows="4"
                          placeholder="Ajoutez une note personnalisée à inclure dans l'email…"
                          class="mt-1 w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-transparent resize-none"></textarea>
                @error('reminderNote') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <p class="text-xs text-gray-400">
                Un email de relance sera envoyé à l'adresse du club avec le détail des paiements en attente.
            </p>

            <div class="flex justify-end gap-3 pt-1">
                <button wire:click="$set('showReminderModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Annuler
                </button>
                <button wire:click="sendReminder" wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <span wire:loading.remove wire:target="sendReminder">Envoyer la relance</span>
                    <span wire:loading wire:target="sendReminder">Envoi…</span>
                </button>
            </div>
        </div>
    </div>
    @endteleport

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL : Historique des relances                                        --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @teleport('body')
    <div x-data x-show="$wire.showHistoryModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div @click.outside="$wire.showHistoryModal = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4 max-h-[80vh] flex flex-col">

            <div class="flex items-center justify-between flex-shrink-0">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Historique des relances</h3>
                    <p class="text-sm text-gray-500">{{ $historyTenantName }}</p>
                </div>
                <button wire:click="$set('showHistoryModal', false)"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="overflow-y-auto flex-1 space-y-3 pr-1">
                @forelse ($this->reminderHistory as $reminder)
                    <div class="border border-gray-100 rounded-xl p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-red-600">
                                    {{ number_format($reminder->amount_due / 100, 0, ',', ' ') }} XOF
                                </p>
                                @if ($reminder->note)
                                    <p class="text-sm text-gray-600 mt-1">{{ $reminder->note }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-1">
                                    @if ($reminder->sentBy)
                                        Envoyée par {{ $reminder->sentBy->name }}
                                    @else
                                        Envoyée automatiquement
                                    @endif
                                </p>
                            </div>
                            <time class="text-xs text-gray-400 whitespace-nowrap">
                                {{ $reminder->created_at->format('d/m/Y H:i') }}
                            </time>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-8">Aucune relance envoyée.</p>
                @endforelse
            </div>

            <div class="flex justify-end flex-shrink-0 pt-2 border-t border-gray-100">
                <button wire:click="$set('showHistoryModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>
    @endteleport

</div>

<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Transferts</h1>
            <p class="text-gray-500 text-sm mt-0.5">Tableau de bord du mercato</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('club.transfers.outgoing') }}" wire:navigate
               class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-orange-700 bg-orange-50 border border-orange-200 rounded-xl hover:bg-orange-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                Sortants
            </a>
            <a href="{{ route('club.transfers.incoming') }}" wire:navigate
               class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                Entrants
            </a>
            <a href="{{ route('club.transfers.windows') }}" wire:navigate
               class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Fenêtres
            </a>
            <a href="{{ route('club.transfers.register-pdf') }}" target="_blank"
               class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Registre PDF
            </a>
        </div>
    </div>

    {{-- Alerte fenêtre de transfert --}}
    @if($currentWindow)
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-4 mb-6 flex items-center gap-3">
        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <p class="font-bold text-emerald-800 text-sm">Fenêtre de transfert active : {{ $currentWindow->name }}</p>
            <p class="text-emerald-600 text-xs">Du {{ $currentWindow->start_date->format('d/m/Y') }} au {{ $currentWindow->end_date->format('d/m/Y') }}</p>
        </div>
    </div>
    @else
    <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 mb-6 flex items-center gap-3">
        <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <p class="font-bold text-amber-800 text-sm">Aucune fenêtre de transfert active</p>
            <p class="text-amber-600 text-xs">Les transferts peuvent être enregistrés mais vous êtes hors mercato. <a href="{{ route('club.transfers.windows') }}" wire:navigate class="underline font-semibold">Configurer les fenêtres →</a></p>
        </div>
    </div>
    @endif

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php
            $kpis = [
                ['label'=>'Sortants actifs',   'value'=> $stats['outgoing_active'],  'sub'=>'En cours de négociation', 'color'=>'orange', 'icon'=>'M17 8l4 4m0 0l-4 4m4-4H3'],
                ['label'=>'Entrants actifs',   'value'=> $stats['incoming_active'],  'sub'=>'Recrutements en cours',   'color'=>'blue',   'icon'=>'M7 16l-4-4m0 0l4-4m-4 4h18'],
                ['label'=>'Finalisés '.now()->year, 'value'=> $stats['finalized_year'], 'sub'=>'Cette saison',       'color'=>'emerald', 'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label'=>'Fees totaux',       'value'=> number_format($stats['total_fees'],0,'.',' ').' F', 'sub'=>'Transferts finalisés', 'color'=>'violet', 'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-{{ $kpi['color'] }}-50 mb-3">
                <svg class="w-5 h-5 text-{{ $kpi['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['icon'] }}"/>
                </svg>
            </div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $kpi['value'] }}</p>
            <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $kpi['label'] }}</p>
            <p class="text-xs text-gray-400">{{ $kpi['sub'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Sortants actifs --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-900">Transferts sortants</h2>
                <a href="{{ route('club.transfers.outgoing') }}" wire:navigate class="text-xs font-semibold text-[#1E3A5F] hover:underline">Voir tout →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($activeOutgoing as $t)
                <a href="{{ route('club.transfers.show', $t->id) }}" wire:navigate
                   class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $t->playerDisplayName() }}</p>
                        <p class="text-xs text-gray-400">{{ $t->counterpart_club ?? 'Club non précisé' }}</p>
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full
                        {{ $t->status === 'listed' ? 'bg-gray-100 text-gray-600' : '' }}
                        {{ $t->status === 'negotiating' ? 'bg-blue-50 text-blue-700' : '' }}
                        {{ $t->status === 'offer_received' ? 'bg-amber-50 text-amber-700' : '' }}
                        {{ $t->status === 'agreed' ? 'bg-emerald-50 text-emerald-700' : '' }}">
                        {{ $t->statusLabel() }}
                    </span>
                </a>
                @empty
                <div class="px-5 py-10 text-center text-gray-400 text-sm">Aucun transfert sortant en cours</div>
                @endforelse
            </div>
        </div>

        {{-- Entrants actifs --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-900">Transferts entrants</h2>
                <a href="{{ route('club.transfers.incoming') }}" wire:navigate class="text-xs font-semibold text-[#1E3A5F] hover:underline">Voir tout →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($activeIncoming as $t)
                <a href="{{ route('club.transfers.show', $t->id) }}" wire:navigate
                   class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $t->playerDisplayName() }}</p>
                        <p class="text-xs text-gray-400">{{ $t->counterpart_club ?? ($t->search_position ? 'Profil : '.$t->search_position : 'Club non précisé') }}</p>
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full
                        {{ $t->status === 'listed' ? 'bg-gray-100 text-gray-600' : '' }}
                        {{ $t->status === 'negotiating' ? 'bg-blue-50 text-blue-700' : '' }}
                        {{ $t->status === 'offer_received' ? 'bg-amber-50 text-amber-700' : '' }}
                        {{ $t->status === 'agreed' ? 'bg-emerald-50 text-emerald-700' : '' }}">
                        {{ $t->statusLabel() }}
                    </span>
                </a>
                @empty
                <div class="px-5 py-10 text-center text-gray-400 text-sm">Aucun recrutement en cours</div>
                @endforelse
            </div>
        </div>

        {{-- Dernières négociations --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-900">Dernières activités</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentNegotiations as $neg)
                <div class="flex items-start gap-3 px-5 py-3.5">
                    <div class="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0" style="background-color: var(--club-primary);"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold">{{ $neg->transfer->playerDisplayName() }}</span>
                            — {{ $neg->note }}
                        </p>
                        @if($neg->amount_proposed)
                        <p class="text-xs text-emerald-600 font-semibold mt-0.5">{{ number_format($neg->amount_proposed,0,'.',' ') }} F proposés</p>
                        @endif
                    </div>
                    <span class="text-xs text-gray-400 flex-shrink-0">{{ $neg->date->format('d/m/Y') }}</span>
                </div>
                @empty
                <div class="px-5 py-10 text-center text-gray-400 text-sm">Aucune activité récente</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

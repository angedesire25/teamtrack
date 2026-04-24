<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Finances</h1>
            <p class="text-gray-500 text-sm mt-0.5">Tableau de bord financier</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('club.finance.subscriptions') }}" wire:navigate
               class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                Cotisations
            </a>
            <a href="{{ route('club.finance.expenses') }}" wire:navigate
               class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                Dépenses
            </a>
            <div class="flex gap-1">
                <a href="{{ route('club.finance.export-csv', ['year' => $year]) }}"
                   class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    CSV
                </a>
                <a href="{{ route('club.finance.export-pdf', ['year' => $year]) }}" target="_blank"
                   class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    PDF
                </a>
            </div>
            <select wire:model.live="year" class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none bg-white font-semibold text-gray-700">
                @foreach(range(now()->year, now()->year - 4) as $y)
                <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php
            $kpis = [
                ['label'=>'Recettes '.$year,    'value'=> number_format($totalIncome,0,'.',' ').' F',    'sub'=>'Cotisations + Dons',        'color'=>'emerald', 'icon'=>'M7 11l5-5m0 0l5 5m-5-5v12'],
                ['label'=>'Dépenses '.$year,    'value'=> number_format($totalExpenses,0,'.',' ').' F',  'sub'=>'Toutes catégories',         'color'=>'rose',    'icon'=>'M17 13l-5 5m0 0l-5-5m5 5V6'],
                ['label'=>'Solde '.$year,       'value'=> number_format($solde,0,'.',' ').' F',          'sub'=> $solde >= 0 ? 'Excédent' : 'Déficit', 'color'=> $solde >= 0 ? 'blue' : 'red', 'icon'=>'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3'],
                ['label'=>'Retards '.$season,   'value'=> $overdueCount,                                 'sub'=>$recoveryRate.'% recouvrement','color'=>'amber',  'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
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

    <div class="grid lg:grid-cols-3 gap-6 mb-6">
        {{-- Cotisations saison --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900">Cotisations {{ $season }}</h2>
                <a href="{{ route('club.finance.subscriptions') }}" wire:navigate class="text-xs font-semibold text-[#1E3A5F] hover:underline">Gérer →</a>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total dû</span>
                    <span class="font-bold text-gray-900">{{ number_format($totalDue,0,'.',' ') }} F</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Collecté</span>
                    <span class="font-bold text-emerald-600">{{ number_format($totalCollected,0,'.',' ') }} F</span>
                </div>
                @if($totalDue > 0)
                <div class="w-full bg-gray-100 rounded-full h-2.5 mt-2">
                    <div class="h-2.5 rounded-full" style="width:{{ $recoveryRate }}%; background-color: var(--club-primary);"></div>
                </div>
                <p class="text-xs text-gray-400 text-center">{{ $recoveryRate }}% de taux de recouvrement</p>
                @endif
                @if($overdueCount > 0)
                <div class="bg-red-50 border border-red-100 rounded-xl px-3 py-2 text-xs text-red-700 font-semibold">
                    ⚠ {{ $overdueCount }} joueur(s) en retard de paiement
                </div>
                @endif
            </div>
        </div>

        {{-- Répartition dépenses --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900">Dépenses {{ $year }}</h2>
                <a href="{{ route('club.finance.expenses') }}" wire:navigate class="text-xs font-semibold text-[#1E3A5F] hover:underline">Gérer →</a>
            </div>
            @if($byCategory->isNotEmpty())
            <div class="space-y-2.5">
                @php $maxCat = $byCategory->max(); @endphp
                @foreach($byCategory as $catName => $amount)
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-gray-600 truncate">{{ $catName }}</span>
                        <span class="text-gray-900 ml-2 flex-shrink-0">{{ number_format($amount,0,'.',' ') }} F</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full bg-rose-400" style="width:{{ $maxCat > 0 ? round($amount/$maxCat*100) : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400 text-center py-6">Aucune dépense cette année</p>
            @endif
        </div>

        {{-- Derniers paiements --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-900">Derniers paiements</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentPayments as $pay)
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $pay->subscription->player->fullName() }}</p>
                        <p class="text-xs text-gray-400">{{ $pay->payment_date->format('d/m/Y') }} · {{ $pay->methodLabel() }}</p>
                    </div>
                    <p class="font-bold text-emerald-600 text-sm flex-shrink-0">{{ number_format($pay->amount,0,'.',' ') }} F</p>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-8">Aucun paiement</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Graphiques mensuels --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="font-bold text-gray-900 mb-6">Évolution mensuelle {{ $year }}</h2>
        @php
            $maxVal = max(array_merge($monthlyIncome, $monthlyExpenses, [1]));
        @endphp
        <div class="grid grid-cols-12 gap-1 items-end" style="height:160px;">
            @foreach($monthLabels as $i => $label)
            <div class="flex flex-col items-center gap-0.5 group h-full justify-end">
                <div class="w-full flex gap-0.5 items-end justify-center" style="height:140px;">
                    {{-- Barre des recettes --}}
                    <div class="flex-1 rounded-t-sm bg-emerald-400 transition-all relative"
                         style="height:{{ $maxVal > 0 ? max(2, round($monthlyIncome[$i]/$maxVal*100)) : 2 }}%;opacity:{{ $monthlyIncome[$i] > 0 ? 1 : 0.25 }};">
                        @if($monthlyIncome[$i] > 0)
                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none z-10">
                            +{{ number_format($monthlyIncome[$i],0,'.',' ') }} F
                        </div>
                        @endif
                    </div>
                    {{-- Barre des dépenses --}}
                    <div class="flex-1 rounded-t-sm bg-rose-400 transition-all relative"
                         style="height:{{ $maxVal > 0 ? max(2, round($monthlyExpenses[$i]/$maxVal*100)) : 2 }}%;opacity:{{ $monthlyExpenses[$i] > 0 ? 1 : 0.25 }};">
                        @if($monthlyExpenses[$i] > 0)
                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none z-10">
                            -{{ number_format($monthlyExpenses[$i],0,'.',' ') }} F
                        </div>
                        @endif
                    </div>
                </div>
                <span class="text-gray-400" style="font-size:8px;">{{ $label }}</span>
            </div>
            @endforeach
        </div>
        <div class="flex items-center gap-4 mt-3 justify-center text-xs">
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-emerald-400 inline-block"></span> Recettes</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-rose-400 inline-block"></span> Dépenses</span>
        </div>
    </div>
</div>

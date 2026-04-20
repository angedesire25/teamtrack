<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Dons</h1>
            <p class="text-gray-500 text-sm mt-0.5">Tableau de bord des collectes</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('club.donations.campaigns') }}" wire:navigate
               class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Campagnes
            </a>
            <a href="{{ route('club.donations.donors') }}" wire:navigate
               class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Donateurs
            </a>
            <a href="{{ route('club.donations.export-csv') }}"
               class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV
            </a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php
            $kpis = [
                ['label'=>'Total collecté',     'value'=> number_format($totalCollected,0,'.',' ').' F', 'sub'=>'Tous dons complétés', 'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color'=>'emerald'],
                ['label'=>'Donateurs',           'value'=> number_format($totalDonors), 'sub'=>'Profils uniques', 'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color'=>'blue'],
                ['label'=>'Transactions',        'value'=> number_format($totalDonations), 'sub'=>'Dons complétés', 'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color'=>'violet'],
                ['label'=>'Dons récurrents actifs','value'=> number_format($recurringActive), 'sub'=>'Mensuels + annuels', 'icon'=>'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'color'=>'amber'],
            ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-{{ $kpi['color'] }}-50">
                    <svg class="w-5 h-5 text-{{ $kpi['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $kpi['value'] }}</p>
            <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $kpi['label'] }}</p>
            <p class="text-xs text-gray-400">{{ $kpi['sub'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Graphe mensuel --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-4">Évolution mensuelle</h2>
            <div class="relative h-48">
                @php
                    $max = max(array_merge($monthAmounts, [1]));
                @endphp
                <div class="flex items-end gap-1 h-full">
                    @foreach($monthAmounts as $i => $amount)
                    @php $pct = $max > 0 ? ($amount / $max) * 100 : 0; @endphp
                    <div class="flex-1 flex flex-col items-center gap-1 group">
                        <div class="w-full rounded-t-lg transition-all relative" style="height: {{ max(4, $pct) }}%; background-color: var(--club-primary); opacity: {{ $amount > 0 ? 1 : 0.2 }};">
                            @if($amount > 0)
                            <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                {{ number_format($amount,0,'.',' ') }} F
                            </div>
                            @endif
                        </div>
                        <span class="text-xs text-gray-400 writing-mode-vertical" style="font-size:9px;">{{ $monthLabels[$i] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Campagnes actives --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900">Campagnes</h2>
                <a href="{{ route('club.donations.campaigns') }}" wire:navigate class="text-xs font-semibold text-[#1E3A5F] hover:underline">Voir tout</a>
            </div>
            <div class="space-y-4">
                @forelse($campaigns->where('is_active', true)->take(4) as $c)
                <div>
                    <div class="flex justify-between items-baseline mb-1">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $c->title }}</p>
                        <p class="text-xs text-gray-400 ml-2 flex-shrink-0">{{ $c->completed_donations_count }} dons</p>
                    </div>
                    @if($c->goal_amount)
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full" style="width:{{ $c->progressPercent() }}%; background-color: var(--club-primary);"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">{{ number_format($c->collected_amount,0,'.',' ') }} / {{ number_format($c->goal_amount,0,'.',' ') }} F</p>
                    @else
                    <p class="text-xs text-gray-400">{{ number_format($c->collected_amount,0,'.',' ') }} F collectés</p>
                    @endif
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">Aucune campagne active</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Derniers dons --}}
    <div class="mt-6 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Derniers dons</h2>
            <a href="{{ route('club.donations.donors') }}" wire:navigate class="text-xs font-semibold text-[#1E3A5F] hover:underline">Tous les donateurs</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Donateur</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Campagne</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Type</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentDonations as $d)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $d->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $d->donorName() }}</td>
                        <td class="px-4 py-3 hidden md:table-cell text-xs text-gray-500">{{ $d->campaign?->title ?? '—' }}</td>
                        <td class="px-4 py-3 font-bold text-emerald-600">{{ number_format($d->amount,0,'.',' ') }} F</td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            <span class="text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full font-medium">{{ $d->frequencyLabel() }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($d->receipt_number)
                            <a href="{{ route('club.donations.receipt-pdf', $d->id) }}" class="text-xs font-semibold text-gray-400 hover:text-[#1E3A5F]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">Aucun don pour le moment</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Stock & Matériel</h1>
            <p class="text-gray-500 text-sm mt-0.5">Maillots, équipements et fournisseurs du club</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('club.stock.inventory-pdf') }}"
               class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Export PDF
            </a>
            <a href="{{ route('club.stock.inventory-csv') }}"
               class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        @php
            $kpis = [
                ['label'=>'Alertes maillots',    'value'=>$lowJerseys,        'color'=>$lowJerseys>0?'text-red-600':'text-emerald-600',   'bg'=>$lowJerseys>0?'bg-red-50':'bg-emerald-50'],
                ['label'=>'Alertes matériel',    'value'=>$lowEquipment,      'color'=>$lowEquipment>0?'text-red-600':'text-emerald-600', 'bg'=>$lowEquipment>0?'bg-red-50':'bg-emerald-50'],
                ['label'=>'Hors service',        'value'=>$outOfService,      'color'=>$outOfService>0?'text-red-600':'text-gray-700',    'bg'=>'bg-gray-50'],
                ['label'=>'À réparer',           'value'=>$toRepair,          'color'=>$toRepair>0?'text-amber-600':'text-gray-700',      'bg'=>$toRepair>0?'bg-amber-50':'bg-gray-50'],
                ['label'=>'Maillots attribués',  'value'=>$activeAssignments, 'color'=>'text-[#1E3A5F]',                                  'bg'=>'bg-blue-50'],
                ['label'=>'Emprunts en retard',  'value'=>$overdueMovements,  'color'=>$overdueMovements>0?'text-red-600':'text-emerald-600','bg'=>$overdueMovements>0?'bg-red-50':'bg-emerald-50'],
            ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
            <p class="text-2xl font-extrabold {{ $kpi['color'] }}">{{ $kpi['value'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $kpi['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Alertes stock bas --}}
    @if($alertJerseys->isNotEmpty() || $alertEquipment->isNotEmpty())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-5 mb-6">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="flex-1">
                <p class="font-bold text-red-700 mb-2">Articles sous le seuil d'alerte</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($alertJerseys as $j)
                    <span class="text-xs bg-white border border-red-200 text-red-700 px-2.5 py-1 rounded-full font-medium">
                        Maillot {{ $j->name }} {{ $j->size }} — {{ $j->quantity_available }} restant(s)
                    </span>
                    @endforeach
                    @foreach($alertEquipment as $e)
                    <span class="text-xs bg-white border border-red-200 text-red-700 px-2.5 py-1 rounded-full font-medium">
                        {{ $e->name }} — {{ $e->quantity_available }} restant(s)
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Navigation rapide --}}
    <div class="grid sm:grid-cols-3 gap-4">
        <a href="{{ route('club.stock.jerseys') }}" wire:navigate
           class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:border-[#1E3A5F]/30 hover:shadow-md transition-all group">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform"
                 style="background-color: var(--club-primary);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-1">Maillots</h3>
            <p class="text-sm text-gray-500">Catalogue, stock par taille, attributions aux joueurs et historique</p>
            @if($lowJerseys > 0)
            <span class="mt-3 inline-block text-xs bg-red-50 text-red-600 border border-red-100 px-2.5 py-1 rounded-full font-semibold">{{ $lowJerseys }} alerte(s)</span>
            @endif
        </a>

        <a href="{{ route('club.stock.equipment') }}" wire:navigate
           class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:border-[#1E3A5F]/30 hover:shadow-md transition-all group">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform"
                 style="background-color: var(--club-primary);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-1">Matériel</h3>
            <p class="text-sm text-gray-500">Ballons, cônes, chasubles, filets, état et mouvements</p>
            @if($lowEquipment > 0 || $outOfService > 0)
            <span class="mt-3 inline-block text-xs bg-red-50 text-red-600 border border-red-100 px-2.5 py-1 rounded-full font-semibold">{{ $lowEquipment + $outOfService }} alerte(s)</span>
            @endif
        </a>

        <a href="{{ route('club.stock.suppliers') }}" wire:navigate
           class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:border-[#1E3A5F]/30 hover:shadow-md transition-all group">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform"
                 style="background-color: var(--club-primary);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-1">Fournisseurs</h3>
            <p class="text-sm text-gray-500">Répertoire des fournisseurs et bons de commande PDF</p>
        </a>
    </div>
</div>

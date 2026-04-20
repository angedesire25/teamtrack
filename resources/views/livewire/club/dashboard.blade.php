<div>
    {{-- En-tête --}}
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-gray-900">Tableau de bord</h1>
        <p class="text-gray-500 text-sm mt-1">Bienvenue, {{ auth()->user()->name }} · {{ now()->isoFormat('dddd D MMMM YYYY') }}</p>
    </div>

    {{-- Statistiques clés --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['label' => 'Joueurs actifs',  'value' => $stats['players'],    'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'bg-blue-50 text-blue-600'],
            ['label' => 'Catégories',      'value' => $stats['categories'], 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'color' => 'bg-purple-50 text-purple-600'],
            ['label' => 'Équipes',         'value' => $stats['teams'],      'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z', 'color' => 'bg-emerald-50 text-emerald-600'],
            ['label' => 'Personnel',       'value' => $stats['staff'],      'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => 'bg-orange-50 text-orange-600'],
        ] as $stat)
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl {{ $stat['color'] }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $stat['icon'] }}"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">{{ $stat['label'] }}</p>
            </div>
            <p class="text-3xl font-extrabold text-gray-900">{{ $stat['value'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-2 gap-6">

        {{-- Alertes joueurs --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-800 text-sm">Alertes joueurs</h2>
                <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-semibold">{{ $alertPlayers->count() }}</span>
            </div>

            @if($alertPlayers->isEmpty())
                <div class="px-6 py-10 text-center text-gray-400 text-sm">Aucune alerte — tout va bien !</div>
            @else
                <ul class="divide-y divide-gray-50">
                    @foreach($alertPlayers as $p)
                    <li class="flex items-center gap-3 px-6 py-3">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0 text-xs font-bold text-gray-600">
                            {{ strtoupper(substr($p->first_name, 0, 1) . substr($p->last_name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $p->first_name }} {{ $p->last_name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $p->team?->name ?? $p->category?->name ?? '—' }}</p>
                        </div>
                        <div class="flex-shrink-0 flex flex-col items-end gap-1">
                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $p->statusColor() }}">{{ $p->statusLabel() }}</span>
                            @if($p->license_expires_at && $p->license_expires_at->diffInDays(now(), false) <= 30 && $p->status === 'active')
                                <span class="text-xs text-amber-600 font-medium">Licence exp. {{ $p->license_expires_at->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
                <div class="px-6 py-3 border-t border-gray-100">
                    <a href="{{ route('club.players.index') }}" wire:navigate class="text-xs font-semibold text-[#1E3A5F] hover:underline">Voir tous les joueurs →</a>
                </div>
            @endif
        </div>

        {{-- Activité récente --}}
        <div class="space-y-6">

            {{-- Contrats expirant --}}
            @if($expiringContracts->isNotEmpty())
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="font-bold text-amber-700 text-sm">{{ $expiringContracts->count() }} contrat(s) expirant bientôt</p>
                </div>
                @foreach($expiringContracts as $s)
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-sm text-amber-800 font-medium">{{ $s->first_name }} {{ $s->last_name }}</span>
                    <span class="text-xs text-amber-600">{{ $s->contract_end->format('d/m/Y') }}</span>
                </div>
                @endforeach
                <a href="{{ route('club.staff.index') }}" wire:navigate class="text-xs font-semibold text-amber-700 hover:underline mt-2 inline-block">Gérer le personnel →</a>
            </div>
            @endif

            {{-- Derniers joueurs ajoutés --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-800 text-sm">Derniers joueurs ajoutés</h2>
                </div>
                @if($recentPlayers->isEmpty())
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">
                        Aucun joueur pour l'instant.
                        <a href="{{ route('club.players.create') }}" wire:navigate class="block mt-2 font-semibold text-[#1E3A5F] hover:underline">Ajouter le premier joueur →</a>
                    </div>
                @else
                    <ul class="divide-y divide-gray-50">
                        @foreach($recentPlayers as $p)
                        <li class="flex items-center gap-3 px-6 py-3">
                            @if($p->photo)
                                <img src="{{ Storage::url($p->photo) }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0" alt="">
                            @else
                                <div class="w-8 h-8 rounded-full bg-[#1E3A5F]/10 flex items-center justify-center text-xs font-bold text-[#1E3A5F] flex-shrink-0">
                                    {{ strtoupper(substr($p->first_name, 0, 1) . substr($p->last_name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $p->first_name }} {{ $p->last_name }}</p>
                                <p class="text-xs text-gray-400">{{ $p->position ?? '—' }} · {{ $p->team?->name ?? 'Sans équipe' }}</p>
                            </div>
                            <span class="text-xs {{ $p->statusColor() }} px-2 py-0.5 rounded-full font-semibold flex-shrink-0">{{ $p->statusLabel() }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <div class="px-6 py-3 border-t border-gray-100">
                        <a href="{{ route('club.players.index') }}" wire:navigate class="text-xs font-semibold text-[#1E3A5F] hover:underline">Voir tous →</a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

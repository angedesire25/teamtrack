{{--
    Vue : Tableau de bord super administrateur
    Affiche les KPIs globaux, les alertes d'expiration d'essai,
    le graphique d'inscriptions et la liste des derniers clubs.
--}}

<x-slot:pageTitle>Tableau de bord</x-slot:pageTitle>

<div class="space-y-8">

    {{-- ============================================================ --}}
    {{-- ALERTES : trials expirant dans moins de 7 jours              --}}
    {{-- ============================================================ --}}
    @if ($trialExpiringSoon->isNotEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-amber-800">
                        {{ $trialExpiringSoon->count() }} club(s) en période d'essai expirant bientôt
                    </h3>
                    <div class="mt-2 space-y-1">
                        @foreach ($trialExpiringSoon as $tenant)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-amber-700 font-medium">{{ $tenant->name }}</span>
                                <span class="text-amber-600">
                                    Expire {{ $tenant->trial_ends_at->diffForHumans() }}
                                    ({{ $tenant->trial_ends_at->format('d/m/Y') }})
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- CARTES KPI                                                    --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-2 xl:grid-cols-5 gap-5">

        {{-- Clubs actifs --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Clubs actifs</span>
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['actifs'] }}</p>
            <p class="text-xs text-emerald-600 mt-1 font-medium">● Actif</p>
        </div>

        {{-- En trial --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">En essai</span>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['trial'] }}</p>
            <p class="text-xs text-blue-600 mt-1 font-medium">● Période d'essai</p>
        </div>

        {{-- Suspendus --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Suspendus</span>
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['suspendus'] }}</p>
            <p class="text-xs text-red-600 mt-1 font-medium">● Suspendu</p>
        </div>

        {{-- Total joueurs --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Joueurs</span>
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['joueurs'], 0, ',', ' ') }}</p>
            <p class="text-xs text-gray-400 mt-1">Total plateforme</p>
        </div>

        {{-- MRR --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">MRR</span>
                <div class="w-8 h-8 bg-[#1E3A5F]/10 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#1E3A5F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">
                {{ number_format($stats['mrr'], 0, ',', ' ') }}
            </p>
            <p class="text-xs text-gray-400 mt-1">FCFA / mois</p>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- GRAPHIQUE + DERNIERS CLUBS                                    --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-5">

        {{-- Graphique inscriptions (60%) --}}
        <div class="xl:col-span-3 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-1">Évolution des inscriptions</h2>
            <p class="text-xs text-gray-400 mb-5">Nouveaux clubs par mois (6 derniers mois)</p>

            <div class="relative h-64" wire:ignore>
                <canvas id="inscriptionsChart"></canvas>
            </div>
        </div>

        {{-- Derniers clubs inscrits (40%) --}}
        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-1">Derniers clubs inscrits</h2>
            <p class="text-xs text-gray-400 mb-5">8 inscriptions les plus récentes</p>

            <div class="space-y-3">
                @forelse ($recentTenants as $tenant)
                    <div class="flex items-center gap-3">
                        {{-- Avatar couleur primaire du club --}}
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 text-white text-sm font-bold"
                             style="background-color: {{ $tenant->primary_color }}">
                            {{ strtoupper(substr($tenant->name, 0, 1)) }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $tenant->name }}</p>
                            <p class="text-xs text-gray-400">{{ $tenant->plan->name ?? '—' }}</p>
                        </div>

                        {{-- Badge statut --}}
                        @php
                            $badgeClass = match($tenant->status) {
                                'active'    => 'bg-emerald-100 text-emerald-700',
                                'trial'     => 'bg-blue-100 text-blue-700',
                                'suspended' => 'bg-red-100 text-red-700',
                                'cancelled' => 'bg-gray-100 text-gray-600',
                                default     => 'bg-gray-100 text-gray-600',
                            };
                            $badgeLabel = match($tenant->status) {
                                'active'    => 'Actif',
                                'trial'     => 'Essai',
                                'suspended' => 'Suspendu',
                                'cancelled' => 'Annulé',
                                default     => $tenant->status,
                            };
                        @endphp
                        <span class="flex-shrink-0 text-xs font-semibold px-2 py-0.5 rounded-full {{ $badgeClass }}">
                            {{ $badgeLabel }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-8">Aucun club inscrit.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    // Initialisation du graphique d'inscriptions via Chart.js
    (function () {
        const ctx = document.getElementById('inscriptionsChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @js($graphData['labels']),
                datasets: [{
                    label: 'Nouveaux clubs',
                    data: @js($graphData['data']),
                    backgroundColor: '#2E75B6',
                    borderRadius: 6,
                    borderSkipped: false,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.parsed.y + ' club' + (ctx.parsed.y > 1 ? 's' : ''),
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 12 } },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 12 },
                        },
                        grid: { color: '#f3f4f6' },
                    },
                },
            },
        });
    })();
</script>
@endpush

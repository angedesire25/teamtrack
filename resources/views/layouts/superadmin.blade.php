<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Super Admin' }} — {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Chart.js pour les graphiques du dashboard --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-50">

<div class="flex h-screen overflow-hidden">

    {{-- ========================================================= --}}
    {{-- SIDEBAR                                                    --}}
    {{-- ========================================================= --}}
    <aside class="w-64 flex-shrink-0 flex flex-col bg-[#1E3A5F]">

        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-white/10 flex-shrink-0">
            <div class="text-white font-bold text-xl tracking-wide">TeamTrack</div>
            <div class="text-blue-300 text-xs mt-0.5 font-medium">Super Administration</div>
        </div>

        {{-- Navigation principale --}}
        <nav class="flex-1 px-3 py-5 space-y-0.5 overflow-y-auto">

            {{-- Tableau de bord --}}
            <a href="{{ route('superadmin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('superadmin.dashboard')
                          ? 'bg-white/15 text-white'
                          : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z
                             M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z
                             M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z
                             M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Tableau de bord
            </a>

            {{-- Séparateur Gestion --}}
            <div class="pt-5 pb-2 px-3">
                <p class="text-blue-300/50 text-xs uppercase tracking-widest font-semibold">Gestion</p>
            </div>

            {{-- Clubs --}}
            <a href="{{ route('superadmin.clubs.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('superadmin.clubs.*')
                          ? 'bg-white/15 text-white'
                          : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5
                             M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Clubs
                <span class="ml-auto bg-white/20 text-white text-xs rounded-full px-2 py-0.5 font-medium">
                    {{ \App\Models\Tenant::count() }}
                </span>
            </a>

            {{-- Plans --}}
            <a href="{{ route('superadmin.plans.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('superadmin.plans.*')
                          ? 'bg-white/15 text-white'
                          : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                             M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Plans
            </a>

            {{-- Paiements --}}
            <a href="{{ route('superadmin.payments.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('superadmin.payments.*')
                          ? 'bg-white/15 text-white'
                          : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Paiements
            </a>

        </nav>

        {{-- Profil + Déconnexion --}}
        <div class="px-4 py-4 border-t border-white/10 flex-shrink-0">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-[#2E75B6] flex items-center justify-center
                            text-white text-sm font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                    <p class="text-blue-300 text-xs">Super Admin</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 text-blue-300 hover:text-white text-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Se déconnecter
                </button>
            </form>
        </div>

    </aside>

    {{-- ========================================================= --}}
    {{-- ZONE PRINCIPALE                                            --}}
    {{-- ========================================================= --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Barre supérieure --}}
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between flex-shrink-0">
            <h1 class="text-gray-900 font-semibold text-lg">
                {{ $pageTitle ?? 'Tableau de bord' }}
            </h1>
            <span class="text-gray-400 text-sm">
                {{ ucfirst(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')) }}
            </span>
        </header>

        {{-- Contenu de la page --}}
        <main class="flex-1 overflow-y-auto p-8">
            {{ $slot }}
        </main>

    </div>
</div>

@stack('scripts')
</body>
</html>

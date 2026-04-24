<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $tenant = app()->has('tenant') ? app('tenant') : auth()->user()?->tenant;
        $primary = $tenant?->primary_color ?? '#1E3A5F';
    @endphp
    <title>{{ $title ?? 'Dashboard' }} — {{ $tenant?->name ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --club-primary: {{ $primary }}; }
        .sidebar-bg   { background-color: var(--club-primary); }
        .active-link  { background: rgba(255,255,255,.15); }
        .nav-link:hover { background: rgba(255,255,255,.10); }
        .sidebar-nav { scrollbar-width: none; -ms-overflow-style: none; }
        .sidebar-nav::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="h-full font-sans antialiased bg-gray-50 text-gray-900">

{{-- Bannière impersonation --}}
@if(session('impersonating_super_admin_id'))
<div class="bg-[#1E3A5F] text-white text-sm py-2 px-6 flex items-center justify-between">
    <span><strong>Mode impersonation</strong> — vous consultez l'espace de <strong>{{ $tenant?->name }}</strong></span>
    <form method="POST" action="{{ route('impersonate.stop') }}">
        @csrf
        <button class="text-xs underline hover:text-blue-200">Retour au panel super admin</button>
    </form>
</div>
@endif

<div class="flex h-full min-h-screen" x-data="{ sidebarOpen: false }">

    {{-- ══════════════ OVERLAY MOBILE ══════════════ --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-20 lg:hidden"
         x-cloak @click="sidebarOpen = false"></div>

    {{-- ══════════════ SIDEBAR ══════════════ --}}
    <aside class="sidebar-bg fixed inset-y-0 left-0 z-30 w-64 flex flex-col
                  transform transition-transform duration-200
                  -translate-x-full lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           x-cloak>

        {{-- Logo + nom du club --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10 flex-shrink-0">
            @if($tenant?->logo)
                <img src="{{ Storage::url($tenant->logo) }}" alt="Logo" class="w-9 h-9 rounded-lg object-cover">
            @else
                <div class="w-9 h-9 rounded-lg bg-[#F97316] flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="1.8"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" stroke-width="1.8"/>
                        <path d="M2 12h20" stroke-width="1.8"/>
                    </svg>
                </div>
            @endif
            <div class="min-w-0">
                <p class="text-white font-bold text-sm leading-tight truncate">{{ $tenant?->name ?? 'Mon Club' }}</p>
                <p class="text-white/50 text-xs truncate">{{ $tenant?->city }}</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
            @php
                $navItems = [
                    ['route' => 'club.dashboard',    'label' => 'Tableau de bord', 'pattern' => 'dashboard',
                     'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'club.players.index','label' => 'Joueurs',          'pattern' => 'players*',
                     'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['route' => 'club.categories.index','label' => 'Catégories',   'pattern' => 'categories*',
                     'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
                    ['route' => 'club.teams.index',  'label' => 'Équipes',         'pattern' => 'teams*',
                     'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                    ['route' => 'club.staff.index',  'label' => 'Personnel',        'pattern' => 'staff*',
                     'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ['route' => 'club.users.index',  'label' => 'Utilisateurs',    'pattern' => 'users*',
                     'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                    ['route' => 'club.planning.calendar', 'label' => 'Planning', 'pattern' => 'planning*',
                     'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['route' => 'club.stock.overview', 'label' => 'Stock', 'pattern' => 'stock*',
                     'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    ['route' => 'club.donations.dashboard', 'label' => 'Dons', 'pattern' => 'donations*',
                     'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                    ['route' => 'club.transfers.dashboard', 'label' => 'Transferts', 'pattern' => 'transfers*',
                     'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                    ['route' => 'club.finance.dashboard', 'label' => 'Finances', 'pattern' => 'finance*',
                     'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
                    ['route' => 'club.medical.overview', 'label' => 'Médical', 'pattern' => 'medical*',
                     'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                    ['route' => 'club.documents.index', 'label' => 'Documents', 'pattern' => 'documents*',
                     'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z'],
                ];
            @endphp

            @foreach($navItems as $item)
                @php $active = request()->routeIs($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}" wire:navigate
                   class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ $active ? 'active-link text-white' : 'text-white/70 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span>{{ $item['label'] }}</span>
                    @if($active)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-[#F97316]"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Utilisateur connecté + déconnexion --}}
        <div class="border-t border-white/10 px-3 py-4 flex-shrink-0">
            <div class="flex items-center gap-3 px-2 mb-2">
                <div class="w-8 h-8 rounded-full bg-[#F97316] flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-white/45 text-xs truncate">{{ auth()->user()->primaryRoleLabel() }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="nav-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-white/60 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Déconnexion
                </button>
            </form>
        </div>
    </aside>

    {{-- ══════════════ ZONE PRINCIPALE ══════════════ --}}
    <div class="flex-1 flex flex-col lg:ml-64 min-h-screen">

        {{-- Header --}}
        <header class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 sm:px-6 h-16 flex items-center gap-4">
            {{-- Bouton hamburger mobile --}}
            <button class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100"
                    @click="sidebarOpen = true" aria-label="Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Titre de la page --}}
            <div class="flex-1 min-w-0">
                @isset($header)
                    <div class="text-gray-800 font-semibold text-sm sm:text-base truncate">{{ $header }}</div>
                @endisset
            </div>

            {{-- Nom du club (desktop) --}}
            <div class="hidden sm:flex items-center gap-2">
                <span class="text-xs text-gray-400">{{ $tenant?->subdomain }}.teamtrack.test</span>
            </div>

            {{-- Cloche de notifications --}}
            <livewire:club.notification-bell />

            {{-- Avatar --}}
            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                <span class="text-gray-600 text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
            </div>
        </header>

        {{-- Contenu principal --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>

    </div>
</div>

{{-- Toast notification --}}
<div id="toast-container"
     class="fixed bottom-6 right-6 z-50 space-y-2 pointer-events-none"
     x-data="toastManager()"
     @toast.window="addToast($event.detail)">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transform transition ease-out duration-200"
             x-transition:enter-start="translate-y-4 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-sm font-medium max-w-xs"
             :class="toast.type === 'success' ? 'bg-emerald-600 text-white' : (toast.type === 'error' ? 'bg-red-600 text-white' : 'bg-gray-800 text-white')">
            <span x-text="toast.message"></span>
        </div>
    </template>
</div>

<script>
function toastManager() {
    return {
        toasts: [],
        addToast({ message, type = 'success' }) {
            const id = Date.now();
            this.toasts.push({ id, message, type, visible: true });
            setTimeout(() => {
                const t = this.toasts.find(t => t.id === id);
                if (t) t.visible = false;
                setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 200);
            }, 3500);
        }
    };
}
</script>

@livewireScripts
@stack('scripts')
</body>
</html>

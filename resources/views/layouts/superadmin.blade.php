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

    {{-- Chart.js pour les graphiques du tableau de bord --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        .sa-nav { scrollbar-width: none; -ms-overflow-style: none; }
        .sa-nav::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">

<div class="flex h-screen overflow-hidden">

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- SIDEBAR                                                        --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <aside class="w-64 flex-shrink-0 flex flex-col bg-[#1E3A5F]">

        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-white/10 flex-shrink-0">
            <div class="text-white font-bold text-xl tracking-wide">TeamTrack</div>
            <div class="text-blue-300 text-xs mt-0.5 font-medium">Super Administration</div>
        </div>

        {{-- Navigation --}}
        <nav class="sa-nav flex-1 px-3 py-4 overflow-y-auto">
            @php
                $link = fn(string $route, string $pattern) =>
                    'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors '
                    .(request()->routeIs($pattern)
                        ? 'bg-white/15 text-white'
                        : 'text-blue-100 hover:bg-white/10 hover:text-white');

                $sep = fn(string $label) => null; // juste pour la lisibilité ci-dessous
            @endphp

            {{-- Tableau de bord --}}
            <a href="{{ route('superadmin.dashboard') }}" class="{{ $link('superadmin.dashboard','superadmin.dashboard') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Tableau de bord
            </a>

            {{-- ─── GESTION ─────────────────────────────────── --}}
            <div class="pt-5 pb-2 px-3">
                <p class="text-blue-300/50 text-[10px] uppercase tracking-widest font-semibold">Gestion</p>
            </div>

            <a href="{{ route('superadmin.clubs.index') }}" class="{{ $link('','superadmin.clubs.*') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Clubs
                <span class="ml-auto bg-white/20 text-white text-[10px] rounded-full px-1.5 py-0.5 font-semibold leading-none">
                    {{ \App\Models\Tenant::count() }}
                </span>
            </a>

            <a href="{{ route('superadmin.plans.index') }}" class="{{ $link('','superadmin.plans.*') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Plans
            </a>

            <a href="{{ route('superadmin.payments.index') }}" class="{{ $link('','superadmin.payments.*') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Paiements
            </a>

            {{-- ─── FINANCES ────────────────────────────────── --}}
            <div class="pt-5 pb-2 px-3">
                <p class="text-blue-300/50 text-[10px] uppercase tracking-widest font-semibold">Finances</p>
            </div>

            <a href="{{ route('superadmin.finance.unpaid') }}" class="{{ $link('','superadmin.finance.unpaid') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Impayés
            </a>

            <a href="{{ route('superadmin.finance.discounts') }}" class="{{ $link('','superadmin.finance.discounts') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Remises &amp; Coupons
            </a>

            <a href="{{ route('superadmin.finance.invoices') }}" class="{{ $link('','superadmin.finance.invoices') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
                Factures
            </a>

            {{-- ─── COMMUNICATION ───────────────────────────── --}}
            <div class="pt-5 pb-2 px-3">
                <p class="text-blue-300/50 text-[10px] uppercase tracking-widest font-semibold">Communication</p>
            </div>

            <a href="{{ route('superadmin.communication.messaging') }}" class="{{ $link('','superadmin.communication.messaging') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                Messagerie clubs
            </a>

            <a href="{{ route('superadmin.communication.announcements') }}" class="{{ $link('','superadmin.communication.announcements') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                Annonces plateforme
            </a>

            <a href="{{ route('superadmin.communication.templates') }}" class="{{ $link('','superadmin.communication.templates') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Templates e-mails
            </a>

            {{-- ─── ADMINISTRATEURS ─────────────────────────── --}}
            <div class="pt-5 pb-2 px-3">
                <p class="text-blue-300/50 text-[10px] uppercase tracking-widest font-semibold">Administrateurs</p>
            </div>

            <a href="{{ route('superadmin.admins.index') }}" class="{{ $link('','superadmin.admins.index') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Comptes admin
            </a>

            <a href="{{ route('superadmin.admins.audit') }}" class="{{ $link('','superadmin.admins.audit') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Journal d'audit
            </a>

            {{-- ─── PARAMÈTRES ──────────────────────────────── --}}
            <div class="pt-5 pb-2 px-3">
                <p class="text-blue-300/50 text-[10px] uppercase tracking-widest font-semibold">Paramètres</p>
            </div>

            <a href="{{ route('superadmin.settings.company') }}" class="{{ $link('','superadmin.settings.company') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
                Informations société
            </a>

            <a href="{{ route('superadmin.settings.smtp') }}" class="{{ $link('','superadmin.settings.smtp') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                </svg>
                SMTP
            </a>

            <a href="{{ route('superadmin.settings.stripe') }}" class="{{ $link('','superadmin.settings.stripe') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Stripe
            </a>

            <a href="{{ route('superadmin.settings.trials') }}" class="{{ $link('','superadmin.settings.trials') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Trials
            </a>

            <a href="{{ route('superadmin.settings.maintenance') }}" class="{{ $link('','superadmin.settings.maintenance') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Maintenance
            </a>

            <a href="{{ route('superadmin.settings.legal') }}" class="{{ $link('','superadmin.settings.legal') }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                CGU / Mentions légales
            </a>

        </nav>

        {{-- Profil + Déconnexion --}}
        <div class="px-4 py-4 border-t border-white/10 flex-shrink-0">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-[#2E75B6] flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
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

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ZONE PRINCIPALE                                                --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Barre supérieure --}}
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between flex-shrink-0">
            <h1 class="text-gray-900 font-semibold text-lg">
                {{ $pageTitle ?? 'Tableau de bord' }}
            </h1>
            <span class="text-gray-400 text-sm">
                {{ ucfirst(now()->isoFormat('dddd D MMMM YYYY')) }}
            </span>
        </header>

        {{-- Contenu de la page --}}
        <main class="flex-1 overflow-y-auto p-8">
            {{ $slot }}
        </main>

    </div>
</div>

{{-- ── Toast notifications ─────────────────────────────────────────────── --}}
<div class="fixed bottom-6 right-6 z-[9999] space-y-2.5 pointer-events-none"
     x-data="saToastManager()"
     @toast.window="addToast($event.detail)">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transform transition ease-out duration-250"
             x-transition:enter-start="translate-x-4 opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0 translate-x-4"
             class="pointer-events-auto flex items-start gap-3 px-4 py-3.5 rounded-xl shadow-xl text-sm font-medium min-w-[280px] max-w-sm border"
             :class="{
                'bg-emerald-600 text-white border-emerald-500': toast.type === 'success',
                'bg-red-600    text-white border-red-500':     toast.type === 'error',
                'bg-amber-500  text-white border-amber-400':   toast.type === 'warning',
                'bg-[#1E3A5F]  text-white border-[#2E75B6]':   !['success','error','warning'].includes(toast.type),
             }">
            {{-- Icône --}}
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <template x-if="toast.type === 'success'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </template>
                <template x-if="toast.type === 'error'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </template>
                <template x-if="toast.type === 'warning'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </template>
                <template x-if="!['success','error','warning'].includes(toast.type)">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </template>
            </svg>
            {{-- Message --}}
            <span x-text="toast.message" class="leading-snug flex-1"></span>
            {{-- Fermer --}}
            <button @click="dismiss(toast.id)" class="opacity-70 hover:opacity-100 transition-opacity flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>

<script>
function saToastManager() {
    return {
        toasts: [],
        addToast({ message, type = 'info' }) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, message, type, visible: true });
            setTimeout(() => this.dismiss(id), type === 'error' ? 6000 : 3500);
        },
        dismiss(id) {
            const t = this.toasts.find(t => t.id === id);
            if (t) {
                t.visible = false;
                setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 200);
            }
        }
    };
}
</script>

@livewireScripts
@stack('scripts')
</body>
</html>

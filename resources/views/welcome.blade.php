<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TeamTrack — Gérez votre club de football comme un pro</title>
    <meta name="description" content="Plateforme SaaS complète pour les clubs de football africains. Joueurs, matchs, transferts, stock, dons et statistiques.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /*
         * Styles additionnels non couverts par Tailwind
         */

        /* Hover doux sur les cartes */
        .card-lift {
            transition: transform 200ms ease, box-shadow 200ms ease;
        }
        .card-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,.10);
        }

        /* Compteurs : chiffres tabulaires */
        .stat-number {
            font-variant-numeric: tabular-nums;
            font-feature-settings: 'tnum';
        }

        /* Fade-in au scroll */
        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 500ms ease, transform 500ms ease;
        }
        .reveal.in-view {
            opacity: 1;
            transform: translateY(0);
        }

        /* Délais de stagger pour les grilles */
        .reveal-delay-1 { transition-delay:  80ms; }
        .reveal-delay-2 { transition-delay: 160ms; }
        .reveal-delay-3 { transition-delay: 240ms; }
        .reveal-delay-4 { transition-delay: 320ms; }
        .reveal-delay-5 { transition-delay: 400ms; }

    </style>
</head>

<body class="font-sans antialiased bg-[#F8FAFC] text-[#0F172A]">

{{-- ============================================================ --}}
{{-- NAVBAR FIXE                                                  --}}
{{-- ============================================================ --}}
<header class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2.5 flex-shrink-0">
                {{-- Ballon de foot SVG inline --}}
                <div class="w-9 h-9 bg-[#1E3A5F] rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        <path d="M2 12h20"/>
                    </svg>
                </div>
                <span class="text-[#1E3A5F] font-black text-xl tracking-tight leading-none">TeamTrack</span>
            </a>

            {{-- Navigation centrale — desktop uniquement --}}
            <nav class="hidden md:flex items-center gap-7">
                <a href="#fonctionnalites"
                   class="text-sm font-semibold text-gray-500 hover:text-[#F97316] transition-colors">
                    Fonctionnalités
                </a>
                <a href="#tarifs"
                   class="text-sm font-semibold text-gray-500 hover:text-[#F97316] transition-colors">
                    Tarifs
                </a>
                <a href="#temoignages"
                   class="text-sm font-semibold text-gray-500 hover:text-[#F97316] transition-colors">
                    Témoignages
                </a>
                <a href="#contact"
                   class="text-sm font-semibold text-gray-500 hover:text-[#F97316] transition-colors">
                    Contact
                </a>
            </nav>

            {{-- CTAs desktop --}}
            <div class="hidden md:flex items-center gap-3">
                @auth
                    @if(auth()->user()->is_super_admin)
                        <a href="{{ route('superadmin.dashboard') }}"
                           class="text-sm font-bold px-4 py-2 rounded-full bg-[#F97316] text-white hover:bg-orange-600 transition-colors">
                            Panel Admin
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}"
                           class="text-sm font-bold px-4 py-2 rounded-full bg-[#F97316] text-white hover:bg-orange-600 transition-colors">
                            Mon espace
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm font-semibold px-4 py-2 rounded-full border border-[#1E3A5F] text-[#1E3A5F] hover:bg-[#1E3A5F] hover:text-white transition-colors">
                        Se connecter
                    </a>
                    <a href="{{ route('login') }}"
                       class="text-sm font-bold px-5 py-2 rounded-full bg-[#F97316] text-white hover:bg-orange-600 transition-colors shadow-md shadow-orange-200">
                        Essai gratuit
                    </a>
                @endauth
            </div>

            {{-- Bouton hamburger — mobile uniquement --}}
            <button id="hamburger-btn"
                    class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors"
                    aria-label="Menu">
                {{-- Icône ouverte --}}
                <svg id="icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                {{-- Icône fermée --}}
                <svg id="icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

        </div>

        {{-- Menu mobile (caché par défaut) --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 py-4 space-y-1">
            <a href="#fonctionnalites"
               class="mobile-nav-link block px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-[#F97316] hover:bg-orange-50 rounded-lg transition-colors">
                Fonctionnalités
            </a>
            <a href="#tarifs"
               class="mobile-nav-link block px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-[#F97316] hover:bg-orange-50 rounded-lg transition-colors">
                Tarifs
            </a>
            <a href="#temoignages"
               class="mobile-nav-link block px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-[#F97316] hover:bg-orange-50 rounded-lg transition-colors">
                Témoignages
            </a>
            <a href="#contact"
               class="mobile-nav-link block px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-[#F97316] hover:bg-orange-50 rounded-lg transition-colors">
                Contact
            </a>
            <div class="flex flex-col gap-2 px-4 pt-3 border-t border-gray-100">
                @auth
                    <a href="{{ auth()->user()->is_super_admin ? route('superadmin.dashboard') : route('dashboard') }}"
                       class="text-center text-sm font-bold px-4 py-3 rounded-xl bg-[#F97316] text-white">
                        Mon espace
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-center text-sm font-semibold px-4 py-3 rounded-xl border border-[#1E3A5F] text-[#1E3A5F]">
                        Se connecter
                    </a>
                    <a href="{{ route('login') }}"
                       class="text-center text-sm font-bold px-4 py-3 rounded-xl bg-[#F97316] text-white">
                        Essai gratuit 30 jours
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>


{{-- ============================================================ --}}
{{-- HERO SECTION — style stade sombre + mockup app             --}}
{{-- ============================================================ --}}
<section class="relative min-h-screen flex items-center overflow-hidden"
         style="background: linear-gradient(135deg, #071220 0%, #0c1e35 40%, #0d2a1a 100%);">

    {{-- Lignes de terrain en filigrane --}}
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <svg class="absolute inset-0 w-full h-full opacity-[0.045]"
             viewBox="0 0 1440 800" preserveAspectRatio="xMidYMid slice"
             xmlns="http://www.w3.org/2000/svg">
            <circle cx="720" cy="400" r="180" fill="none" stroke="white" stroke-width="1.5"/>
            <line x1="0"    y1="400" x2="1440" y2="400" stroke="white" stroke-width="1.2"/>
            <rect  x="0"    y="170" width="150" height="460" fill="none" stroke="white" stroke-width="1.2"/>
            <rect  x="1290" y="170" width="150" height="460" fill="none" stroke="white" stroke-width="1.2"/>
            <rect  x="0"    y="290" width="55"  height="220" fill="none" stroke="white" stroke-width="1.2"/>
            <rect  x="1385" y="290" width="55"  height="220" fill="none" stroke="white" stroke-width="1.2"/>
            <line x1="720" y1="0"  x2="720"  y2="800" stroke="white" stroke-width="1.2"/>
        </svg>
    </div>

    {{-- Halo orange gauche --}}
    <div class="absolute -left-40 top-1/2 -translate-y-1/2 w-[480px] h-[480px] rounded-full pointer-events-none"
         style="background: radial-gradient(circle, rgba(249,115,22,0.14) 0%, transparent 65%); filter: blur(72px);"
         aria-hidden="true"></div>

    {{-- Halo bleu droit --}}
    <div class="absolute -right-40 top-1/3 w-[500px] h-[500px] rounded-full pointer-events-none"
         style="background: radial-gradient(circle, rgba(30,58,95,0.5) 0%, transparent 65%); filter: blur(80px);"
         aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-24 w-full">
        <div class="grid lg:grid-cols-2 gap-10 xl:gap-16 items-center">

            {{-- ---- Contenu textuel ---- --}}
            <div class="space-y-7">

                {{-- H1 --}}
                <h1 class="text-5xl md:text-6xl xl:text-[4.25rem] font-extrabold text-white leading-[1.06] tracking-tight">
                    Le logiciel N°1<br>
                    pour gérer votre<br>
                    <span class="text-[#F97316]">club de football</span>
                </h1>

                {{-- Sous-titre --}}
                <p class="text-lg md:text-xl text-white/60 max-w-lg leading-relaxed">
                    TeamTrack est la plateforme de référence pour les dirigeants
                    de clubs africains — joueurs, matchs, finances et bien plus.
                </p>

                {{-- CTAs --}}
                <div class="flex flex-wrap items-center gap-4 pt-1">
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-8 py-4 bg-[#F97316] text-white font-bold rounded-lg text-base shadow-lg shadow-orange-500/30 hover:bg-orange-600 hover:shadow-xl transition-all">
                        Demander une démo
                    </a>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-8 py-4 bg-transparent text-white font-bold rounded-lg text-base border-2 border-white/35 hover:border-white hover:bg-white/10 transition-all">
                        Inscrire mon club
                    </a>
                </div>

            </div>

            {{-- ---- Mockup App avec bulles d'annotation ---- --}}
            <div class="relative hidden lg:block mt-6">

                {{-- ▲ Bulle supérieure --}}
                <div class="absolute -top-10 right-6 z-20 w-52">
                    <div class="bg-[#0f2540]/90 backdrop-blur text-white text-sm font-medium px-4 py-3 rounded-2xl shadow-2xl border border-white/10 leading-snug">
                        Je vois les événements de chaque équipe.
                    </div>
                    <div class="ml-10 w-0 h-0 border-l-8 border-r-8 border-t-8 border-l-transparent border-r-transparent border-t-[#0f2540]/90"></div>
                </div>

                {{-- Fenêtre app --}}
                <div class="rounded-2xl overflow-hidden shadow-[0_30px_80px_rgba(0,0,0,.55)] border border-white/10">

                    {{-- Barre navigateur --}}
                    <div class="flex items-center gap-2 px-4 py-2.5 bg-[#1a2e4a] border-b border-white/10">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-red-400/80"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-400/80"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-400/80"></div>
                        </div>
                        <div class="flex-1 mx-4 bg-[#243a55] rounded h-5 flex items-center px-3">
                            <span class="text-white/30 text-xs">asecmimosas.teamtrack.test</span>
                        </div>
                    </div>

                    {{-- Contenu app --}}
                    <div class="flex">

                        {{-- Sidebar icônes --}}
                        <div class="w-14 bg-[#1E3A5F] flex flex-col items-center pt-4 pb-6 gap-5 flex-shrink-0">
                            <div class="w-8 h-8 bg-[#F97316] rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </div>
                            @foreach([
                                'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                                'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                                'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                                'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4',
                            ] as $ico)
                                <svg class="w-5 h-5 text-white/35" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $ico }}"/>
                                </svg>
                            @endforeach
                        </div>

                        {{-- Zone principale --}}
                        <div class="flex-1 bg-[#F8FAFC] p-5">

                            {{-- En-tête --}}
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">78 membres</p>
                                    <p class="text-gray-400 text-xs">ASEC Mimosas · Saison 2025-26</p>
                                </div>
                                <div class="flex gap-2 items-center">
                                    <span class="text-xs bg-[#F97316] text-white px-3 py-1 rounded-full font-semibold cursor-pointer">+ Ajouter</span>
                                    <span class="text-xs bg-white border border-gray-200 text-gray-500 px-3 py-1 rounded-full">Filtrer</span>
                                    <span class="text-xs bg-white border border-gray-200 text-gray-500 px-3 py-1 rounded-full">Trier</span>
                                </div>
                            </div>

                            {{-- Cards joueurs (grille 4 colonnes) --}}
                            <div class="grid grid-cols-4 gap-2.5 mb-3">
                                @foreach([
                                    ['KK', 'Koné Karim',   'Attaquant',  'bg-emerald-500', 'bg-emerald-100 text-emerald-700', 'Actif'],
                                    ['OB', 'Oumar Bah',    'Milieu',     'bg-blue-500',    'bg-blue-100 text-blue-700',       'Actif'],
                                    ['DS', 'Diallo Sékou', 'Gardien',    'bg-purple-500',  'bg-purple-100 text-purple-700',   'Actif'],
                                    ['AK', 'Adama Koné',   'Défenseur',  'bg-rose-500',    'bg-orange-100 text-orange-600',   'Blessé'],
                                ] as [$i, $name, $pos, $av, $badge, $status])
                                    <div class="bg-white rounded-xl p-3 border border-gray-100 text-center shadow-sm">
                                        <div class="w-10 h-10 rounded-full {{ $av }} text-white flex items-center justify-center text-sm font-bold mx-auto mb-2">{{ $i }}</div>
                                        <p class="text-xs font-semibold text-gray-700 leading-tight truncate">{{ $name }}</p>
                                        <p class="text-xs text-gray-400 mb-1.5">{{ $pos }}</p>
                                        <span class="text-xs {{ $badge }} px-2 py-0.5 rounded-full font-medium">{{ $status }}</span>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Liste bas --}}
                            <div class="space-y-1.5">
                                @foreach([
                                    ['MB', 'bg-slate-300',  'Mamadou Baldé',   'Défenseur', 'text-emerald-600', 'Actif'],
                                    ['SC', 'bg-amber-300',  'Saliou Camara',   'Attaquant', 'text-orange-500',  'Suspendu'],
                                    ['MG', 'bg-rose-300',   'Marie Georgette', 'Staff',     'text-emerald-600', 'Actif'],
                                ] as [$i, $col, $name, $pos, $sc, $status])
                                    <div class="flex items-center gap-3 bg-white rounded-lg px-3 py-2 border border-gray-100">
                                        <div class="w-7 h-7 rounded-full {{ $col }} flex items-center justify-center text-xs font-bold text-gray-700 flex-shrink-0">{{ $i }}</div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-gray-700 leading-none">{{ $name }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $pos }}</p>
                                        </div>
                                        <span class="text-xs {{ $sc }} font-semibold whitespace-nowrap">{{ $status }}</span>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ▼ Bulle inférieure --}}
                <div class="absolute -bottom-10 right-10 z-20 w-56">
                    <div class="ml-10 w-0 h-0 border-l-8 border-r-8 border-b-8 border-l-transparent border-r-transparent border-b-[#0f2540]/90"></div>
                    <div class="bg-[#0f2540]/90 backdrop-blur text-white text-sm font-medium px-4 py-3 rounded-2xl shadow-2xl border border-white/10 leading-snug">
                        Je peux envoyer un message à tout le club !
                    </div>
                </div>

                {{-- Connecteur pointillé décoratif --}}
                <div class="absolute -right-5 bottom-16 z-10">
                    <div class="w-14 h-14 rounded-full border-2 border-dashed border-[#F97316]/50 flex items-center justify-center">
                        <div class="w-3 h-3 bg-[#F97316] rounded-full"></div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Social proof --}}
        <div class="mt-16 pt-10 border-t border-white/10 text-center">
            <p class="text-xs font-black uppercase tracking-widest text-white/30 mb-4">ILS NOUS FONT CONFIANCE</p>
            <div class="flex flex-wrap justify-center gap-2">
                @foreach(['ASEC Mimosas','Horoya AC','Djoliba AC','AS Vita Club','Stade Malien'] as $club)
                    <span class="text-sm font-semibold text-white/70 bg-white/8 border border-white/15 px-4 py-1.5 rounded-full">
                        {{ $club }}
                    </span>
                @endforeach
            </div>
        </div>

    </div>
</section>


{{-- ============================================================ --}}
{{-- CHIFFRES CLÉS                                                --}}
{{-- ============================================================ --}}
<section class="bg-[#1E3A5F] py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">

            @foreach([
                ['250',   '+', 'Clubs actifs',        '250'],
                ['18000', '+', 'Joueurs gérés',        '18000'],
                ['3200',  '+', 'Matchs planifiés',     '3200'],
                ['98',    '%', 'Satisfaction client',  '98'],
            ] as [$display, $suffix, $label, $target])
                <div class="reveal">
                    <p class="stat-number text-4xl xl:text-5xl font-black text-white"
                       data-counter="{{ $target }}"
                       data-suffix="{{ $suffix }}">
                        0<span class="text-[#F97316]">{{ $suffix }}</span>
                    </p>
                    <p class="text-blue-300 text-xs font-bold uppercase tracking-widest mt-2">{{ $label }}</p>
                </div>
            @endforeach

        </div>
    </div>
</section>


{{-- ============================================================ --}}
{{-- FONCTIONNALITÉS                                              --}}
{{-- ============================================================ --}}
<section id="fonctionnalites" class="py-24 bg-[#F8FAFC]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- En-tête de section --}}
        <div class="text-center max-w-2xl mx-auto mb-16 reveal">
            <p class="text-[#F97316] text-xs font-black uppercase tracking-widest mb-3">Fonctionnalités</p>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#1E3A5F] leading-tight mb-4">
                Tout ce dont votre club a besoin
            </h2>
            <p class="text-gray-500 text-lg">
                Une plateforme complète qui centralise chaque aspect de la gestion de votre club dans un seul espace moderne.
            </p>
        </div>

        {{-- Grille 3×2 --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

            @php
            $features = [
                [
                    'color' => '#3b82f6',
                    'bg'    => '#eff6ff',
                    'path'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                    'title' => 'Gestion des joueurs & inscriptions',
                    'desc'  => 'Fiches joueurs complètes, documents administratifs, suivi médical et renouvellement de licences en quelques clics.',
                ],
                [
                    'color' => '#F97316',
                    'bg'    => '#fff7ed',
                    'path'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    'title' => 'Planning matchs & entraînements',
                    'desc'  => 'Calendriers interactifs, convocations automatiques, gestion des terrains et notifications pour tout votre staff.',
                ],
                [
                    'color' => '#8b5cf6',
                    'bg'    => '#f5f3ff',
                    'path'  => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
                    'title' => 'Gestion du stock & maillots',
                    'desc'  => 'Inventaire des équipements, attribution des maillots par joueur, suivi des tailles et commandes groupées.',
                ],
                [
                    'color' => '#10b981',
                    'bg'    => '#ecfdf5',
                    'path'  => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                    'title' => 'Transferts de joueurs',
                    'desc'  => 'Arrivées, départs, prêts et indemnités de transfert. Génération automatique des documents officiels.',
                ],
                [
                    'color' => '#ec4899',
                    'bg'    => '#fdf2f8',
                    'path'  => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                    'title' => 'Collecte de dons en ligne',
                    'desc'  => 'Campagnes de financement, abonnements supporters, paiement Mobile Money et suivi complet des donateurs.',
                ],
                [
                    'color' => '#1E3A5F',
                    'bg'    => '#f0f4f9',
                    'path'  => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'title' => 'Rapports & statistiques',
                    'desc'  => 'Tableaux de bord visuels, performances individuelles et collectives, export PDF/CSV prêts à partager.',
                ],
            ];
            @endphp

            @foreach($features as $i => $f)
                <div class="card-lift bg-white rounded-2xl p-7 border border-gray-100 shadow-sm reveal reveal-delay-{{ min($i, 5) }}">

                    {{-- Icône --}}
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-5 flex-shrink-0"
                         style="background-color: {{ $f['bg'] }}">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                             stroke="{{ $f['color'] }}" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="{{ $f['path'] }}"/>
                        </svg>
                    </div>

                    <h3 class="text-base font-bold text-[#0F172A] mb-2 leading-snug">{{ $f['title'] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $f['desc'] }}</p>

                </div>
            @endforeach

        </div>
    </div>
</section>


{{-- ============================================================ --}}
{{-- TARIFS                                                       --}}
{{-- ============================================================ --}}
<section id="tarifs" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center max-w-2xl mx-auto mb-16 reveal">
            <p class="text-[#F97316] text-xs font-black uppercase tracking-widest mb-3">Tarifs</p>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#1E3A5F] leading-tight mb-4">
                Des prix faits pour l'Afrique
            </h2>
            <p class="text-gray-500">
                30 jours d'essai gratuit, sans carte bancaire. Paiement Mobile Money accepté.
            </p>
        </div>

        {{-- 3 cartes tarifaires --}}
        <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto items-start">

            {{-- Starter --}}
            <div class="card-lift bg-[#F8FAFC] border border-gray-200 rounded-2xl p-8 reveal">
                <p class="text-gray-400 text-xs font-black uppercase tracking-widest mb-3">Starter</p>
                <div class="flex items-baseline gap-1.5 mb-1">
                    <span class="text-4xl font-black text-[#1E3A5F]">30 000</span>
                    <span class="text-gray-400 text-sm font-medium">FCFA / mois</span>
                </div>
                <p class="text-gray-500 text-sm mb-7">Idéal pour les petits clubs qui démarrent.</p>

                <ul class="space-y-3 mb-8">
                    @foreach(['50 joueurs', '3 utilisateurs', 'Planning de base', 'Gestion du stock', 'Support par email'] as $item)
                        <li class="flex items-center gap-2.5 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('login') }}"
                   class="block text-center py-3 rounded-xl text-sm font-bold border-2 border-[#1E3A5F] text-[#1E3A5F] hover:bg-[#1E3A5F] hover:text-white transition-colors">
                    Commencer l'essai
                </a>
            </div>

            {{-- Pro — carte mise en avant --}}
            <div class="card-lift bg-[#1E3A5F] rounded-2xl p-8 shadow-2xl shadow-[#1E3A5F]/25 relative reveal reveal-delay-1"
                 style="transform: scale(1.04);">

                {{-- Badge Populaire --}}
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 whitespace-nowrap">
                    <span class="bg-[#F97316] text-white text-xs font-black px-4 py-1.5 rounded-full shadow-lg shadow-orange-400/40">
                        ⚡ Populaire
                    </span>
                </div>

                <p class="text-blue-300 text-xs font-black uppercase tracking-widest mb-3">Pro</p>
                <div class="flex items-baseline gap-1.5 mb-1">
                    <span class="text-4xl font-black text-white">75 000</span>
                    <span class="text-blue-300 text-sm font-medium">FCFA / mois</span>
                </div>
                <p class="text-blue-200 text-sm mb-7">Pour les clubs structurés avec plusieurs équipes.</p>

                <ul class="space-y-3 mb-8">
                    @foreach(['200 joueurs','10 utilisateurs','Planning avancé','Transferts & licences','Collecte de dons','Rapports PDF','Support prioritaire'] as $item)
                        <li class="flex items-center gap-2.5 text-sm text-blue-100">
                            <svg class="w-4 h-4 text-[#F97316] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('login') }}"
                   class="block text-center py-3.5 rounded-xl text-sm font-black bg-[#F97316] text-white hover:bg-orange-500 transition-colors shadow-lg shadow-orange-500/30">
                    Commencer l'essai
                </a>
            </div>

            {{-- Elite --}}
            <div class="card-lift bg-[#F8FAFC] border border-gray-200 rounded-2xl p-8 reveal reveal-delay-2">
                <p class="text-gray-400 text-xs font-black uppercase tracking-widest mb-3">Elite</p>
                <div class="flex items-baseline gap-1.5 mb-1">
                    <span class="text-4xl font-black text-[#1E3A5F]">150 000</span>
                    <span class="text-gray-400 text-sm font-medium">FCFA / mois</span>
                </div>
                <p class="text-gray-500 text-sm mb-7">Pour les académies et clubs professionnels.</p>

                <ul class="space-y-3 mb-8">
                    @foreach(['Joueurs illimités','Utilisateurs illimités','Multi-équipes','API & intégrations','Domaine personnalisé','Manager dédié','SLA 99.9%'] as $item)
                        <li class="flex items-center gap-2.5 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('login') }}"
                   class="block text-center py-3 rounded-xl text-sm font-bold border-2 border-[#1E3A5F] text-[#1E3A5F] hover:bg-[#1E3A5F] hover:text-white transition-colors">
                    Nous contacter
                </a>
            </div>

        </div>

        <p class="text-center text-sm text-gray-400 mt-10 reveal">
            💳 Orange Money · MTN Mobile Money · Wave · Virement bancaire · Espèces
        </p>

    </div>
</section>


{{-- ============================================================ --}}
{{-- TÉMOIGNAGES                                                  --}}
{{-- ============================================================ --}}
<section id="temoignages" class="py-24 bg-[#F8FAFC]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center max-w-xl mx-auto mb-16 reveal">
            <p class="text-[#F97316] text-xs font-black uppercase tracking-widest mb-3">Témoignages</p>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#1E3A5F] leading-tight">
                Ils ont transformé leur club
            </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @foreach([
                [
                    'initiales' => 'MK',
                    'couleur'   => 'bg-blue-600',
                    'nom'       => 'Moussa Konaté',
                    'poste'     => 'Directeur, AS Abidjan FC',
                    'ville'     => 'Abidjan, Côte d\'Ivoire',
                    'texte'     => '"Avant TeamTrack je gérais tout sur des cahiers et WhatsApp. Maintenant en 5 minutes je sais exactement où en est chaque joueur. C\'est une révolution pour notre club."',
                ],
                [
                    'initiales' => 'AF',
                    'couleur'   => 'bg-[#F97316]',
                    'nom'       => 'Aminata Fall',
                    'poste'     => 'Présidente, Étoile de Dakar',
                    'ville'     => 'Dakar, Sénégal',
                    'texte'     => '"La collecte de dons nous a permis de lever 2 millions FCFA en un mois pour nos maillots. Nos supporters adorent soutenir le club directement via la plateforme."',
                ],
                [
                    'initiales' => 'BS',
                    'couleur'   => 'bg-emerald-600',
                    'nom'       => 'Boubacar Sylla',
                    'poste'     => 'Manager, Racing Conakry',
                    'ville'     => 'Conakry, Guinée',
                    'texte'     => '"Le suivi médical des joueurs est excellent. On a évité plusieurs rechutes de blessures grâce aux alertes. Le support client répond toujours en moins d\'une heure."',
                ],
            ] as $i => $t)
                <div class="card-lift bg-white rounded-2xl p-7 border border-gray-100 shadow-sm reveal reveal-delay-{{ $i }}">

                    {{-- Étoiles --}}
                    <div class="flex gap-0.5 mb-5">
                        @for($s = 0; $s < 5; $s++)
                            <svg class="w-4 h-4 text-[#F97316]" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        @endfor
                    </div>

                    <p class="text-gray-600 text-sm leading-relaxed italic mb-6">{{ $t['texte'] }}</p>

                    <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                        <div class="w-10 h-10 rounded-full {{ $t['couleur'] }} flex items-center justify-center text-white text-sm font-black flex-shrink-0">
                            {{ $t['initiales'] }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-[#0F172A] leading-none">{{ $t['nom'] }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $t['poste'] }}</p>
                            <p class="text-xs text-gray-400">{{ $t['ville'] }}</p>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

    </div>
</section>


{{-- ============================================================ --}}
{{-- CTA FINALE                                                   --}}
{{-- ============================================================ --}}
<section id="contact" class="bg-[#1E3A5F] py-24 relative overflow-hidden">

    {{-- Arrière-plan décoratif --}}
    <div class="absolute inset-0 field-pattern opacity-50"></div>
    <div class="hero-glow w-[400px] h-[400px] bg-[#F97316]/10 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>

    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-7 reveal">

        <p class="text-5xl">🏆</p>

        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight">
            Prêt à transformer la gestion<br>de votre club ?
        </h2>
        <p class="text-blue-200 text-lg">
            Rejoignez plus de 250 clubs africains. 30 jours d'essai gratuit, sans engagement, sans carte bancaire.
        </p>

        <div class="flex flex-wrap justify-center gap-4 pt-2">
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2.5 px-8 py-4 bg-[#F97316] text-white font-black rounded-full hover:bg-orange-500 transition-all shadow-2xl shadow-orange-500/30 text-base">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Commencer l'essai gratuit
            </a>
            <a href="mailto:contact@teamtrack.ci"
               class="inline-flex items-center gap-2 px-8 py-4 bg-white/10 text-white font-semibold rounded-full hover:bg-white/20 transition-colors border border-white/20 text-base">
                Nous contacter
            </a>
        </div>

        <p class="text-blue-300 text-sm pt-2">
            📞 +225 07 00 00 00 &nbsp;&bull;&nbsp; ✉️ contact@teamtrack.ci
        </p>

    </div>
</section>


{{-- ============================================================ --}}
{{-- FOOTER                                                       --}}
{{-- ============================================================ --}}
<footer class="bg-[#0d1f36] text-gray-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

        <div class="grid sm:grid-cols-2 md:grid-cols-5 gap-8 mb-10">

            {{-- Brand --}}
            <div class="sm:col-span-2 space-y-4">
                <a href="/" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-[#2E75B6] rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                            <path d="M2 12h20"/>
                        </svg>
                    </div>
                    <span class="text-white font-black text-lg">TeamTrack</span>
                </a>
                <p class="text-sm leading-relaxed max-w-xs">
                    La plateforme SaaS de référence pour la gestion moderne des clubs de football en Afrique de l'Ouest.
                </p>
                {{-- Réseaux sociaux --}}
                <div class="flex gap-2 pt-1">
                    @foreach([
                        ['Facebook',  'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z'],
                        ['Instagram', 'M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01M6.5 20h11a3 3 0 003-3V7a3 3 0 00-3-3h-11a3 3 0 00-3 3v10a3 3 0 003 3z'],
                        ['Twitter',   'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z'],
                    ] as [$name, $path])
                        <a href="#"
                           class="w-8 h-8 bg-white/8 rounded-lg flex items-center justify-center hover:bg-[#F97316] transition-colors"
                           title="{{ $name }}">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Colonnes de liens --}}
            @foreach([
                ['Produit',    ['Fonctionnalités' => '#fonctionnalites', 'Tarifs' => '#tarifs', 'Connexion' => route('login')]],
                ['Entreprise', ['À propos' => '#', 'Blog' => '#', 'Carrières' => '#']],
                ['Légal',      ['Mentions légales' => '#', 'Confidentialité' => '#', 'CGU' => '#']],
            ] as [$title, $links])
                <div class="space-y-3">
                    <h4 class="text-white text-xs font-black uppercase tracking-widest">{{ $title }}</h4>
                    <ul class="space-y-2">
                        @foreach($links as $label => $href)
                            <li>
                                <a href="{{ $href }}" class="text-sm hover:text-[#F97316] transition-colors">
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

        </div>

        <div class="border-t border-white/8 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <p>&copy; {{ date('Y') }} TeamTrack SaaS. Tous droits réservés.</p>
            <p class="text-gray-600">Conçu avec ❤️ pour les clubs africains</p>
        </div>

    </div>
</footer>


{{-- ============================================================ --}}
{{-- SCRIPTS VANILLA JS                                           --}}
{{-- ============================================================ --}}
<script>
(function () {
    'use strict';

    /* ---------------------------------------------------------- */
    /* 1. Menu hamburger mobile                                    */
    /* ---------------------------------------------------------- */
    const btn       = document.getElementById('hamburger-btn');
    const menu      = document.getElementById('mobile-menu');
    const iconOpen  = document.getElementById('icon-open');
    const iconClose = document.getElementById('icon-close');

    if (btn && menu) {
        btn.addEventListener('click', function () {
            const isOpen = !menu.classList.contains('hidden');

            if (isOpen) {
                menu.classList.add('hidden');
                iconOpen.classList.remove('hidden');
                iconClose.classList.add('hidden');
            } else {
                menu.classList.remove('hidden');
                iconOpen.classList.add('hidden');
                iconClose.classList.remove('hidden');
            }
        });

        /* Fermeture au clic sur un lien du menu mobile */
        menu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                menu.classList.add('hidden');
                iconOpen.classList.remove('hidden');
                iconClose.classList.add('hidden');
            });
        });
    }

    /* ---------------------------------------------------------- */
    /* 2. Reveal au scroll (Intersection Observer)                 */
    /* ---------------------------------------------------------- */
    var revealObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.reveal').forEach(function (el) {
        revealObserver.observe(el);
    });

    /* ---------------------------------------------------------- */
    /* 3. Compteurs animés                                         */
    /* ---------------------------------------------------------- */
    var counterObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            counterObserver.unobserve(entry.target);

            var el       = entry.target;
            var target   = parseInt(el.dataset.counter, 10);
            var suffix   = el.dataset.suffix || '';
            var duration = 1600;
            var fps      = 60;
            var steps    = duration / (1000 / fps);
            var inc      = target / steps;
            var current  = 0;

            var timer = setInterval(function () {
                current += inc;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                var formatted = Math.floor(current).toLocaleString('fr-FR');
                el.innerHTML = formatted + '<span class="text-[#F97316]">' + suffix + '</span>';
            }, 1000 / fps);
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('[data-counter]').forEach(function (el) {
        counterObserver.observe(el);
    });

})();
</script>

</body>
</html>

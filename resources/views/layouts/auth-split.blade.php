<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion — {{ config('app.name', 'TeamTrack') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white">

<div class="min-h-screen flex">

    {{-- ═══════════════════════════════════════════
         PANNEAU GAUCHE — branding TeamTrack
    ════════════════════════════════════════════════ --}}
    <div class="hidden lg:flex lg:w-[52%] xl:w-[55%] relative flex-col overflow-hidden"
         style="background: linear-gradient(160deg, #0c1e35 0%, #1E3A5F 55%, #0e3022 100%);">

        {{-- Lignes de terrain en filigrane --}}
        <svg class="absolute inset-0 w-full h-full opacity-[0.06] pointer-events-none"
             viewBox="0 0 800 900" preserveAspectRatio="xMidYMid slice"
             xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <circle cx="400" cy="430" r="190" fill="none" stroke="white" stroke-width="1.5"/>
            <line x1="0"   y1="430" x2="800" y2="430" stroke="white" stroke-width="1.2"/>
            <line x1="400" y1="0"   x2="400" y2="900" stroke="white" stroke-width="1.2"/>
            <rect x="0"   y="220" width="130" height="420" fill="none" stroke="white" stroke-width="1.2"/>
            <rect x="670" y="220" width="130" height="420" fill="none" stroke="white" stroke-width="1.2"/>
            <rect x="0"   y="330" width="50"  height="200" fill="none" stroke="white" stroke-width="1.2"/>
            <rect x="750" y="330" width="50"  height="200" fill="none" stroke="white" stroke-width="1.2"/>
            <circle cx="400" cy="430" r="12" fill="white" opacity=".25"/>
        </svg>

        {{-- Halo orange décoratif --}}
        <div class="absolute bottom-0 left-0 w-[420px] h-[420px] rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(249,115,22,0.18) 0%, transparent 65%); filter: blur(60px);"
             aria-hidden="true"></div>

        {{-- ── Contenu panneau gauche ── --}}
        <div class="relative z-10 flex flex-col h-full px-10 xl:px-14 py-10">

            {{-- Logo --}}
            <a href="/" class="flex items-center gap-3 flex-shrink-0">
                <div class="w-10 h-10 bg-[#F97316] rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        <path d="M2 12h20"/>
                    </svg>
                </div>
                <span class="text-white font-black text-2xl tracking-tight">TeamTrack</span>
            </a>

            {{-- Illustration + texte central --}}
            <div class="flex-1 flex flex-col items-center justify-center text-center px-4">

                {{-- Illustration SVG terrain de foot --}}
                <div class="mb-8">
                    <svg viewBox="0 0 280 200" class="w-64 xl:w-72" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        {{-- Terrain --}}
                        <rect x="10" y="20" width="260" height="160" rx="6" fill="none" stroke="white" stroke-width="1.2" opacity=".35"/>
                        {{-- Ligne médiane --}}
                        <line x1="140" y1="20" x2="140" y2="180" stroke="white" stroke-width="1" opacity=".35"/>
                        {{-- Cercle central --}}
                        <circle cx="140" cy="100" r="32" fill="none" stroke="white" stroke-width="1" opacity=".35"/>
                        <circle cx="140" cy="100" r="3" fill="white" opacity=".4"/>
                        {{-- Surface gauche --}}
                        <rect x="10" y="60" width="48" height="80" fill="none" stroke="white" stroke-width="1" opacity=".35"/>
                        <rect x="10" y="78" width="20" height="44" fill="none" stroke="white" stroke-width="1" opacity=".35"/>
                        {{-- Surface droite --}}
                        <rect x="222" y="60" width="48" height="80" fill="none" stroke="white" stroke-width="1" opacity=".35"/>
                        <rect x="250" y="78" width="20" height="44" fill="none" stroke="white" stroke-width="1" opacity=".35"/>

                        {{-- Joueurs : ronds colorés avec numéro --}}
                        <circle cx="80"  cy="75"  r="11" fill="#F97316" opacity=".9"/>
                        <text x="80"  y="80"  text-anchor="middle" font-size="9" font-weight="bold" fill="white">7</text>
                        <circle cx="140" cy="60"  r="11" fill="#F97316" opacity=".9"/>
                        <text x="140" y="65"  text-anchor="middle" font-size="9" font-weight="bold" fill="white">9</text>
                        <circle cx="200" cy="75"  r="11" fill="#F97316" opacity=".9"/>
                        <text x="200" y="80"  text-anchor="middle" font-size="9" font-weight="bold" fill="white">11</text>
                        <circle cx="100" cy="130" r="11" fill="white" opacity=".75"/>
                        <text x="100" y="135" text-anchor="middle" font-size="9" font-weight="bold" fill="#1E3A5F">4</text>
                        <circle cx="180" cy="130" r="11" fill="white" opacity=".75"/>
                        <text x="180" y="135" text-anchor="middle" font-size="9" font-weight="bold" fill="#1E3A5F">5</text>
                        <circle cx="140" cy="155" r="11" fill="white" opacity=".75"/>
                        <text x="140" y="160" text-anchor="middle" font-size="9" font-weight="bold" fill="#1E3A5F">1</text>

                        {{-- Ballon --}}
                        <circle cx="140" cy="100" r="7" fill="white" opacity=".9"/>
                        <path d="M138 94 l4 0 l2 5 l-4 3 l-4-3 z" fill="#1E3A5F" opacity=".5"/>

                        {{-- Lignes de passe en pointillés --}}
                        <line x1="80"  y1="75"  x2="140" y2="60"  stroke="white" stroke-width="1" stroke-dasharray="4,3" opacity=".3"/>
                        <line x1="140" y1="60"  x2="200" y2="75"  stroke="white" stroke-width="1" stroke-dasharray="4,3" opacity=".3"/>
                        <line x1="140" y1="100" x2="140" y2="60"  stroke="#F97316" stroke-width="1.5" stroke-dasharray="4,3" opacity=".5"/>
                    </svg>
                </div>

                {{-- Accroche --}}
                <h1 class="text-3xl xl:text-4xl font-extrabold text-white leading-tight mb-4">
                    Gérez votre club<br>
                    <span class="text-[#F97316]">comme un pro.</span>
                </h1>
                <p class="text-white/55 text-base max-w-sm leading-relaxed">
                    La plateforme N°1 de gestion de clubs de football en Afrique de l'Ouest.
                    Joueurs, matchs, finances — tout en un.
                </p>

                {{-- 3 badges stats --}}
                <div class="flex gap-4 mt-8">
                    @foreach([['250+','Clubs actifs'],['18k+','Joueurs gérés'],['98%','Satisfaction']] as [$v,$l])
                        <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3 text-center">
                            <p class="text-white font-extrabold text-lg leading-none">{{ $v }}</p>
                            <p class="text-white/50 text-xs mt-1">{{ $l }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Carte support en bas --}}
            <div class="flex-shrink-0 bg-white/10 border border-white/15 rounded-2xl px-6 py-5 backdrop-blur-sm">
                <div class="flex items-start gap-4">
                    <div class="w-9 h-9 rounded-full bg-[#F97316]/20 border border-[#F97316]/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-[#F97316]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm mb-1.5">Support technique</p>
                        <p class="text-white/55 text-xs leading-relaxed">
                            Helpdesk : <span class="text-white/80">+225 07 00 00 00</span><br>
                            Email : <span class="text-white/80">support@teamtrack.app</span>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         PANNEAU DROIT — formulaire de connexion
    ════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col items-center justify-center px-6 sm:px-10 lg:px-16 xl:px-20 bg-white">

        {{-- Logo mobile uniquement --}}
        <div class="lg:hidden mb-8">
            <a href="/" class="flex items-center gap-2">
                <div class="w-9 h-9 bg-[#1E3A5F] rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        <path d="M2 12h20"/>
                    </svg>
                </div>
                <span class="text-[#1E3A5F] font-black text-xl">TeamTrack</span>
            </a>
        </div>

        <div class="w-full max-w-md">
            {{ $slot }}
        </div>

        <p class="mt-10 text-xs text-gray-400 text-center">
            &copy; {{ date('Y') }} TeamTrack · Tous droits réservés
        </p>
    </div>

</div>

</body>
</html>

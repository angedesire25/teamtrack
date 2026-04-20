<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenant?->name ?? 'Club' }} — Faire un don</title>
    @php
        $tenant  = app()->has('tenant') ? app('tenant') : null;
        $primary = $tenant?->primary_color ?? '#1E3A5F';
    @endphp
    <style>:root { --club-primary: {{ $primary }}; }</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Header public --}}
    <header class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm font-bold"
                     style="background-color: var(--club-primary);">
                    {{ strtoupper(substr($tenant?->name ?? 'C', 0, 2)) }}
                </div>
                <div>
                    <p class="font-bold text-gray-900 text-sm">{{ $tenant?->name ?? 'Club' }}</p>
                    <p class="text-xs text-gray-400">Collecte de dons</p>
                </div>
            </div>
            <a href="{{ url('/') }}" class="text-xs text-gray-400 hover:text-gray-600">
                Retour au site →
            </a>
        </div>
    </header>

    {{-- Mode test Stripe --}}
    @if(str_starts_with(config('services.stripe.key', ''), 'pk_test_'))
    <div class="bg-amber-50 border-b border-amber-200 text-center py-2">
        <p class="text-xs font-semibold text-amber-700">Mode test Stripe — Aucun paiement réel ne sera prélevé</p>
    </div>
    @endif

    <main class="max-w-4xl mx-auto px-4 py-8">
        {{ $slot }}
    </main>

    <footer class="mt-16 py-8 text-center text-xs text-gray-400">
        <p>Paiement sécurisé par <strong>Stripe</strong> · Vos données sont protégées</p>
        <p class="mt-1">Propulsé par <strong>TeamTrack</strong></p>
    </footer>

    @livewireScripts
</body>
</html>

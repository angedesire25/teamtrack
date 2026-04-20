<div class="max-w-lg mx-auto text-center">
    @if($found && $donation)
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-10">
        <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Merci pour votre don !</h1>
        <p class="text-gray-500 mb-6">Votre générosité fait une vraie différence pour notre club.</p>

        <div class="bg-gray-50 rounded-xl p-5 text-left space-y-2 mb-6">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Montant</span>
                <span class="font-bold text-gray-900">{{ number_format($donation->amount, 0, '.', ' ') }} {{ strtoupper($donation->currency) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Type</span>
                <span class="font-semibold text-gray-700">{{ $donation->frequencyLabel() }}</span>
            </div>
            @if($donation->campaign)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Campagne</span>
                <span class="font-semibold text-gray-700">{{ $donation->campaign->title }}</span>
            </div>
            @endif
            @if($donation->receipt_number)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Reçu N°</span>
                <span class="font-semibold text-gray-700">{{ $donation->receipt_number }}</span>
            </div>
            @endif
        </div>

        @if(!$donation->is_anonymous && $donation->donor?->email)
        <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 text-sm text-blue-700 mb-6">
            📧 Un reçu PDF a été envoyé à <strong>{{ $donation->donor->email }}</strong>
        </div>
        @endif

        @if($donation->frequency !== 'one_time')
        <div class="bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 text-sm text-amber-700 mb-6">
            🔄 Votre don <strong>{{ $donation->frequencyLabel() === 'Mensuel' ? 'mensuel' : 'annuel' }}</strong> est actif. Il sera reconduit automatiquement.
        </div>
        @endif

        <a href="{{ url('/dons') }}"
           class="inline-block px-6 py-3 text-sm font-bold text-white rounded-xl"
           style="background-color: var(--club-primary);">
            Faire un autre don
        </a>
    </div>
    @else
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-10">
        <div class="w-20 h-20 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h1 class="text-xl font-extrabold text-gray-900 mb-2">Paiement en cours de traitement</h1>
        <p class="text-gray-500 mb-6">Votre paiement est en cours de confirmation. Vous recevrez un email de confirmation sous quelques instants.</p>
        <a href="{{ url('/dons') }}" class="text-sm font-semibold text-[#1E3A5F] hover:underline">Retour à la page de dons</a>
    </div>
    @endif
</div>

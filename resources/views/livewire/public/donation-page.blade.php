<div>
    @if($campaigns->count() > 1 && !$campaign)
    {{-- Liste des campagnes --}}
    <div class="mb-8">
        <h2 class="text-2xl font-extrabold text-gray-900 mb-2">Nos campagnes de dons</h2>
        <p class="text-gray-500">Choisissez une campagne à soutenir</p>
    </div>
    <div class="grid sm:grid-cols-2 gap-6">
        @foreach($campaigns as $c)
        <a href="{{ url('/dons/'.$c->id) }}"
           class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:border-[#1E3A5F]/30 hover:shadow-md transition-all block">
            <h3 class="font-bold text-gray-900 text-lg mb-2">{{ $c->title }}</h3>
            @if($c->description)
            <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ $c->description }}</p>
            @endif
            @if($c->goal_amount)
            <div class="mb-3">
                <div class="flex justify-between text-xs font-semibold mb-1">
                    <span class="text-gray-500">{{ number_format($c->collected_amount,0,'.',' ') }} F collectés</span>
                    <span style="color: var(--club-primary)">{{ $c->progressPercent() }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all" style="width:{{ $c->progressPercent() }}%; background-color: var(--club-primary);"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1">Objectif : {{ number_format($c->goal_amount,0,'.',' ') }} F</p>
            </div>
            @endif
            <span class="text-sm font-bold" style="color: var(--club-primary)">Faire un don →</span>
        </a>
        @endforeach
    </div>

    @else
    {{-- Formulaire de don --}}
    <div class="grid lg:grid-cols-5 gap-8">

        {{-- Colonne gauche : info campagne --}}
        <div class="lg:col-span-2">
            @if($campaign)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sticky top-6">
                <h2 class="text-xl font-extrabold text-gray-900 mb-3">{{ $campaign->title }}</h2>
                @if($campaign->description)
                <p class="text-gray-600 text-sm leading-relaxed mb-5">{{ $campaign->description }}</p>
                @endif

                @if($campaign->goal_amount)
                <div class="mb-5">
                    <div class="flex justify-between text-sm font-bold mb-2">
                        <span class="text-gray-700">{{ number_format($campaign->collected_amount,0,'.',' ') }} F</span>
                        <span style="color: var(--club-primary)">{{ $campaign->progressPercent() }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-500" style="width:{{ $campaign->progressPercent() }}%; background-color: var(--club-primary);"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1.5">Objectif : {{ number_format($campaign->goal_amount,0,'.',' ') }} F CFA</p>
                </div>
                @endif

                @if($campaign->end_date)
                <div class="bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 text-sm">
                    <p class="font-semibold text-amber-700">⏰ Campagne jusqu'au</p>
                    <p class="text-amber-600">{{ $campaign->end_date->translatedFormat('d F Y') }}</p>
                </div>
                @endif
            </div>
            @else
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white text-xl font-bold mb-4"
                     style="background-color: var(--club-primary);">
                    ❤️
                </div>
                <h2 class="text-xl font-extrabold text-gray-900 mb-2">Soutenez votre club</h2>
                <p class="text-gray-500 text-sm">Chaque don compte et contribue directement au développement de nos joueurs et de nos équipes.</p>
            </div>
            @endif
        </div>

        {{-- Colonne droite : formulaire --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                @if(!$step2)
                {{-- Étape 1 : Montant --}}
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900 text-lg">Choisissez votre montant</h3>
                    <p class="text-sm text-gray-400 mt-0.5">En F CFA</p>
                </div>
                <div class="px-6 py-6 space-y-6">
                    {{-- Montants suggérés --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($campaign?->defaultSuggestedAmounts() ?? [5000,10000,25000,50000] as $amt)
                        <button type="button" wire:click="selectAmount('{{ $amt }}')"
                                class="py-3 rounded-xl border-2 text-sm font-bold transition-all
                                       {{ $selectedAmount == $amt
                                            ? 'border-[--club-primary] text-white'
                                            : 'border-gray-200 text-gray-700 hover:border-gray-300' }}"
                                style="{{ $selectedAmount == $amt ? 'background-color: var(--club-primary); border-color: var(--club-primary);' : '' }}">
                            {{ number_format($amt,0,'.',' ') }} F
                        </button>
                        @endforeach
                    </div>

                    {{-- Montant libre --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ou saisissez un montant libre</label>
                        <div class="relative">
                            <input wire:model.live="customAmount" type="number" min="100" step="100"
                                   placeholder="Ex : 15 000"
                                   class="w-full pl-4 pr-16 py-3 border-2 border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-[--club-primary] transition-colors"
                                   style="{{ $customAmount ? 'border-color: var(--club-primary);' : '' }}">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">F CFA</span>
                        </div>
                        @error('selectedAmount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        @error('customAmount')   <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Fréquence --}}
                    @if(!$campaign || $campaign->allow_recurring)
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Fréquence</label>
                        <div class="flex gap-2">
                            @foreach(['one_time' => 'Don unique', 'monthly' => 'Mensuel', 'annual' => 'Annuel'] as $val => $label)
                            <button type="button" wire:click="$set('frequency','{{ $val }}')"
                                    class="flex-1 py-2.5 rounded-xl border-2 text-sm font-semibold transition-all
                                           {{ $frequency === $val ? 'text-white' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}"
                                    style="{{ $frequency === $val ? 'background-color: var(--club-primary); border-color: var(--club-primary);' : '' }}">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>
                        @if($frequency !== 'one_time')
                        <p class="text-xs text-amber-600 bg-amber-50 rounded-lg px-3 py-2 mt-2">
                            Votre carte sera débitée automatiquement chaque {{ $frequency === 'monthly' ? 'mois' : 'année' }}. Annulable à tout moment.
                        </p>
                        @endif
                    </div>
                    @endif

                    <button type="button" wire:click="goToStep2"
                            class="w-full py-4 text-white font-extrabold rounded-xl text-base shadow-lg transition-all hover:opacity-90 active:scale-[.98]"
                            style="background-color: var(--club-primary);">
                        <span wire:loading.remove>
                            Continuer →
                            @if($selectedAmount || $customAmount)
                            ({{ number_format((float)($customAmount ?: $selectedAmount), 0, '.', ' ') }} F)
                            @endif
                        </span>
                        <span wire:loading>Chargement…</span>
                    </button>
                </div>

                @else
                {{-- Étape 2 : Informations donateur --}}
                <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                    <button wire:click="backToStep1" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Vos informations</h3>
                        <p class="text-sm text-gray-400">Don de {{ number_format((float)($customAmount ?: $selectedAmount), 0, '.', ' ') }} F · {{ $frequency === 'one_time' ? 'Unique' : ($frequency === 'monthly' ? 'Mensuel' : 'Annuel') }}</p>
                    </div>
                </div>
                <form wire:submit="checkout" class="px-6 py-6 space-y-4">
                    {{-- Anonyme --}}
                    @if(!$campaign || $campaign->allow_anonymous)
                    <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                        <input wire:model.live="isAnonymous" id="anon" type="checkbox"
                               class="w-4 h-4 rounded border-gray-300 text-[#1E3A5F] focus:ring-[#1E3A5F]/30">
                        <label for="anon" class="text-sm font-semibold text-gray-700 cursor-pointer">Don anonyme</label>
                    </div>
                    @endif

                    @if(!$isAnonymous)
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Prénom *</label>
                            <input wire:model="firstName" type="text" autofocus
                                   class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                            @error('firstName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom *</label>
                            <input wire:model="lastName" type="text"
                                   class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
                            @error('lastName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email * <span class="text-xs font-normal text-gray-400">(votre reçu sera envoyé ici)</span></label>
                        <input wire:model="email" type="email"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                               placeholder="vous@email.com">
                        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Message (optionnel)</label>
                        <textarea wire:model="message" rows="2"
                                  class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]"
                                  placeholder="Un mot d'encouragement…"></textarea>
                    </div>

                    <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 text-xs text-blue-700">
                        🔒 Paiement sécurisé par <strong>Stripe</strong>. Vous serez redirigé vers leur page de paiement.
                        @if(!$isAnonymous) Un reçu PDF vous sera envoyé par email automatiquement. @endif
                    </div>

                    <button type="submit"
                            class="w-full py-4 text-white font-extrabold rounded-xl text-base shadow-lg transition-all hover:opacity-90 active:scale-[.98]"
                            style="background-color: var(--club-primary);">
                        <span wire:loading.remove wire:target="checkout">
                            💳 Payer {{ number_format((float)($customAmount ?: $selectedAmount), 0, '.', ' ') }} F CFA
                        </span>
                        <span wire:loading wire:target="checkout">Redirection vers Stripe…</span>
                    </button>
                </form>
                @endif

            </div>

            {{-- Sécurité --}}
            <div class="flex items-center justify-center gap-4 mt-4 text-xs text-gray-400">
                <span class="flex items-center gap-1">🔒 SSL sécurisé</span>
                <span class="flex items-center gap-1">✅ Stripe certifié PCI</span>
                <span class="flex items-center gap-1">📧 Reçu automatique</span>
            </div>
        </div>
    </div>
    @endif
</div>

<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth-split')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        if (auth()->user()->is_super_admin) {
            $this->redirect(route('superadmin.dashboard'), navigate: true);
            return;
        }

        $this->redirectIntended(default: route('club.dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    {{-- Titre --}}
    <div class="mb-8">
        <h2 class="text-2xl font-extrabold text-[#1E3A5F] tracking-tight">Connexion</h2>
        <p class="text-gray-400 text-sm mt-1">Bienvenue ! Entrez vos identifiants pour accéder à votre espace.</p>
    </div>

    {{-- Statut session (ex : lien de réinitialisation envoyé) --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Adresse e-mail</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-[18px] h-[18px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </span>
                <input wire:model="form.email"
                       id="email" type="email" name="email"
                       required autofocus autocomplete="username"
                       placeholder="votre@email.com"
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-800
                              placeholder-gray-400 bg-gray-50 focus:bg-white
                              focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/25 focus:border-[#1E3A5F]
                              transition-colors" />
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-1.5" />
        </div>

        {{-- Mot de passe --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="text-sm font-semibold text-gray-700">Mot de passe</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate
                       class="text-xs font-semibold text-[#F97316] hover:text-orange-600 transition-colors">
                        Mot de passe oublié ?
                    </a>
                @endif
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-[18px] h-[18px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </span>
                <input wire:model="form.password"
                       id="password" type="password" name="password"
                       required autocomplete="current-password"
                       placeholder="••••••••"
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-800
                              placeholder-gray-400 bg-gray-50 focus:bg-white
                              focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/25 focus:border-[#1E3A5F]
                              transition-colors" />
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-1.5" />
        </div>

        {{-- Se souvenir de moi --}}
        <div class="flex items-center">
            <input wire:model="form.remember"
                   id="remember" type="checkbox" name="remember"
                   class="w-4 h-4 rounded border-gray-300 text-[#1E3A5F] focus:ring-[#1E3A5F]/30 cursor-pointer">
            <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer select-none">
                Se souvenir de moi
            </label>
        </div>

        {{-- Séparateur --}}
        <div class="pt-1">
            {{-- Bouton connexion --}}
            <button type="submit"
                    class="w-full py-3.5 bg-[#1E3A5F] text-white font-bold rounded-xl text-sm
                           hover:bg-[#162d4a] active:scale-[.98] transition-all
                           shadow-md shadow-[#1E3A5F]/20
                           focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/40">
                <span wire:loading.remove wire:target="login">Se connecter</span>
                <span wire:loading wire:target="login" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Connexion en cours…
                </span>
            </button>

            {{-- Séparateur Or --}}
            <div class="flex items-center gap-3 my-4">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-xs text-gray-400 font-medium">Ou</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            {{-- Bouton demo --}}
            <a href="mailto:support@teamtrack.app"
               class="flex items-center justify-center w-full py-3.5 border-2 border-[#1E3A5F] text-[#1E3A5F] font-bold rounded-xl text-sm
                      hover:bg-[#1E3A5F] hover:text-white transition-all">
                Demander un accès
            </a>
        </div>

    </form>
</div>

<div class="relative" x-data="{ open: @entangle('open') }" @click.outside="open = false">

    {{-- Bouton cloche --}}
    <button @click="open = !open"
            class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full leading-none">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak
         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden origin-top-right">

        {{-- En-tête --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <span class="text-sm font-semibold text-gray-800">Notifications</span>
            <div class="flex items-center gap-3">
                @if($unreadCount > 0)
                    <button wire:click="markAllRead"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors">
                        Tout marquer lu
                    </button>
                @endif
                @if($notifications->isNotEmpty())
                    <button wire:click="deleteAll"
                            wire:confirm="Supprimer toutes les notifications ?"
                            class="text-xs text-gray-400 hover:text-red-500 transition-colors">
                        Tout supprimer
                    </button>
                @endif
            </div>
        </div>

        {{-- Liste --}}
        <div class="divide-y divide-gray-50 max-h-96 overflow-y-auto">
            @forelse($notifications as $notif)
                @php
                    $data     = $notif->data;
                    $isLogin  = $data['event'] === 'login';
                    $isUnread = $notif->read_at === null;
                    $time     = \Carbon\Carbon::parse($data['occurred_at'])->diffForHumans();
                @endphp
                <div class="flex items-start gap-3 px-4 py-3 {{ $isUnread ? 'bg-blue-50/40' : '' }} hover:bg-gray-50 transition-colors group">
                    {{-- Icône --}}
                    <div class="flex-shrink-0 mt-0.5 w-8 h-8 rounded-full flex items-center justify-center
                        {{ $isLogin ? 'bg-emerald-100' : 'bg-amber-100' }}">
                        @if($isLogin)
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        @endif
                    </div>

                    {{-- Texte --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800 leading-snug">
                            <span class="font-semibold">{{ $data['user_name'] }}</span>
                            s'est {{ $isLogin ? 'connecté(e)' : 'déconnecté(e)' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $time }}
                            @if(!empty($data['ip']))
                                · {{ $data['ip'] }}
                            @endif
                        </p>
                    </div>

                    {{-- Bouton marquer lu --}}
                    <div class="flex-shrink-0 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        @if($isUnread)
                            <button wire:click="markRead('{{ $notif->id }}')"
                                    class="p-1 text-gray-300 hover:text-blue-500 transition-colors"
                                    title="Marquer comme lu">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <circle cx="10" cy="10" r="4"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-10 text-center">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-sm text-gray-400">Aucune notification</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

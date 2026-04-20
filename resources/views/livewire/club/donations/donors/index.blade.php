<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('club.donations.dashboard') }}" wire:navigate class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-extrabold text-gray-900">Donateurs</h1>
            <p class="text-gray-500 text-sm mt-0.5">Historique et profils</p>
        </div>
        <a href="{{ route('club.donations.export-csv') }}"
           class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Liste donateurs --}}
        <div class="{{ $viewingDonorId ? 'lg:col-span-1' : 'lg:col-span-3' }}">
            <div class="relative mb-4">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Rechercher un donateur…"
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="divide-y divide-gray-50">
                    @forelse($donors as $donor)
                    <button wire:click="viewDonor({{ $donor->id }})"
                            class="w-full flex items-center gap-3 px-5 py-4 hover:bg-gray-50 transition-colors text-left
                                   {{ $viewingDonorId === $donor->id ? 'bg-blue-50' : '' }}">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                             style="background-color: var(--club-primary);">
                            {{ strtoupper(substr($donor->first_name,0,1).substr($donor->last_name,0,1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm truncate">{{ $donor->fullName() }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $donor->email ?? 'Sans email' }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-emerald-600">{{ number_format($donor->total_donated ?? 0, 0, '.', ' ') }} F</p>
                            <p class="text-xs text-gray-400">{{ $donor->completed_donations_count }} don(s)</p>
                        </div>
                    </button>
                    @empty
                    <div class="px-5 py-14 text-center text-gray-400">Aucun donateur</div>
                    @endforelse
                </div>
                @if($donors->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $donors->links() }}</div>
                @endif
            </div>
        </div>

        {{-- Historique donateur --}}
        @if($viewingDonorId && $viewingDonor)
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-gray-900">{{ $viewingDonor->fullName() }}</h2>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $viewingDonor->email ?? 'Sans email' }}</p>
                    </div>
                    <button wire:click="$set('viewingDonorId',null)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-4 grid grid-cols-3 gap-4 border-b border-gray-100">
                    <div class="text-center">
                        <p class="text-xl font-extrabold text-emerald-600">{{ number_format($donorDonations->sum('amount'),0,'.',' ') }} F</p>
                        <p class="text-xs text-gray-400">Total donné</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xl font-extrabold text-gray-900">{{ $donorDonations->count() }}</p>
                        <p class="text-xs text-gray-400">Donations</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xl font-extrabold text-gray-900">{{ $donorDonations->first()?->created_at->format('d/m/Y') ?? '—' }}</p>
                        <p class="text-xs text-gray-400">1er don</p>
                    </div>
                </div>

                <div class="divide-y divide-gray-50">
                    @forelse($donorDonations as $d)
                    <div class="flex items-center gap-4 px-6 py-4">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-800">{{ $d->campaign?->title ?? 'Don général' }}</p>
                            <p class="text-xs text-gray-400">{{ $d->created_at->translatedFormat('d F Y') }} · {{ $d->frequencyLabel() }}</p>
                        </div>
                        <p class="font-bold text-emerald-600">{{ number_format($d->amount,0,'.',' ') }} F</p>
                        @if($d->receipt_number)
                        <a href="{{ route('club.donations.receipt-pdf', $d->id) }}"
                           class="p-1.5 text-gray-400 hover:text-[#1E3A5F] hover:bg-gray-100 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </a>
                        @endif
                    </div>
                    @empty
                    <div class="px-6 py-10 text-center text-gray-400">Aucun don complété</div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

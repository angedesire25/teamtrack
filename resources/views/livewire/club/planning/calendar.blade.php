<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Planning</h1>
            <p class="text-gray-500 text-sm mt-0.5">Matchs, entraînements et événements du club</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <select wire:model.live="filterTeam"
                    class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20">
                <option value="">Toutes les équipes</option>
                @foreach($teams as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>
            <a href="{{ route('club.planning.fields') }}" wire:navigate
               class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Terrains
            </a>
            <a href="{{ route('club.planning.export-ics') }}"
               class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Export .ics
            </a>
            <a href="{{ route('club.planning.create') }}" wire:navigate
               class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl shadow-sm"
               style="background-color: var(--club-primary);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvel événement
            </a>
        </div>
    </div>

    {{-- Légende --}}
    <div class="flex flex-wrap gap-3 mb-4">
        <span class="flex items-center gap-1.5 text-xs font-semibold text-gray-600">
            <span class="w-3 h-3 rounded-full bg-red-500"></span> Match
        </span>
        <span class="flex items-center gap-1.5 text-xs font-semibold text-gray-600">
            <span class="w-3 h-3 rounded-full bg-blue-500"></span> Entraînement
        </span>
        <span class="flex items-center gap-1.5 text-xs font-semibold text-gray-600">
            <span class="w-3 h-3 rounded-full bg-violet-500"></span> Réunion
        </span>
        <span class="flex items-center gap-1.5 text-xs font-semibold text-gray-600">
            <span class="w-3 h-3 rounded-full bg-amber-500"></span> Déplacement
        </span>
    </div>

    {{-- Calendrier --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4" id="calendar-wrap">
        <div id="teamtrack-calendar"></div>
    </div>

    {{-- Popup événement --}}
    <div id="event-popup"
         class="hidden fixed z-50 bg-white rounded-2xl shadow-2xl border border-gray-200 w-72 p-4"
         style="pointer-events:auto;">
        <div class="flex items-start justify-between mb-2">
            <div>
                <p id="popup-title" class="font-bold text-gray-900 text-sm"></p>
                <p id="popup-type" class="text-xs font-semibold mt-0.5"></p>
            </div>
            <button onclick="document.getElementById('event-popup').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 ml-2 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="space-y-1 text-xs text-gray-500">
            <p id="popup-time"></p>
            <p id="popup-field" class="hidden"></p>
            <p id="popup-team" class="hidden"></p>
            <p id="popup-opponent" class="hidden"></p>
        </div>
        <div class="flex gap-2 mt-3">
            <a id="popup-edit-btn" href="#"
               class="flex-1 text-center py-1.5 text-xs font-bold text-white rounded-lg"
               style="background-color: var(--club-primary);">Modifier</a>
            <a id="popup-sheet-btn" href="#"
               class="flex-1 text-center py-1.5 text-xs font-semibold text-[#1E3A5F] bg-blue-50 rounded-lg border border-blue-100 hidden">Feuille</a>
        </div>
    </div>
</div>

@push('scripts')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales/fr.global.min.js'></script>
<script>
(function() {
    const eventsData = @json(json_decode($eventsJson));

    function initCalendar() {
        const el = document.getElementById('teamtrack-calendar');
        if (!el || window._fcInstance) return;

        window._fcInstance = new FullCalendar.Calendar(el, {
            locale: 'fr',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            height: 'auto',
            events: eventsData,
            eventClick: function(info) {
                const e    = info.event;
                const p    = e.extendedProps;
                const pop  = document.getElementById('event-popup');
                const rect = info.el.getBoundingClientRect();

                document.getElementById('popup-title').textContent   = e.title;
                document.getElementById('popup-type').textContent     = p.typeLabel;
                document.getElementById('popup-type').style.color     = e.backgroundColor;

                const start = e.start ? e.start.toLocaleString('fr-FR', {day:'2-digit',month:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '';
                document.getElementById('popup-time').textContent = start;

                const fieldEl = document.getElementById('popup-field');
                if (p.field) { fieldEl.textContent = '📍 ' + p.field; fieldEl.classList.remove('hidden'); }
                else fieldEl.classList.add('hidden');

                const teamEl = document.getElementById('popup-team');
                if (p.team) { teamEl.textContent = '👥 ' + p.team; teamEl.classList.remove('hidden'); }
                else teamEl.classList.add('hidden');

                const oppEl = document.getElementById('popup-opponent');
                if (p.opponent) { oppEl.textContent = '⚽ vs ' + p.opponent; oppEl.classList.remove('hidden'); }
                else oppEl.classList.add('hidden');

                document.getElementById('popup-edit-btn').href = '/planning/' + e.id + '/edit';

                const sheetBtn = document.getElementById('popup-sheet-btn');
                if (p.type === 'match') {
                    sheetBtn.href = '/planning/' + e.id + '/match-sheet';
                    sheetBtn.textContent = 'Feuille de match';
                    sheetBtn.classList.remove('hidden');
                } else if (p.type === 'training') {
                    sheetBtn.href = '/planning/' + e.id + '/attendance';
                    sheetBtn.textContent = 'Présences';
                    sheetBtn.classList.remove('hidden');
                } else {
                    sheetBtn.classList.add('hidden');
                }

                // Positionnement de la popup
                const scrollY = window.scrollY || document.documentElement.scrollTop;
                pop.style.top  = (rect.bottom + scrollY + 8) + 'px';
                pop.style.left = Math.min(rect.left, window.innerWidth - 300) + 'px';
                pop.classList.remove('hidden');
                info.jsEvent.stopPropagation();
            },
        });
        window._fcInstance.render();
    }

    document.addEventListener('click', function(e) {
        const pop = document.getElementById('event-popup');
        if (pop && !pop.contains(e.target)) pop.classList.add('hidden');
    });

    document.addEventListener('livewire:navigated', function() {
        window._fcInstance = null;
        setTimeout(initCalendar, 50);
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => setTimeout(initCalendar, 50));
    } else {
        setTimeout(initCalendar, 50);
    }
})();
</script>
@endpush

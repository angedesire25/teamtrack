<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IcsExportController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $events = Event::with(['field', 'team'])
            ->when($request->team_id, fn($q) => $q->where('team_id', $request->team_id))
            ->orderBy('starts_at')
            ->get();

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//TeamTrack//Planning//FR',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:TeamTrack Planning',
            'X-WR-TIMEZONE:Africa/Abidjan',
        ];

        foreach ($events as $event) {
            $uid     = 'event-' . $event->id . '@teamtrack.app';
            $dtstart = $event->starts_at->utc()->format('Ymd\THis\Z');
            $dtend   = $event->ends_at->utc()->format('Ymd\THis\Z');
            $dtstamp = now()->utc()->format('Ymd\THis\Z');

            $summary  = $this->escape($event->title);
            $location = $event->field ? $this->escape($event->field->name . ($event->field->address ? ', ' . $event->field->address : '')) : '';
            $desc     = $this->escape(implode(' | ', array_filter([
                $event->typeLabel(),
                $event->team?->name,
                $event->opponent ? 'vs ' . $event->opponent : null,
                $event->notes,
            ])));

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:' . $uid;
            $lines[] = 'DTSTAMP:' . $dtstamp;
            $lines[] = 'DTSTART:' . $dtstart;
            $lines[] = 'DTEND:' . $dtend;
            $lines[] = 'SUMMARY:' . $summary;
            if ($location) $lines[] = 'LOCATION:' . $location;
            if ($desc)     $lines[] = 'DESCRIPTION:' . $desc;
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        $content = implode("\r\n", $lines) . "\r\n";

        return response($content, 200, [
            'Content-Type'        => 'text/calendar; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="planning.ics"',
        ]);
    }

    private function escape(string $str): string
    {
        return str_replace(["\r\n", "\n", "\r", ',', ';', '\\'], ['\n', '\n', '\n', '\,', '\;', '\\\\'], $str);
    }
}

<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Player;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DocumentController extends Controller
{
    /** Téléchargement sécurisé d'un document (vérifie l'appartenance au tenant) */
    public function download(int $document): Response
    {
        $doc = Document::where('tenant_id', app('tenant')->id)->findOrFail($document);

        abort_unless(Storage::disk('local')->exists($doc->file_path), 404);

        $contents = Storage::disk('local')->get($doc->file_path);
        $ext      = $doc->extension();
        $filename = \Illuminate\Support\Str::slug($doc->title).'-v'.$doc->version.'.'.$ext;

        return response($contents, 200, [
            'Content-Type'        => $doc->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /** Génère et télécharge un ZIP contenant tous les documents d'un joueur */
    public function zipDossier(int $player): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $player = Player::where('tenant_id', app('tenant')->id)->findOrFail($player);

        $documents = Document::where('tenant_id', app('tenant')->id)
            ->where('documentable_type', Player::class)
            ->where('documentable_id', $player->id)
            ->orderBy('document_type')
            ->orderBy('document_group_id')
            ->orderBy('version')
            ->get();

        // Répertoire temporaire
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zipName = 'dossier-'
            .\Illuminate\Support\Str::slug($player->last_name.'-'.$player->first_name)
            .'-'.now()->format('Ymd')
            .'.zip';

        $zipPath = $tempDir.'/'.$zipName;

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Organisation du ZIP par type de document
        foreach ($documents as $doc) {
            $fullPath = Storage::disk('local')->path($doc->file_path);
            if (!file_exists($fullPath)) {
                continue;
            }

            $folder   = $doc->typeLabel();
            $basename = \Illuminate\Support\Str::slug($doc->title)
                .'-v'.$doc->version
                .'.'.$doc->extension();

            $zip->addFile($fullPath, $folder.'/'.$basename);
        }

        // Fiche récapitulative au format texte
        $summary = $this->buildSummary($player, $documents);
        $zip->addFromString('RECAPITULATIF.txt', $summary);

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    private function buildSummary(Player $player, $documents): string
    {
        $lines = [
            'DOSSIER ADMINISTRATIF',
            str_repeat('=', 40),
            'Joueur : '.$player->last_name.' '.$player->first_name,
            'Généré le : '.now()->isoFormat('D MMMM YYYY à HH:mm'),
            str_repeat('-', 40),
            '',
        ];

        foreach ($documents->groupBy('document_type') as $type => $docs) {
            $lines[] = strtoupper($docs->first()->typeLabel());
            foreach ($docs as $doc) {
                $signed  = $doc->isSigned() ? ' [SIGNÉ le '.$doc->signed_at->format('d/m/Y').']' : '';
                $expires = $doc->expires_at ? ' | Exp. '.$doc->expires_at->format('d/m/Y') : '';
                $lines[] = '  • '.$doc->title.' (v'.$doc->version.')'.$expires.$signed;
            }
            $lines[] = '';
        }

        return implode("\n", $lines);
    }
}

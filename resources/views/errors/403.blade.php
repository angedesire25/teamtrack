<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Accès refusé · TeamTrack</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '403';
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 40vw;
            font-weight: 900;
            color: rgba(220, 38, 38, 0.04);
            letter-spacing: -0.05em;
            pointer-events: none;
            user-select: none;
            line-height: 1;
        }

        .card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(30, 58, 95, 0.14);
            max-width: 520px;
            width: 100%;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .card__header {
            background: #1E3A5F;
            padding: 28px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand { color: #fff; font-size: 1.3rem; font-weight: 800; letter-spacing: 0.5px; }
        .brand span { color: #60a5fa; }

        .http-badge {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }

        /* Bande d'alerte rouge sous le header */
        .alert-bar {
            background: #fef2f2;
            border-bottom: 1px solid #fecaca;
            padding: 10px 40px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: #dc2626;
            font-weight: 600;
        }

        .card__body { padding: 40px 40px 40px; text-align: center; }

        .icon-wrap {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: #fef2f2;
            border: 3px solid #fca5a5;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px;
        }

        .icon-wrap svg { width: 36px; height: 36px; color: #dc2626; stroke: currentColor; fill: none; }

        h1 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 12px;
        }

        .code-line {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #dc2626;
            background: #fef2f2;
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            margin-bottom: 16px;
        }

        p { color: #6b7280; line-height: 1.7; font-size: 0.95rem; }

        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px 18px;
            margin: 20px 0;
            text-align: left;
            font-size: 0.85rem;
            color: #4b5563;
            line-height: 1.6;
        }

        .divider { border: none; border-top: 1px solid #f3f4f6; margin: 24px 0; }

        .actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 22px;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s;
            cursor: pointer;
            border: none;
        }

        .btn-primary { background: #1E3A5F; color: #fff; }
        .btn-primary:hover { background: #162d4a; }
        .btn-secondary { background: #f3f4f6; color: #374151; }
        .btn-secondary:hover { background: #e5e7eb; }

        .footer-note { margin-top: 24px; font-size: 0.75rem; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="card">

        <div class="card__header">
            <div class="brand">Team<span>Track</span></div>
            <span class="http-badge">HTTP 403</span>
        </div>

        <div class="alert-bar">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Accès non autorisé
        </div>

        <div class="card__body">

            <div class="icon-wrap">
                <svg viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>

            <div class="code-line">Accès refusé</div>
            <h1>Vous n'êtes pas autorisé</h1>

            <p>
                Vous n'avez pas les droits nécessaires pour accéder à cette ressource.
                Cette action est réservée à certains rôles ou profils.
            </p>

            @if (!empty($exception?->getMessage()))
                <div class="info-box">
                    <strong style="color:#374151;">Détail :</strong><br>
                    {{ $exception->getMessage() }}
                </div>
            @endif

            <hr class="divider">

            <div class="actions">
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Retour
                </a>
                <a href="/" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Accueil
                </a>
            </div>

            <p class="footer-note">
                TeamTrack · Si vous pensez que c'est une erreur, contactez votre administrateur.
            </p>
        </div>

    </div>
</body>
</html>

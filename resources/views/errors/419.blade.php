<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 — Session expirée · TeamTrack</title>
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
            content: '419';
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 40vw;
            font-weight: 900;
            color: rgba(245, 158, 11, 0.05);
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
            background: rgba(245, 158, 11, 0.2);
            border: 1px solid rgba(245, 158, 11, 0.4);
            color: #fcd34d;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }

        /* Barre de progression simulant le timeout */
        .timeout-bar {
            height: 3px;
            background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 60%, #fde68a 100%);
        }

        .card__body { padding: 40px 40px 40px; text-align: center; }

        .icon-wrap {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: #fffbeb;
            border: 3px solid #fcd34d;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px;
            position: relative;
        }

        .icon-wrap svg { width: 36px; height: 36px; color: #d97706; stroke: currentColor; fill: none; }

        /* Petite horloge animée */
        .clock-hand {
            animation: tick 1s steps(1) infinite;
        }
        @keyframes tick {
            0%   { transform: rotate(0deg);   transform-origin: 12px 12px; }
            25%  { transform: rotate(90deg);  transform-origin: 12px 12px; }
            50%  { transform: rotate(180deg); transform-origin: 12px 12px; }
            75%  { transform: rotate(270deg); transform-origin: 12px 12px; }
            100% { transform: rotate(360deg); transform-origin: 12px 12px; }
        }

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
            color: #d97706;
            background: #fffbeb;
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            margin-bottom: 16px;
        }

        p { color: #6b7280; line-height: 1.7; font-size: 0.95rem; }

        .tip-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 14px 18px;
            margin: 20px 0;
            text-align: left;
            font-size: 0.85rem;
            color: #92400e;
            line-height: 1.6;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .tip-box svg { flex-shrink: 0; margin-top: 1px; }

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

        .btn-amber { background: #f59e0b; color: #fff; }
        .btn-amber:hover { background: #d97706; }

        .footer-note { margin-top: 24px; font-size: 0.75rem; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="card">

        <div class="card__header">
            <div class="brand">Team<span>Track</span></div>
            <span class="http-badge">HTTP 419</span>
        </div>

        <!-- Barre de timeout -->
        <div class="timeout-bar"></div>

        <div class="card__body">

            <div class="icon-wrap">
                <svg viewBox="0 0 24 24" stroke-width="1.5">
                    <circle cx="12" cy="12" r="9"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                    <line x1="12" y1="3" x2="12" y2="5" stroke-linecap="round" class="clock-hand"/>
                </svg>
            </div>

            <div class="code-line">Session expirée</div>
            <h1>Votre session a expiré</h1>

            <p>
                Pour des raisons de sécurité, votre session s'est terminée après
                une période d'inactivité. Veuillez recharger la page et réessayer.
            </p>

            <div class="tip-box">
                <svg width="16" height="16" fill="none" stroke="#d97706" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>
                    Cela arrive souvent si vous avez laissé l'onglet ouvert trop longtemps
                    ou si les cookies de votre navigateur ont été effacés.
                </span>
            </div>

            <hr class="divider">

            <div class="actions">
                <a href="javascript:history.back()" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Retour
                </a>
                <button onclick="window.location.reload()" class="btn btn-amber">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Recharger la page
                </button>
            </div>

            <p class="footer-note">
                TeamTrack · Erreur CSRF (Page Expired) — Jeton de sécurité invalide.
            </p>
        </div>

    </div>
</body>
</html>

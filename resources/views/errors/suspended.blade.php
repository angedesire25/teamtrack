<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès suspendu — {{ $tenant->name }}</title>
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
        }

        .card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(30, 58, 95, 0.12);
            max-width: 520px;
            width: 100%;
            overflow: hidden;
        }

        /* Bandeau supérieur couleur TeamTrack */
        .card__header {
            background: #1E3A5F;
            padding: 32px 40px;
            text-align: center;
        }

        .card__header .logo-text {
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .card__header .club-name {
            color: #93c5fd;
            font-size: 0.95rem;
            margin-top: 4px;
        }

        /* Icône d'avertissement */
        .card__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 40px 0;
        }

        .icon-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #fef2f2;
            border: 3px solid #fca5a5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        /* Corps du message */
        .card__body {
            padding: 24px 40px 40px;
            text-align: center;
        }

        .card__body h1 {
            color: #dc2626;
            font-size: 1.4rem;
            font-weight: 700;
            margin: 20px 0 12px;
        }

        .card__body p {
            color: #4b5563;
            line-height: 1.7;
            font-size: 0.95rem;
        }

        /* Séparateur */
        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 28px 0;
        }

        /* Bloc contact */
        .contact-block {
            background: #f0f4f8;
            border-radius: 10px;
            padding: 16px 20px;
            text-align: left;
        }

        .contact-block p {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .contact-block a {
            color: #1E3A5F;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .contact-block a:hover {
            color: #2E75B6;
            text-decoration: underline;
        }

        /* Code HTTP */
        .http-code {
            margin-top: 20px;
            font-size: 0.75rem;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="card">
        {{-- En-tête avec le nom du club --}}
        <div class="card__header">
            <div class="logo-text">TeamTrack</div>
            <div class="club-name">{{ $tenant->name }}</div>
        </div>

        {{-- Icône d'avertissement --}}
        <div class="card__icon">
            <div class="icon-circle">⚠️</div>
        </div>

        {{-- Corps du message --}}
        <div class="card__body">
            <h1>Accès suspendu</h1>

            <p>
                L'accès au club <strong>{{ $tenant->name }}</strong> a été
                temporairement suspendu pour <strong>défaut de paiement</strong>.
            </p>
            <p style="margin-top: 10px;">
                Veuillez régulariser votre abonnement pour rétablir l'accès
                à toutes les fonctionnalités de la plateforme.
            </p>

            <hr class="divider">

            {{-- Contact support --}}
            <div class="contact-block">
                <p>Besoin d'aide ? Contactez notre support :</p>
                <a href="mailto:support@teamtrack.test">support@teamtrack.test</a>
            </div>

            <p class="http-code">Code HTTP 402 — Payment Required</p>
        </div>
    </div>
</body>
</html>

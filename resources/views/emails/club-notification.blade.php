<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"><style>
body { font-family: sans-serif; color: #374151; background: #f9fafb; margin: 0; padding: 32px 0; }
.wrap { max-width: 560px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; }
.header { background: #1E3A5F; padding: 24px 32px; }
.header h1 { color: white; margin: 0; font-size: 18px; }
.header p { color: #93c5fd; margin: 4px 0 0; font-size: 13px; }
.body { padding: 32px; white-space: pre-line; line-height: 1.6; font-size: 15px; }
.footer { padding: 16px 32px; background: #f9fafb; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; text-align: center; }
</style></head>
<body>
<div class="wrap">
    <div class="header">
        <h1>TeamTrack</h1>
        <p>Message à l'attention de {{ $clubName }}</p>
    </div>
    <div class="body">{{ $body }}</div>
    <div class="footer">TeamTrack · Super Administration · Ce message a été envoyé depuis la plateforme TeamTrack.</div>
</div>
</body>
</html>

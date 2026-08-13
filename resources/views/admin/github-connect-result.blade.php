<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $success ? 'GitHub Connected' : 'GitHub Connection Failed' }} &mdash; {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        :root { color-scheme: light dark; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Figtree', system-ui, -apple-system, sans-serif;
            background: #f9fafb;
            color: #111827;
            padding: 1.5rem;
        }
        @media (prefers-color-scheme: dark) {
            body { background: #030712; color: #f3f4f6; }
        }
        .card {
            max-width: 26rem;
            width: 100%;
            text-align: center;
            background: #ffffff;
            border: 1px solid #f3f4f6;
            border-radius: 1rem;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
            padding: 2.5rem 2rem;
        }
        @media (prefers-color-scheme: dark) {
            .card { background: #111827; border-color: #1f2937; }
        }
        .icon {
            width: 4rem;
            height: 4rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 1.75rem;
        }
        .icon.success { background: rgba(34, 197, 94, 0.12); color: #16a34a; }
        .icon.error { background: rgba(239, 68, 68, 0.12); color: #dc2626; }
        h1 { font-size: 1.25rem; font-weight: 700; margin: 0 0 0.5rem; }
        p { color: #6b7280; line-height: 1.5; margin: 0; }
        @media (prefers-color-scheme: dark) { p { color: #9ca3af; } }
        .hint { margin-top: 1.5rem; font-size: 0.8rem; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon {{ $success ? 'success' : 'error' }}">{{ $success ? '✓' : '✕' }}</div>
        <h1>{{ $success ? 'GitHub account connected' : 'Connection failed' }}</h1>
        <p>{{ $message }}</p>
        <p class="hint">You can close this tab and return to {{ config('app.name') }} to continue.</p>
    </div>
</body>
</html>

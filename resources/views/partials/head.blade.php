<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

{{-- Favicon and app icons --}}
<link rel="icon" href="/favicon.ico" sizes="32x32">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="icon" href="/favicon-16x16.png" type="image/png" sizes="16x16">
<link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
<link rel="apple-touch-icon" href="/apple-touch-icon.png" sizes="180x180">
<link rel="icon" href="/android-chrome-192x192.png" type="image/png" sizes="192x192">
<link rel="icon" href="/android-chrome-512x512.png" type="image/png" sizes="512x512">

{{-- Web app manifest --}}
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#f97316">
<meta name="application-name" content="ChordHound">
<meta name="apple-mobile-web-app-title" content="ChordHound">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@600;700&family=Henny+Penny&display=swap" rel="stylesheet">

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

{{-- Multi-instrument player (loaded after Tone.js from app.js) --}}
<script src="{{ asset('js/multi-instrument-player.js') }}" defer></script>

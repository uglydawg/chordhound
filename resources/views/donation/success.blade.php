<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank You - ChordHound</title>
    <link rel="icon" href="/favicon.ico" sizes="32x32">
    <link rel="icon" href="/favicon-16x16.png" type="image/png" sizes="16x16">
    <link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" sizes="180x180">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Henny+Penny&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        // Check for dark mode preference and apply immediately to prevent flash
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-zinc-900 dark:to-zinc-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-8 text-center">
            <!-- Success Icon -->
            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <!-- Thank You Message -->
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white mb-2">
                Thank You! 🎉
            </h1>
            <p class="text-zinc-600 dark:text-zinc-200 mb-6">
                Your generous support helps keep <span style="font-family: 'Henny Penny', cursive;">ChordHound</span> free and enables us to continue developing amazing features for musicians like you!
            </p>
            
            <!-- ChordHound Logo -->
            <div class="mb-6">
                <svg class="w-16 h-16 mx-auto text-orange-500 dark:text-orange-400" viewBox="0 0 128 128" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Dog head outline -->
                    <path fill="currentColor" d="M64 20c-16 0-29 11-35 24-3 5-3 11 0 16l3 5c0 8 5 14 11 19 5 8 14 14 21 14s16-6 21-14c5-5 11-11 11-19l3-5c3-5 3-11 0-16-6-13-19-24-35-24z"/>
                    <!-- Left ear -->
                    <path fill="currentColor" d="M37 32c-5-3-11 0-13 5-3 5 0 11 5 14l8 5c3 0 5-3 5-5l-5-19z"/>
                    <!-- Right ear -->
                    <path fill="currentColor" d="M91 32c5-3 11 0 13 5 3 5 0 11-5 14l-8 5c-3 0-5-3-5-5l5-19z"/>
                    <!-- Eyes -->
                    <circle fill="currentColor" cx="53" cy="59" r="5"/>
                    <circle fill="currentColor" cx="75" cy="59" r="5"/>
                    <!-- Nose -->
                    <path fill="currentColor" d="M64 69c-3 0-5 3-5 5s3 5 5 5 5-3 5-5-3-5-5-5z"/>
                    <!-- Mouth -->
                    <path fill="currentColor" d="M64 80c-5 0-11 3-11 8 0 3 3 5 5 5h11c3 0 5-3 5-5 0-5-5-8-11-8z"/>
                    <!-- Piano keys at bottom -->
                    <rect fill="currentColor" x="32" y="101" width="8" height="16"/>
                    <rect fill="currentColor" x="48" y="101" width="8" height="16"/>
                    <rect fill="currentColor" x="64" y="101" width="8" height="16"/>
                    <rect fill="currentColor" x="80" y="101" width="8" height="16"/>
                    <rect fill="currentColor" x="96" y="101" width="8" height="16"/>
                    <!-- Black keys -->
                    <rect fill="currentColor" x="39" y="101" width="5" height="11"/>
                    <rect fill="currentColor" x="55" y="101" width="5" height="11"/>
                    <rect fill="currentColor" x="87" y="101" width="5" height="11"/>
                </svg>
            </div>
            
            <!-- Actions -->
            <div class="space-y-3">
                <a href="{{ route('home') }}" 
                   class="w-full inline-flex items-center justify-center px-6 py-3 bg-orange-600 hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-600 text-white font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                    </svg>
                    Start Creating Chords
                </a>
                
                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="w-full inline-flex items-center justify-center px-6 py-2 border border-zinc-200 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 font-medium rounded-lg transition-colors">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" 
                       class="w-full inline-flex items-center justify-center px-6 py-2 border border-zinc-200 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 font-medium rounded-lg transition-colors">
                        Create Free Account
                    </a>
                @endauth
            </div>
        </div>
        
        <!-- Additional Info -->
        <div class="text-center mt-6">
            <p class="text-sm text-zinc-600 dark:text-zinc-300">
                Questions about your donation? Contact us at <a href="mailto:support@chordhound.com" class="text-orange-600 dark:text-orange-300 hover:underline">support@chordhound.com</a>
            </p>
        </div>
    </div>
</body>
</html>
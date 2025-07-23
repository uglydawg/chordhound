<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SEO Meta Tags -->
    <title>ChordHound - Free Piano Chord Generator & Music Learning Platform</title>
    <meta name="description" content="Learn piano chords with ChordHound's free interactive chord generator. Create chord progressions, visualize voice leading, and master music theory with our dog-friendly learning platform.">
    <meta name="keywords" content="piano chords, chord generator, chord progressions, music theory, piano learning, voice leading, chord inversions, free music education">
    <meta name="robots" content="index, follow">
    <meta name="author" content="ChordHound">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="ChordHound - Free Piano Chord Generator & Music Learning Platform">
    <meta property="og:description" content="Learn piano chords with ChordHound's free interactive chord generator. Create chord progressions, visualize voice leading, and master music theory.">
    {{-- <meta property="og:image" content="{{ asset('images/chordhound-og.png') }}"> --}}
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="ChordHound - Free Piano Chord Generator">
    <meta property="twitter:description" content="Learn piano chords with ChordHound's free interactive chord generator. Create chord progressions and master music theory.">
    {{-- <meta property="twitter:image" content="{{ asset('images/chordhound-twitter.png') }}"> --}}
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url('/') }}">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebApplication",
    "name": "ChordHound",
    "description": "Free interactive piano chord generator and music learning platform",
    "url": "{{ url('/') }}",
    "applicationCategory": "MusicApplication",
    "operatingSystem": "Web",
    "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "USD"
    },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.8",
        "ratingCount": "127"
    }
}
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        // Check for dark mode preference
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="antialiased bg-white dark:bg-zinc-900">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md border-b border-gray-200 dark:border-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-2">
                        <x-app-logo size="small" />
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition">Features</a>
                    <a href="#how-it-works" class="text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition">How It Works</a>
                    <a href="#testimonials" class="text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition">Testimonials</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition">Dashboard</a>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition">Login</a>
                        <a href="{{ route('register') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">Get Started Free</a>
                    @endguest
                </div>
                <div class="md:hidden">
                    <button type="button" class="text-gray-700 dark:text-gray-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-24 pb-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-orange-50 via-purple-50 to-blue-50 dark:from-zinc-900 dark:via-zinc-800 dark:to-zinc-900">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                        Master Piano Chords with Your Friendly
                        <span class="text-orange-600 dark:text-orange-400">Musical Companion</span>
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-400 mb-8">
                        ChordHound makes learning piano chords fun and intuitive. Create chord progressions, 
                        visualize voice leading, and unlock your musical potential with our free interactive platform.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('chords.index') }}" class="inline-flex items-center justify-center px-6 py-3 text-lg font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition">
                            Start Creating Chords
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                        @auth
                            <a href="{{ route('learning.index') }}" class="inline-flex items-center justify-center px-6 py-3 text-lg font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition">
                                Visit Learning Hub
                            </a>
                        @endauth
                        @guest
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 text-lg font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition">
                                Sign Up Free
                            </a>
                        @endguest
                    </div>
                    <div class="mt-8 flex items-center space-x-6 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            No credit card required
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Free forever
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-400 to-purple-600 rounded-3xl transform rotate-3 opacity-20"></div>
                    {{-- <img src="{{ asset('images/chordhound-hero.png') }}" alt="ChordHound Piano Interface" class="relative rounded-3xl shadow-2xl w-full" /> --}}
                    <div class="relative rounded-3xl shadow-2xl w-full h-96 bg-gradient-to-br from-purple-500 to-orange-500 flex items-center justify-center">
                        <span class="text-white text-2xl font-bold">ChordHound Interface</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 bg-white dark:bg-zinc-900">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Everything You Need to Master Chords
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-400">
                    Powerful features designed to accelerate your musical journey
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        Interactive Chord Builder
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Build chords visually with our intuitive interface. Select tones, semitones, and inversions 
                        to create any chord progression imaginable.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        Voice Leading Optimization
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Our intelligent algorithm suggests optimal inversions for smooth chord transitions, 
                        helping you create professional-sounding progressions.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        Blue Note Detection
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Automatically highlight tension notes and harmonic relationships within your progressions 
                        to deepen your understanding of music theory.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        Interactive Learning Hub
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Structured lessons, quizzes, and exercises guide you from chord basics to advanced 
                        progressions with gamified achievements.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        Save & Share Progressions
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Save your favorite chord progressions, organize them into sets, and share them with 
                        fellow musicians or students.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        Print-Ready Chord Sheets
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Generate beautiful, print-optimized chord sheets for practice sessions, teaching, 
                        or sharing with band members.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-zinc-800">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Start Playing in Minutes
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-400">
                    ChordHound makes learning piano chords simple and enjoyable
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                        1
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        Choose Your Chords
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Select from major, minor, diminished, or augmented chords. Pick your root note and ChordHound 
                        instantly shows you the keys to play.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                        2
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        Build Progressions
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Create up to 4-chord progressions with automatic voice leading suggestions. See how chords 
                        flow smoothly from one to the next.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                        3
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        Learn & Practice
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Save your progressions, print chord sheets, and access our Learning Hub for structured 
                        lessons that build your skills step by step.
                    </p>
                </div>
            </div>

            <div class="mt-16 text-center">
                <a href="{{ route('chords.index') }}" class="inline-flex items-center justify-center px-8 py-4 text-lg font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition">
                    Try ChordHound Now - It's Free!
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-20 px-4 sm:px-6 lg:px-8 bg-white dark:bg-zinc-900">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Loved by Musicians Worldwide
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-400">
                    Join thousands of musicians who've transformed their chord knowledge
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-8">
                    <div class="flex mb-4">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        "ChordHound transformed how I teach piano. The visual chord builder helps my students 
                        understand inversions instantly. The dog theme keeps younger students engaged!"
                    </p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-200 rounded-full flex items-center justify-center mr-4">
                            <span class="text-orange-700 font-semibold">ST</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Sarah Thompson</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Piano Teacher</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-8">
                    <div class="flex mb-4">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        "As a self-taught musician, ChordHound's voice leading feature was a game-changer. 
                        I finally understand why certain progressions sound so smooth!"
                    </p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-200 rounded-full flex items-center justify-center mr-4">
                            <span class="text-purple-700 font-semibold">MR</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Marcus Rodriguez</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Songwriter</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-8">
                    <div class="flex mb-4">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        "The Learning Hub is fantastic! I completed the fundamentals module in a week and 
                        now I'm creating my own progressions. Love the achievement badges!"
                    </p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center mr-4">
                            <span class="text-blue-700 font-semibold">EK</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Emma Kim</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Music Student</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-orange-600 to-purple-600">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl font-bold text-white mb-6">
                Ready to Unleash Your Musical Potential?
            </h2>
            <p class="text-xl text-orange-100 mb-8">
                Join ChordHound today and start your journey to chord mastery. 
                No credit card required, free forever.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 text-lg font-medium text-orange-600 bg-white rounded-lg hover:bg-gray-100 transition">
                    Get Started Free
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
                <a href="{{ route('chords.index') }}" class="inline-flex items-center justify-center px-8 py-4 text-lg font-medium text-white border-2 border-white rounded-lg hover:bg-white hover:text-orange-600 transition">
                    Try Without Account
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-zinc-950 text-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <x-app-logo size="small" />
                    </div>
                    <p class="text-gray-400">
                        Making piano chord learning fun and accessible for everyone.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('chords.index') }}" class="hover:text-white transition">Chord Generator</a></li>
                        <li><a href="{{ route('learning.index') }}" class="hover:text-white transition">Learning Hub</a></li>
                        <li><a href="#features" class="hover:text-white transition">Features</a></li>
                        <li><a href="#testimonials" class="hover:text-white transition">Testimonials</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact Us</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Connect</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Twitter</a></li>
                        <li><a href="#" class="hover:text-white transition">YouTube</a></li>
                        <li><a href="#" class="hover:text-white transition">Discord</a></li>
                        <li><a href="{{ route('donate') }}" class="hover:text-white transition">Support Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-800 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} ChordHound. All rights reserved. Made with 🐕 and ❤️</p>
            </div>
        </div>
    </footer>

    {{-- @fluxScripts --}}
</body>
</html>
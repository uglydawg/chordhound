<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Piano Chords') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-black dark:bg-black">
    <nav class="bg-zinc-900 border-b border-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/" class="text-xl font-semibold text-primary flex items-center space-x-2">
                            <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                            </svg>
                            <span>Chord Studio</span>
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('chords.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('chords.*') ? 'text-primary border-b-2 border-blue-500' : 'text-secondary hover:text-primary' }}">
                            Chords
                        </a>
                        @auth
                            <a href="{{ route('chords.my-sets') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('chords.my-sets') ? 'text-primary border-b-2 border-blue-500' : 'text-secondary hover:text-primary' }}">
                                My Sets
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-sm text-secondary hover:text-primary">Login</a>
                    @else
                        <span class="text-sm text-secondary">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-secondary hover:text-primary">
                                Logout
                            </button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </nav>


    <main>
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
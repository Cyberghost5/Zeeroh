<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('seo')
    <title>@yield('title', config('app.name') . ' — Discover Events in Nigeria')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

    {{-- ── NAV ── --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="text-xl font-black text-primary-700 tracking-tight">Zeeroh</span>
                </a>

                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('events.index') }}" class="text-sm text-gray-600 hover:text-primary-600 font-medium transition-colors">Browse Events</a>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        @if(auth()->user()->isOrganizer())
                            <a href="{{ route('organizer.dashboard') }}" class="btn-secondary text-sm py-2">Dashboard</a>
                        @elseif(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn-secondary text-sm py-2">Dashboard</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn-secondary text-sm py-2">My Tickets</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-primary-600 font-medium">Log in</a>
                        <a href="{{ route('register') }}" class="btn-primary text-sm py-2 px-4">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @yield('content')

    {{-- ── FOOTER ── --}}
    <footer class="bg-primary-950 text-gray-400 py-10 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm">© {{ date('Y') }} Zeeroh. All rights reserved.</p>
        </div>
    </footer>

@stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'JobBoard') - JobBoard</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b',
                        }
                    }
                }
            }
        }
    </script>

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-gray-50 text-gray-800 antialiased">

    {{-- Navigation --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Left: Logo & Links --}}
                <div class="flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xl font-bold text-gray-900">Job<span class="text-indigo-600">Board</span></span>
                    </a>

                    <div class="hidden md:flex items-center space-x-6">
                        <a href="{{ route('home') }}"
                           class="text-sm font-medium {{ request()->routeIs('home') ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600' }} transition-colors">
                            Home
                        </a>
                        <a href="{{ route('jobs.index') }}"
                           class="text-sm font-medium {{ request()->routeIs('jobs.*') ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600' }} transition-colors">
                            Browse Jobs
                        </a>
                        <a href="{{ route('companies.index') }}"
                           class="text-sm font-medium {{ request()->routeIs('companies.*') ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600' }} transition-colors">
                            Companies
                        </a>
                    </div>
                </div>

                {{-- Right: Auth --}}
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}"
                           class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                            Register
                        </a>
                    @endguest

                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-indigo-600 focus:outline-none transition-colors">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center overflow-hidden">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                             alt="{{ auth()->user()->name }}"
                                             class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <span class="text-indigo-600 font-semibold text-sm">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                                <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                                 style="display: none;">

                                <a href="{{ route('dashboard') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                    Dashboard
                                </a>

                                <a href="{{ route('messages.index') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                    Messages
                                </a>

                                @if(auth()->user()->isSeeker())
                                    <a href="{{ route('profile.edit') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                        Profile
                                    </a>
                                @endif

                                @if(auth()->user()->isEmployer())
                                    <a href="{{ route('company.edit') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                        Company
                                    </a>
                                @endif

                                <div class="border-t border-gray-100 my-1"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth

                    {{-- Mobile menu toggle --}}
                    <button class="md:hidden p-2 text-gray-500 hover:text-gray-700" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Navigation --}}
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <div class="flex flex-col space-y-2">
                    <a href="{{ route('home') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('home') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }}">
                        Home
                    </a>
                    <a href="{{ route('jobs.index') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('jobs.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }}">
                        Browse Jobs
                    </a>
                    <a href="{{ route('companies.index') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('companies.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }}">
                        Companies
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-lg bg-green-50 border border-green-200 p-4 flex items-center space-x-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-lg bg-red-50 border border-red-200 p-4 flex items-center space-x-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-lg bg-blue-50 border border-blue-200 p-4 flex items-center space-x-3">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                {{-- Brand --}}
                <div class="col-span-1 md:col-span-2">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 mb-3">
                        <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-lg font-bold text-gray-900">Job<span class="text-indigo-600">Board</span></span>
                    </a>
                    <p class="text-sm text-gray-500 max-w-sm">
                        Connecting talented professionals with outstanding opportunities. Find your next career move today.
                    </p>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">For Job Seekers</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('jobs.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">Browse Jobs</a></li>
                        <li><a href="{{ route('companies.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">Companies</a></li>
                    </ul>
                </div>

                {{-- Company Links --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">For Employers</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('register') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">Post a Job</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-8 pt-6 flex flex-col sm:flex-row items-center justify-between">
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} JobBoard. All rights reserved.</p>
                <div class="flex space-x-6 mt-4 sm:mt-0">
                    <a href="#" class="text-sm text-gray-400 hover:text-indigo-600 transition-colors">Privacy Policy</a>
                    <a href="#" class="text-sm text-gray-400 hover:text-indigo-600 transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Alpine.js for dropdown interactions --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</body>
</html>

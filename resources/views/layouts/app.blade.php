<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'JobBoard') - JobBoard</title>

    {{--
        ============================================================
        WHAT: Poppins web font, loaded from Google Fonts.
        WHY:  The redesign mandates Poppins app-wide (300–800 weights
              so headings can be 600–700, body 400–500, small text 300).
              We use <link rel="preconnect"> + a stylesheet <link> rather
              than @import in CSS because a parallel <link> downloads
              faster (it doesn't block the CSS parser the way @import does).
        ============================================================
    --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // ============================================================
        // WHAT: Global red/crimson theme + Poppins typography for Tailwind.
        // WHY:  This is the single highest-leverage place to re-theme the
        //       whole app. The original design used an indigo `primary`
        //       palette, and most views hardcode `indigo-*` / `blue-*` /
        //       `purple-*` utility classes directly. Instead of editing
        //       dozens of Blade files, we redefine those palettes here so
        //       EVERY existing utility instantly recolors to crimson.
        //       This also enforces the spec's "no blue / no AI-blue" rule:
        //       there is no longer any blue in the system to leak through.
        // HOW:  `extend.colors` merges with Tailwind's defaults, and a
        //       same-named key (e.g. `indigo`) overrides the built-in scale.
        // ============================================================

        // Crimson scale — 500 is the brand primary (#E63946), 600 the
        // hover/darker crimson (#C41E3A), 800 the burgundy secondary
        // (#8B0000). Lighter steps give us the rose/pink accents and the
        // soft hover backgrounds the spec asks for (e.g. #FFE5E5-ish).
        const crimson = {
            50:  '#fdf2f3',
            100: '#fce7e9',
            200: '#f9cdd2',
            300: '#f3a3ac',
            400: '#ec6b7a',
            500: '#e63946', // primary  — buttons, links, active states
            600: '#c41e3a', // hover    — darker crimson
            700: '#a3182f',
            800: '#8b0000', // burgundy — secondary / danger emphasis
            900: '#6b1b1b',
            950: '#450a0a',
        };

        tailwind.config = {
            theme: {
                extend: {
                    // Poppins everywhere via the default `sans` stack.
                    fontFamily: {
                        sans: ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        // `primary` is the semantic name we prefer going forward.
                        primary: crimson,
                        // Override legacy palettes so old utility classes
                        // (text-indigo-600, bg-blue-600, from-purple-700, …)
                        // all render as crimson. Kills every stray blue/indigo.
                        indigo: crimson,
                        blue: crimson,
                        purple: crimson,
                        // Convenience aliases matching the spec's vocabulary.
                        crimson: crimson,
                        burgundy: {
                            DEFAULT: '#8b0000',
                            light:   '#a3182f',
                            dark:    '#6b1b1b',
                        },
                    },
                },
            },
        };
    </script>

    <style>
        /* Guarantee Poppins on the document body even before Tailwind's
           preflight applies, so there is never a flash of a fallback font. */
        body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }
        /* Hide Alpine elements (modals, file inputs) until Alpine initializes,
           preventing a flash of un-cloaked content on page load. App-wide. */
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
{{-- Neutral background = cream (#FAFAFA), body text = charcoal (#2C2C2C) per the red theme spec. --}}
<body class="min-h-screen flex flex-col bg-[#FAFAFA] text-[#2C2C2C] antialiased">

    {{-- Navigation --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- ============================================================
                     MOD 1: "Browse Jobs" must NOT appear in the navbar for guests.
                     Guests see the two-button home page (role selection), not a
                     navbar with Browse Jobs — this keeps the UX focused on
                     choosing a role first. So the primary nav links render ONLY
                     for authenticated users, and only those relevant to the role:
                       - Seeker   → Browse Jobs, Saved Jobs
                       - Employer → My Jobs, Applicants
                       - Admin    → Dashboard
                     The logo links to the dashboard when logged in, home for guests.
                     ============================================================ --}}
                <div class="flex items-center space-x-8">
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xl font-bold text-gray-900">Job<span class="text-primary-600">Board</span></span>
                    </a>

                    <div class="hidden md:flex items-center space-x-6">
                        @auth
                            @if(auth()->user()->isSeeker())
                                {{-- Seeker nav: Browse Jobs + Saved Jobs (MOD 7). --}}
                                <a href="{{ route('jobs.index') }}"
                                   class="text-sm font-medium {{ request()->routeIs('jobs.*') ? 'text-primary-600' : 'text-gray-600 hover:text-primary-600' }} transition-colors">
                                    Browse Jobs
                                </a>
                                <a href="{{ route('seeker.saved-jobs.index') }}"
                                   class="text-sm font-medium {{ request()->routeIs('seeker.saved-jobs.*') ? 'text-primary-600' : 'text-gray-600 hover:text-primary-600' }} transition-colors">
                                    Saved Jobs
                                </a>
                            @elseif(auth()->user()->isEmployer())
                                {{-- Employer nav: My Jobs + Applicants --}}
                                <a href="{{ route('employer.jobs.index') }}"
                                   class="text-sm font-medium {{ request()->routeIs('employer.jobs.*') ? 'text-primary-600' : 'text-gray-600 hover:text-primary-600' }} transition-colors">
                                    My Jobs
                                </a>
                                <a href="{{ route('employer.applicants.index') }}"
                                   class="text-sm font-medium {{ request()->routeIs('employer.applicants.*') ? 'text-primary-600' : 'text-gray-600 hover:text-primary-600' }} transition-colors">
                                    Applicants
                                </a>
                            @elseif(auth()->user()->isAdmin())
                                {{-- Admin keeps its dashboard entry point. --}}
                                <a href="{{ route('admin.dashboard') }}"
                                   class="text-sm font-medium {{ request()->routeIs('admin.*') ? 'text-primary-600' : 'text-gray-600 hover:text-primary-600' }} transition-colors">
                                    Dashboard
                                </a>
                            @endif
                        @endauth
                        {{-- MOD 1: no @else branch — guests get NO navbar links. --}}
                    </div>
                </div>

                {{-- Right: Auth --}}
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}"
                           class="text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                            Register
                        </a>
                    @endguest

                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-primary-600 focus:outline-none transition-colors">
                                {{-- MOD 9: No avatars/profile pictures. The profile
                                     "icon" is just the user's first initial in a
                                     red circle — no image upload anywhere. --}}
                                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                    <span class="text-primary-600 font-semibold text-sm">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>
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

                                {{-- Profile dropdown (per role). MOD 19: the
                                     "Messages" link is gone (messaging removed).
                                     MOD 7: seekers get a "Saved Jobs" entry. --}}
                                <a href="{{ route('dashboard') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600">
                                    Dashboard
                                </a>

                                @if(auth()->user()->isSeeker())
                                    <a href="{{ route('seeker.saved-jobs.index') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600">
                                        Saved Jobs
                                    </a>
                                    <a href="{{ route('seeker.profile.edit') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600">
                                        Edit Profile
                                    </a>
                                @endif

                                @if(auth()->user()->isEmployer())
                                    <a href="{{ route('employer.company.edit') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600">
                                        Edit Company
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

            {{-- Mobile Navigation — mirrors the role-based desktop links. --}}
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <div class="flex flex-col space-y-2">
                    {{-- MOD 1: guests get no mobile nav links either. --}}
                    @auth
                        @if(auth()->user()->isSeeker())
                            <a href="{{ route('jobs.index') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('jobs.*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' }}">
                                Browse Jobs
                            </a>
                            <a href="{{ route('seeker.saved-jobs.index') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('seeker.saved-jobs.*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' }}">
                                Saved Jobs
                            </a>
                            <a href="{{ route('seeker.applications.index') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('seeker.applications.*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' }}">
                                My Applications
                            </a>
                        @elseif(auth()->user()->isEmployer())
                            <a href="{{ route('employer.jobs.index') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('employer.jobs.*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' }}">
                                My Jobs
                            </a>
                            <a href="{{ route('employer.applicants.index') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('employer.applicants.*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' }}">
                                Applicants
                            </a>
                        @elseif(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.*') ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' }}">
                                Dashboard
                            </a>
                        @endif
                    @endauth
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

    {{-- Info flash uses NEUTRAL gray (not blue): blue is now mapped to crimson
         globally, and a red-tinted "info" banner would be mistaken for an error. --}}
    @if(session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-lg bg-gray-50 border border-gray-200 p-4 flex items-center space-x-3">
                <svg class="w-5 h-5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-gray-800">{{ session('info') }}</p>
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
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center space-x-2 mb-3">
                        <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-lg font-bold text-gray-900">Job<span class="text-primary-600">Board</span></span>
                    </a>
                    <p class="text-sm text-gray-500 max-w-sm">
                        Connecting talented professionals with outstanding opportunities. Find your next career move today.
                    </p>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">For Job Seekers</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('jobs.index') }}" class="text-sm text-gray-500 hover:text-primary-600 transition-colors">Browse Jobs</a></li>
                        {{-- Companies footer link removed (feature retired from UI). --}}
                    </ul>
                </div>

                {{-- Company Links --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">For Employers</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('register') }}" class="text-sm text-gray-500 hover:text-primary-600 transition-colors">Post a Job</a></li>
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

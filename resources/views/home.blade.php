@extends('layouts.app')

@section('title', 'Welcome')

{{--
    ============================================================
    WHAT: Guest landing page. A clean, minimal hero with the app
          name, a short tagline, and two large equal-sized buttons:
          "Looking for a Job" and "Looking for Employees".
          Both buttons send the visitor to /login.
    WHY:  The redesign removes the old "hotel-booking-style" search
          bar and the Companies/featured/stats sections. For a guest,
          the only decision that matters is *which side of the
          marketplace they are on*, and both paths start with
          authentication. So we present exactly two choices and route
          both to login (registration is reachable from there).
    NOTE: Logged-in users never reach this page — HomeController
          redirects them straight to their role dashboard. So this
          template only ever renders for guests.
    ============================================================
--}}

@section('content')
    <section class="flex-1 flex items-center justify-center px-4 py-20 sm:py-28">
        <div class="w-full max-w-3xl mx-auto text-center">

            {{-- App name / logo. Poppins 700, crimson accent on "Board". --}}
            <div class="flex items-center justify-center space-x-3 mb-6">
                <svg class="w-12 h-12 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="text-4xl font-bold tracking-tight text-gray-900">Job<span class="text-primary-600">Board</span></span>
            </div>

            {{-- Tagline: heading weight 600–700, charcoal. --}}
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 leading-tight">
                Connect Talent with Employers
            </h1>
            <p class="mt-4 text-base sm:text-lg font-light text-gray-600 max-w-xl mx-auto">
                Find your next opportunity, or your next great hire. Choose how you'd like to get started.
            </p>

            {{--
                Two large, equal-sized call-to-action buttons.
                - Layout: side-by-side on >= sm, stacked on mobile (flex-col → flex-row).
                - Size: min-w 220px, min-h 64px (spec: >=200px wide, >=60px tall).
                - Style: crimson background, white text, Poppins 600.
                - Hover: darken to crimson-700 with a smooth 300ms transition.
                - Both buttons route to /login (the single entry point for both roles).
            --}}
            {{-- MOD 20: Emojis (🔍 / 🏢) replaced with inline SVG icons for a
                 professional, consistent look that scales cleanly and inherits
                 the button's text color. --}}
            <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                <a href="{{ route('login') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center min-w-[220px] min-h-[64px] px-8 py-4
                          text-base font-semibold text-white bg-primary-600 rounded-xl shadow-sm
                          hover:bg-primary-700 transition-colors duration-300">
                    {{-- Magnifying-glass icon --}}
                    <svg class="w-5 h-5 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    Looking for a Job
                </a>

                <a href="{{ route('login') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center min-w-[220px] min-h-[64px] px-8 py-4
                          text-base font-semibold text-white bg-primary-600 rounded-xl shadow-sm
                          hover:bg-primary-700 transition-colors duration-300">
                    {{-- Office-building icon --}}
                    <svg class="w-5 h-5 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01"/></svg>
                    Looking for Employees
                </a>
            </div>

            {{-- Secondary hint: a no-account visitor can register from the login page. --}}
            <p class="mt-8 text-sm font-light text-gray-500">
                New here?
                <a href="{{ route('register') }}" class="font-medium text-primary-600 hover:text-primary-700 transition-colors">
                    Create an account
                </a>
            </p>
        </div>
    </section>
@endsection

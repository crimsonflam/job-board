@extends('layouts.app')

@section('title', 'Saved Jobs')

{{--
    ============================================================
    MOD 7: Saved Jobs page. Same layout + same filters as Browse Jobs,
    but only showing jobs the seeker has bookmarked. Each card's heart is
    already filled; clicking it unsaves (and the job drops off this list on
    the next load). Empty state nudges the user back to Browse Jobs.
    ============================================================
--}}

@php
    $activeFilterCount = collect(['search', 'type', 'location', 'education', 'experience', 'salary_min', 'salary_max'])
        ->filter(fn ($k) => filled(request($k)))->count();
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ filtersOpen: false }">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Saved Jobs</h1>
        <p class="mt-1 text-gray-500 text-sm">
            {{ $jobs->total() }} saved {{ Str::plural('job', $jobs->total()) }}
        </p>
    </div>

    {{-- Mobile filter toggle --}}
    <div class="lg:hidden mb-4">
        <button type="button" @click="filtersOpen = !filtersOpen"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4.5h18M6 12h12M10 19.5h4"/></svg>
            Filters
            @if($activeFilterCount > 0)
                <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold bg-primary-600 text-white">{{ $activeFilterCount }}</span>
            @endif
        </button>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <aside class="w-full lg:w-72 flex-shrink-0 lg:block" :class="filtersOpen ? 'block' : 'hidden'">
            {{-- Same shared filter sidebar, pointing at this route. --}}
            @include('jobs._filters', ['action' => route('seeker.saved-jobs.index')])
        </aside>

        <div class="flex-1 min-w-0">
            @if($jobs->count())
                <div class="space-y-4">
                    @foreach($jobs as $job)
                        @include('components.job-card', ['job' => $job])
                    @endforeach
                </div>
                <div class="mt-8">{{ $jobs->withQueryString()->links() }}</div>
            @else
                {{-- Distinguish "no saved jobs at all" from "filters hid them". --}}
                <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                    @if($activeFilterCount > 0)
                        <h3 class="text-lg font-semibold text-gray-900">No saved jobs match your filters</h3>
                        <a href="{{ route('seeker.saved-jobs.index') }}" class="inline-block mt-4 text-sm text-primary-600 hover:text-primary-700 font-medium">Clear filters</a>
                    @else
                        <h3 class="text-lg font-semibold text-gray-900">You haven't saved any jobs yet.</h3>
                        <a href="{{ route('jobs.index') }}" class="inline-block mt-4 px-5 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Browse Jobs</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

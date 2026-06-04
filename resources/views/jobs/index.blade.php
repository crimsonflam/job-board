@extends('layouts.app')

@section('title', 'Browse Jobs')

{{--
    ============================================================
    WHAT: Browse Jobs — job list with a search + filter sidebar.
    WHY:  Server-side filtering (JobListing::scopeFilter) keeps results
          paginated and fast. The filter sidebar is a shared partial
          (jobs/_filters) reused by the Saved Jobs page.
    MOD 4: No posted-date filter and no sort control (deadline sort was
           removed with deadlines) — all active jobs show, newest first.
    ============================================================
--}}

@php
    $activeFilterCount = collect(['search', 'type', 'location', 'education', 'experience', 'salary_min', 'salary_max'])
        ->filter(fn ($k) => filled(request($k)))->count();
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ filtersOpen: false }">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Browse Jobs</h1>
        <p class="mt-1 text-gray-500 text-sm">
            Showing {{ $jobs->count() }} {{ Str::plural('job', $jobs->count()) }} out of {{ $jobs->total() }}
        </p>
    </div>

    {{-- Mobile: collapse filters into an expandable panel. --}}
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
        {{-- Filter sidebar (shared partial). --}}
        <aside class="w-full lg:w-72 flex-shrink-0 lg:block" :class="filtersOpen ? 'block' : 'hidden'">
            @include('jobs._filters', ['action' => route('jobs.index')])
        </aside>

        {{-- Results --}}
        <div class="flex-1 min-w-0">
            @if($jobs->count())
                <div class="space-y-4">
                    @foreach($jobs as $job)
                        @include('components.job-card', ['job' => $job])
                    @endforeach
                </div>
                <div class="mt-8">{{ $jobs->withQueryString()->links() }}</div>
            @else
                {{-- MOD: no-results state. --}}
                <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <h3 class="text-lg font-semibold text-gray-900">No jobs match your criteria</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters.</p>
                    <a href="{{ route('jobs.index') }}" class="inline-block mt-4 text-sm text-primary-600 hover:text-primary-700 font-medium">Clear all filters</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Browse Jobs')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Page header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Browse Jobs</h1>
        <p class="mt-1 text-gray-500 text-sm">
            @if(isset($jobs))
                {{ $jobs->total() }} {{ Str::plural('job', $jobs->total()) }} found
            @endif
        </p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Left sidebar filters --}}
        <aside class="w-full lg:w-72 flex-shrink-0">
            <form action="{{ route('jobs.index') }}" method="GET" class="space-y-6">
                {{-- Search --}}
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Search</h3>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Keyword or title"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                {{-- Category --}}
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Category</h3>
                    <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Categories</option>
                        @if(isset($categories))
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Job Type --}}
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Job Type</h3>
                    <div class="space-y-2">
                        @foreach(['full-time' => 'Full Time', 'part-time' => 'Part Time', 'contract' => 'Contract', 'freelance' => 'Freelance', 'internship' => 'Internship'] as $value => $label)
                            <label class="flex items-center">
                                <input type="checkbox" name="type[]" value="{{ $value }}"
                                    {{ in_array($value, (array) request('type', [])) ? 'checked' : '' }}
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Remote --}}
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Remote</h3>
                    <label class="flex items-center">
                        <input type="checkbox" name="remote" value="1" {{ request('remote') ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-600">Remote only</span>
                    </label>
                </div>

                {{-- Experience Level --}}
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Experience Level</h3>
                    <select name="experience" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Any Level</option>
                        @foreach(['entry' => 'Entry Level', 'mid' => 'Mid Level', 'senior' => 'Senior', 'lead' => 'Lead', 'executive' => 'Executive'] as $value => $label)
                            <option value="{{ $value }}" {{ request('experience') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Salary Range --}}
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Salary Range</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-gray-500">Min Salary</label>
                            <input type="number" name="salary_min" value="{{ request('salary_min') }}" placeholder="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Max Salary</label>
                            <input type="number" name="salary_max" value="{{ request('salary_max') }}" placeholder="Any"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition text-sm">
                    Apply Filters
                </button>

                @if(request()->hasAny(['search', 'category', 'type', 'remote', 'experience', 'salary_min', 'salary_max']))
                    <a href="{{ route('jobs.index') }}" class="block text-center text-sm text-gray-500 hover:text-gray-700">Clear all filters</a>
                @endif
            </form>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 min-w-0">
            {{-- Sort --}}
            <div class="flex items-center justify-between mb-6">
                <div class="text-sm text-gray-500">
                    Showing {{ isset($jobs) ? $jobs->firstItem() . '-' . $jobs->lastItem() . ' of ' . $jobs->total() : '0' }} results
                </div>
                <form action="{{ route('jobs.index') }}" method="GET" class="flex items-center space-x-2">
                    {{-- Preserve existing filters --}}
                    @foreach(request()->except('sort', 'page') as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <label for="sort" class="text-sm text-gray-600">Sort by:</label>
                    <select name="sort" id="sort" onchange="this.form.submit()"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="relevance" {{ request('sort') === 'relevance' ? 'selected' : '' }}>Relevance</option>
                        <option value="date" {{ request('sort') === 'date' ? 'selected' : '' }}>Date Posted</option>
                        <option value="salary" {{ request('sort') === 'salary' ? 'selected' : '' }}>Salary</option>
                    </select>
                </form>
            </div>

            {{-- Job cards --}}
            @if(isset($jobs) && $jobs->count())
                <div class="space-y-4">
                    @foreach($jobs as $job)
                        @include('components.job-card', ['job' => $job])
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $jobs->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <h3 class="text-lg font-medium text-gray-900">No jobs found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                    <a href="{{ route('jobs.index') }}" class="inline-block mt-4 text-sm text-indigo-600 hover:text-indigo-500 font-medium">Clear filters</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

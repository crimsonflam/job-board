@extends('layouts.app')

@section('title', 'Find Your Dream Job')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight">Find Your Dream Job</h1>
                <p class="mt-4 text-lg text-indigo-100">Search through thousands of jobs from top companies and find the perfect match for your career.</p>

                {{-- Search bar --}}
                <form action="{{ route('jobs.index') }}" method="GET" class="mt-10">
                    <div class="flex flex-col sm:flex-row bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="flex-1 flex items-center px-4">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" placeholder="Job title, keyword, or company"
                                class="w-full py-4 px-3 text-gray-700 text-sm focus:outline-none" value="{{ request('search') }}">
                        </div>
                        <div class="flex items-center border-t sm:border-t-0 sm:border-l border-gray-200 px-4">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <input type="text" name="location" placeholder="City or remote"
                                class="w-full sm:w-48 py-4 px-3 text-gray-700 text-sm focus:outline-none" value="{{ request('location') }}">
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 text-sm font-medium transition">
                            Search Jobs
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- Stats row --}}
    <section class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-3 gap-8 text-center">
                <div>
                    <div class="text-3xl font-bold text-indigo-600">{{ number_format($activeJobsCount ?? 0) }}</div>
                    <div class="mt-1 text-sm text-gray-500">Active Jobs</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-indigo-600">{{ number_format($companiesCount ?? 0) }}</div>
                    <div class="mt-1 text-sm text-gray-500">Companies</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-indigo-600">{{ number_format($categoriesCount ?? 0) }}</div>
                    <div class="mt-1 text-sm text-gray-500">Categories</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Jobs --}}
    @if(isset($featuredJobs) && $featuredJobs->count())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Featured Jobs</h2>
                <p class="mt-1 text-gray-500 text-sm">Hand-picked opportunities from top employers</p>
            </div>
            <a href="{{ route('jobs.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">View all jobs &rarr;</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredJobs as $job)
                @include('components.job-card', ['job' => $job])
            @endforeach
        </div>
    </section>
    @endif

    {{-- Latest Jobs --}}
    @if(isset($latestJobs) && $latestJobs->count())
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Latest Jobs</h2>
                    <p class="mt-1 text-gray-500 text-sm">The most recent opportunities posted on our platform</p>
                </div>
                <a href="{{ route('jobs.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">View all jobs &rarr;</a>
            </div>
            <div class="space-y-4">
                @foreach($latestJobs as $job)
                    @include('components.job-card', ['job' => $job])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Browse by Category --}}
    @if(isset($categories) && $categories->count())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-gray-900">Browse by Category</h2>
            <p class="mt-1 text-gray-500 text-sm">Explore jobs across different industries and fields</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('jobs.index', ['category' => $category->id]) }}"
                    class="bg-white border border-gray-200 rounded-xl p-6 text-center hover:shadow-md hover:border-indigo-200 transition group">
                    @if($category->icon)
                        <div class="text-3xl mb-3">{{ $category->icon }}</div>
                    @else
                        <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        </div>
                    @endif
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600 transition">{{ $category->name }}</h3>
                    <p class="mt-1 text-xs text-gray-500">{{ $category->job_listings_count ?? 0 }} {{ Str::plural('job', $category->job_listings_count ?? 0) }}</p>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Top Companies --}}
    @if(isset($topCompanies) && $topCompanies->count())
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-10">
                <h2 class="text-2xl font-bold text-gray-900">Top Companies</h2>
                <p class="mt-1 text-gray-500 text-sm">Leading employers on our platform</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @foreach($topCompanies as $company)
                    <a href="{{ route('companies.show', $company->slug) }}"
                        class="bg-gray-50 border border-gray-200 rounded-xl p-6 flex flex-col items-center hover:shadow-md hover:border-indigo-200 transition group">
                        <div class="w-16 h-16 bg-white rounded-lg flex items-center justify-center overflow-hidden border border-gray-100 mb-3">
                            @if($company->logo)
                                <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-xl font-bold text-gray-400">{{ strtoupper(substr($company->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600 transition text-center">{{ $company->name }}</h3>
                        <p class="mt-1 text-xs text-gray-500">{{ $company->job_listings_count ?? 0 }} {{ Str::plural('job', $company->job_listings_count ?? 0) }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection

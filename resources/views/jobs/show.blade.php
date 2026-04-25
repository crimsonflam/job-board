@extends('layouts.app')

@section('title', $job->title . ' at ' . ($job->company->name ?? 'Unknown'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6 text-sm text-gray-500">
        <a href="{{ route('jobs.index') }}" class="hover:text-indigo-600">Jobs</a>
        <span class="mx-2">/</span>
        @if($job->category)
            <a href="{{ route('jobs.index', ['category' => $job->category->id]) }}" class="hover:text-indigo-600">{{ $job->category->name }}</a>
            <span class="mx-2">/</span>
        @endif
        <span class="text-gray-700">{{ $job->title }}</span>
    </nav>

    {{-- Company banner --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-8">
        @if($job->company && $job->company->banner)
            <div class="h-40 bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $job->company->banner) }}')"></div>
        @else
            <div class="h-40 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
        @endif
        <div class="p-6 -mt-10 relative">
            <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-5">
                <div class="flex-shrink-0 w-20 h-20 bg-white rounded-xl border-4 border-white shadow-sm flex items-center justify-center overflow-hidden">
                    @if($job->company && $job->company->logo)
                        <img src="{{ asset('storage/' . $job->company->logo) }}" alt="{{ $job->company->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-2xl font-bold text-gray-400">{{ strtoupper(substr($job->company->name ?? 'J', 0, 2)) }}</span>
                    @endif
                </div>
                <div class="mt-4 sm:mt-0 sm:pb-1">
                    @if($job->company)
                        <a href="{{ route('companies.show', $job->company->slug) }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                            {{ $job->company->name }}
                            @if($job->company->is_verified)
                                <svg class="inline w-4 h-4 text-blue-500 ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a.75.75 0 00-1.06-1.06L9.793 10.5 8.354 9.06a.75.75 0 00-1.06 1.06l2 2a.75.75 0 001.06 0l3.353-3.353z" clip-rule="evenodd"/></svg>
                            @endif
                        </a>
                    @endif
                    <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $job->title }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Main content --}}
        <div class="flex-1 min-w-0 space-y-8">
            {{-- Meta info --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="flex flex-wrap gap-4 text-sm">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 font-medium">
                        {{ ucfirst(str_replace('-', ' ', $job->type)) }}
                    </span>
                    @if($job->is_remote)
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-50 text-green-700 font-medium">Remote</span>
                    @endif
                    <span class="inline-flex items-center text-gray-600">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        {{ $job->location ?? 'Not specified' }}
                    </span>
                    <span class="inline-flex items-center text-gray-600">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $job->salaryRange() }}
                    </span>
                    @if($job->experience_level)
                        <span class="inline-flex items-center text-gray-600">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg>
                            {{ ucfirst($job->experience_level) }} Level
                        </span>
                    @endif
                    <span class="inline-flex items-center text-gray-600">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        Posted {{ $job->published_at?->diffForHumans() ?? 'Recently' }}
                    </span>
                    @if($job->expires_at)
                        <span class="inline-flex items-center text-gray-600">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Expires {{ $job->expires_at->format('M d, Y') }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Description --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Job Description</h2>
                <div class="prose prose-sm max-w-none text-gray-600">
                    {!! $job->description !!}
                </div>
            </div>

            {{-- Requirements --}}
            @if($job->requirements)
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Requirements</h2>
                    <div class="prose prose-sm max-w-none text-gray-600">
                        {!! $job->requirements !!}
                    </div>
                </div>
            @endif

            {{-- Benefits --}}
            @if($job->benefits)
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Benefits</h2>
                    <div class="prose prose-sm max-w-none text-gray-600">
                        {!! $job->benefits !!}
                    </div>
                </div>
            @endif

            {{-- Skills --}}
            @if($job->skills && count($job->skills))
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Skills</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($job->skills as $skill)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 text-sm font-medium">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <aside class="w-full lg:w-80 flex-shrink-0 space-y-6">
            {{-- Action buttons --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-3">
                @auth
                    @if(auth()->user()->isSeeker())
                        @if(auth()->user()->hasApplied($job->id))
                            <div class="w-full py-2.5 px-4 bg-green-50 text-green-700 font-medium rounded-lg text-sm text-center">
                                Already Applied
                            </div>
                        @else
                            <a href="{{ route('seeker.applications.create', $job) }}"
                                class="block w-full py-2.5 px-4 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition text-sm text-center">
                                Apply Now
                            </a>
                        @endif

                        <form action="{{ route('seeker.saved-jobs.toggle', $job) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full py-2.5 px-4 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm flex items-center justify-center">
                                @if(auth()->user()->hasSavedJob($job->id))
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>
                                    Saved
                                @else
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                                    Save Job
                                @endif
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                        class="block w-full py-2.5 px-4 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition text-sm text-center">
                        Sign in to Apply
                    </a>
                @endauth
            </div>

            {{-- Company info card --}}
            @if($job->company)
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">About the Company</h3>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                            @if($job->company->logo)
                                <img src="{{ asset('storage/' . $job->company->logo) }}" alt="{{ $job->company->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-lg font-bold text-gray-400">{{ strtoupper(substr($job->company->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('companies.show', $job->company->slug) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                {{ $job->company->name }}
                            </a>
                            @if($job->company->industry)
                                <p class="text-xs text-gray-500">{{ $job->company->industry }}</p>
                            @endif
                        </div>
                    </div>
                    @if($job->company->location)
                        <p class="text-sm text-gray-500 mb-2">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            {{ $job->company->location }}
                        </p>
                    @endif
                    @if($job->company->size)
                        <p class="text-sm text-gray-500 mb-2">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            {{ $job->company->size }} employees
                        </p>
                    @endif
                    <a href="{{ route('companies.show', $job->company->slug) }}"
                        class="block mt-4 text-center text-sm text-indigo-600 font-medium hover:text-indigo-500">
                        View Company Profile
                    </a>
                </div>
            @endif

            {{-- Related jobs --}}
            @if(isset($relatedJobs) && $relatedJobs->count())
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Related Jobs</h3>
                    <div class="space-y-4">
                        @foreach($relatedJobs as $related)
                            <a href="{{ route('jobs.show', $related->slug) }}" class="block group">
                                <h4 class="text-sm font-medium text-gray-900 group-hover:text-indigo-600 transition">{{ $related->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $related->company->name ?? 'Unknown' }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-gray-400">{{ $related->location ?? 'Not specified' }}</span>
                                    <span class="text-xs text-gray-400">&middot;</span>
                                    <span class="text-xs text-gray-400">{{ $related->salaryRange() }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </aside>
    </div>
</div>
@endsection

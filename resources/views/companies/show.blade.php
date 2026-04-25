@extends('layouts.app')

@section('title', $company->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Banner --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-8">
        @if($company->banner)
            <div class="h-48 bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $company->banner) }}')"></div>
        @else
            <div class="h-48 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
        @endif
        <div class="p-6 -mt-12 relative">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between">
                <div class="flex items-end space-x-5">
                    <div class="flex-shrink-0 w-24 h-24 bg-white rounded-xl border-4 border-white shadow-sm flex items-center justify-center overflow-hidden">
                        @if($company->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl font-bold text-gray-400">{{ strtoupper(substr($company->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <div class="pb-1">
                        <div class="flex items-center">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $company->name }}</h1>
                            @if($company->is_verified)
                                <svg class="w-5 h-5 text-blue-500 ml-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a.75.75 0 00-1.06-1.06L9.793 10.5 8.354 9.06a.75.75 0 00-1.06 1.06l2 2a.75.75 0 001.06 0l3.353-3.353z" clip-rule="evenodd"/></svg>
                            @endif
                        </div>
                        @if($company->industry)
                            <p class="text-sm text-gray-500 mt-0.5">{{ $company->industry }}</p>
                        @endif
                    </div>
                </div>
                @if($company->website)
                    <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer"
                        class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                        Visit Website
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Main content --}}
        <div class="flex-1 min-w-0 space-y-8">
            {{-- About --}}
            @if($company->description)
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">About {{ $company->name }}</h2>
                    <div class="prose prose-sm max-w-none text-gray-600">
                        {!! $company->description !!}
                    </div>
                </div>
            @endif

            {{-- Active Jobs --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">
                    Open Positions
                    @if(isset($jobs))
                        <span class="text-sm font-normal text-gray-500">({{ $jobs->total() }})</span>
                    @endif
                </h2>

                @if(isset($jobs) && $jobs->count())
                    <div class="space-y-4">
                        @foreach($jobs as $job)
                            @include('components.job-card', ['job' => $job])
                        @endforeach
                    </div>

                    @if($jobs->hasPages())
                        <div class="mt-6">
                            {{ $jobs->links() }}
                        </div>
                    @endif
                @else
                    <p class="text-sm text-gray-500 text-center py-8">No open positions at the moment.</p>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <aside class="w-full lg:w-80 flex-shrink-0">
            <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-4">
                <h3 class="text-sm font-semibold text-gray-900">Company Details</h3>

                @if($company->location)
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        <div>
                            <p class="text-xs text-gray-400">Location</p>
                            <p class="text-sm text-gray-700">{{ $company->location }}</p>
                        </div>
                    </div>
                @endif

                @if($company->industry)
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        <div>
                            <p class="text-xs text-gray-400">Industry</p>
                            <p class="text-sm text-gray-700">{{ $company->industry }}</p>
                        </div>
                    </div>
                @endif

                @if($company->size)
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                        <div>
                            <p class="text-xs text-gray-400">Company Size</p>
                            <p class="text-sm text-gray-700">{{ $company->size }} employees</p>
                        </div>
                    </div>
                @endif

                @if($company->website)
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                        <div>
                            <p class="text-xs text-gray-400">Website</p>
                            <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer" class="text-sm text-indigo-600 hover:text-indigo-500">
                                {{ parse_url($company->website, PHP_URL_HOST) ?? $company->website }}
                            </a>
                        </div>
                    </div>
                @endif

                @if($company->email)
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        <div>
                            <p class="text-xs text-gray-400">Email</p>
                            <p class="text-sm text-gray-700">{{ $company->email }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </aside>
    </div>
</div>
@endsection

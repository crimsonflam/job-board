@extends('layouts.app')

@section('title', 'Companies')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Page header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Companies</h1>
        <p class="mt-1 text-gray-500 text-sm">Discover great companies that are hiring</p>
    </div>

    {{-- Company cards grid --}}
    @if(isset($companies) && $companies->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($companies as $company)
                <a href="{{ route('companies.show', $company->slug) }}"
                    class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md hover:border-indigo-200 transition group">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center overflow-hidden">
                            @if($company->logo)
                                <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-lg font-bold text-gray-400">{{ strtoupper(substr($company->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center">
                                <h2 class="text-base font-semibold text-gray-900 group-hover:text-indigo-600 transition truncate">{{ $company->name }}</h2>
                                @if($company->is_verified)
                                    <svg class="w-4 h-4 text-blue-500 ml-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a.75.75 0 00-1.06-1.06L9.793 10.5 8.354 9.06a.75.75 0 00-1.06 1.06l2 2a.75.75 0 001.06 0l3.353-3.353z" clip-rule="evenodd"/></svg>
                                @endif
                            </div>
                            @if($company->industry)
                                <p class="text-sm text-gray-500 mt-0.5">{{ $company->industry }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-gray-500">
                        @if($company->location)
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                {{ $company->location }}
                            </span>
                        @endif
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                            {{ $company->job_listings_count ?? 0 }} {{ Str::plural('job', $company->job_listings_count ?? 0) }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($companies->hasPages())
            <div class="mt-8">
                {{ $companies->links() }}
            </div>
        @endif
    @else
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
            <h3 class="text-lg font-medium text-gray-900">No companies found</h3>
            <p class="mt-1 text-sm text-gray-500">Check back later for new companies.</p>
        </div>
    @endif
</div>
@endsection

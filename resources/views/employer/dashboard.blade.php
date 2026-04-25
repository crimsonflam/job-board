@extends('layouts.app')

@section('title', 'Employer Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div class="flex items-center space-x-3">
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $company->name ?? 'My Company' }}
            </h1>
            @if($company && $company->is_verified)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Verified
                </span>
            @endif
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('employer.jobs.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Post New Job
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-sm font-medium text-gray-500">Active Jobs</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ $activeJobs }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-sm font-medium text-gray-500">Total Applications</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ $totalApplications }}</div>
        </div>
        @if($jobStats)
            @foreach($jobStats as $stat)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="text-sm font-medium text-gray-500">{{ ucfirst($stat->status) }} Jobs</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $stat->count }}</div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <a href="{{ route('employer.jobs.create') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center space-x-4 hover:border-indigo-300 hover:shadow-md transition">
            <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <div class="text-sm font-semibold text-gray-900">Post New Job</div>
                <div class="text-xs text-gray-500">Create a new listing</div>
            </div>
        </a>
        <a href="{{ route('employer.jobs.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center space-x-4 hover:border-indigo-300 hover:shadow-md transition">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5h6"/></svg>
            </div>
            <div>
                <div class="text-sm font-semibold text-gray-900">View All Jobs</div>
                <div class="text-xs text-gray-500">Manage your listings</div>
            </div>
        </a>
        <a href="{{ route('employer.company.edit') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center space-x-4 hover:border-indigo-300 hover:shadow-md transition">
            <div class="flex-shrink-0 w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
            </div>
            <div>
                <div class="text-sm font-semibold text-gray-900">Company Profile</div>
                <div class="text-xs text-gray-500">Update your company info</div>
            </div>
        </a>
    </div>

    {{-- Recent Applications --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Applications</h2>
        </div>
        @if($recentApplications->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentApplications as $application)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $application->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $application->jobListing->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'reviewing' => 'bg-blue-100 text-blue-800',
                                            'shortlisted' => 'bg-indigo-100 text-indigo-800',
                                            'interview' => 'bg-purple-100 text-purple-800',
                                            'offered' => 'bg-green-100 text-green-800',
                                            'hired' => 'bg-emerald-100 text-emerald-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                        ];
                                        $color = $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $application->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center text-gray-500 text-sm">
                No applications received yet.
            </div>
        @endif
    </div>
</div>
@endsection

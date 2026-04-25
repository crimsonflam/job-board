@extends('layouts.app')

@section('title', 'My Applications')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Applications</h1>
        <p class="mt-1 text-gray-600">Track the status of all your job applications.</p>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        {{-- Desktop Table --}}
        <div class="hidden md:block">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Applied</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($applications as $application)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $application->jobListing->title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ $application->jobListing->company->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $application->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'reviewed' => 'bg-blue-100 text-blue-800',
                                        'shortlisted' => 'bg-indigo-100 text-indigo-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'hired' => 'bg-green-100 text-green-800',
                                    ];
                                    $color = $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('seeker.applications.show', $application) }}" class="text-blue-600 hover:text-blue-800 font-medium">View Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2 text-sm">You haven't submitted any applications yet.</p>
                                <a href="{{ route('jobs.index') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-800 text-sm font-medium">Browse Available Jobs</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse ($applications as $application)
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">{{ $application->jobListing->title }}</h3>
                            <p class="text-sm text-gray-500">{{ $application->jobListing->company->name }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $application->created_at->format('M d, Y') }}</p>
                        </div>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'reviewed' => 'bg-blue-100 text-blue-800',
                                'shortlisted' => 'bg-indigo-100 text-indigo-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'hired' => 'bg-green-100 text-green-800',
                            ];
                            $color = $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                            {{ ucfirst($application->status) }}
                        </span>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('seeker.applications.show', $application) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View Details</a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <p class="text-sm">You haven't submitted any applications yet.</p>
                    <a href="{{ route('jobs.index') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-800 text-sm font-medium">Browse Available Jobs</a>
                </div>
            @endforelse
        </div>
    </div>

    @if ($applications->hasPages())
        <div class="mt-6">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection

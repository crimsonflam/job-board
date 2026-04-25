@extends('layouts.app')

@section('title', 'Manage Jobs')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Manage Jobs</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Back to Dashboard</a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.jobs.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or company..."
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2 border">
            </div>
            <div class="sm:w-48">
                <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2 border">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                Filter
            </button>
        </form>
    </div>

    {{-- Jobs Table --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Title</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Company</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Category</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Views</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Date</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jobs as $job)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ Str::limit($job->title, 35) }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $job->company->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $job->category->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @if($job->status === 'active')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                @elseif($job->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($job->status === 'closed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Closed</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ ucfirst($job->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ number_format($job->views ?? 0) }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $job->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Change Status --}}
                                    <form method="POST" action="{{ route('admin.jobs.update-status', $job) }}" class="inline-flex">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()"
                                                class="rounded-md border-gray-300 text-xs py-1 pl-2 pr-7 focus:border-indigo-500 focus:ring-indigo-500 border">
                                            <option value="active" {{ $job->status === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="pending" {{ $job->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="closed" {{ $job->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                            <option value="rejected" {{ $job->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </form>

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" class="inline-flex"
                                          onsubmit="return confirm('Are you sure you want to delete this job listing?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">No job listings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($jobs->hasPages())
        <div class="mt-6">
            {{ $jobs->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

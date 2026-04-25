@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Admin Dashboard</h1>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="text-sm font-medium text-gray-500">Total Users</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['total_users'] ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="text-sm font-medium text-gray-500">Seekers</div>
            <div class="mt-2 text-3xl font-bold text-indigo-600">{{ number_format($stats['seekers'] ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="text-sm font-medium text-gray-500">Employers</div>
            <div class="mt-2 text-3xl font-bold text-emerald-600">{{ number_format($stats['employers'] ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="text-sm font-medium text-gray-500">Total Jobs</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['total_jobs'] ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="text-sm font-medium text-gray-500">Active Jobs</div>
            <div class="mt-2 text-3xl font-bold text-green-600">{{ number_format($stats['active_jobs'] ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="text-sm font-medium text-gray-500">Total Applications</div>
            <div class="mt-2 text-3xl font-bold text-blue-600">{{ number_format($stats['total_applications'] ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="text-sm font-medium text-gray-500">Total Companies</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['total_companies'] ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="text-sm font-medium text-gray-500">Verified Companies</div>
            <div class="mt-2 text-3xl font-bold text-amber-600">{{ number_format($stats['verified_companies'] ?? 0) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Recent Job Listings --}}
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Recent Job Listings</h2>
                <a href="{{ route('admin.jobs.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Title</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Company</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentJobs as $job)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium text-gray-900">{{ Str::limit($job->title, 30) }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $job->company->name ?? 'N/A' }}</td>
                                <td class="px-6 py-3">
                                    @if($job->status === 'active')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                    @elseif($job->status === 'pending')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($job->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-500">{{ $job->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">No job listings yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Users --}}
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Recent Users</h2>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Name</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Email</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Role</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentUsers as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-3">
                                    @if($user->role === 'admin')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Admin</span>
                                    @elseif($user->role === 'employer')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Employer</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Seeker</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">No users yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Manage Companies')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Manage Companies</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Back to Dashboard</a>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.companies.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by company name or industry..."
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2 border">
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                Search
            </button>
        </form>
    </div>

    {{-- Companies Table --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Name</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Owner</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Industry</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Jobs</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Verified</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($companies as $company)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $company->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $company->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $company->industry ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $company->jobs_count ?? $company->jobs->count() }}</td>
                            <td class="px-6 py-4">
                                @if($company->is_verified)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Verified</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Unverified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.companies.toggle-verification', $company) }}" class="inline-flex">
                                    @csrf
                                    @method('PUT')
                                    @if($company->is_verified)
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-md transition">
                                            Revoke Verification
                                        </button>
                                    @else
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 border border-green-200 rounded-md transition">
                                            Verify Company
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">No companies found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($companies->hasPages())
        <div class="mt-6">
            {{ $companies->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

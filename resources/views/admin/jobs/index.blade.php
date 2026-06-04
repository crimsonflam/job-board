@extends('layouts.app')

@section('title', 'Manage Jobs')

{{--
    ============================================================
    WHAT: Admin job listings — view + delete only.
    MOD 2: The admin can VIEW jobs (title, company, status read-only) and
           DELETE them. There is NO status-change control — job status is
           owned by the employer who created the job. This prevents admins
           from accidentally activating/deactivating jobs.
    MOD 3: NO status filter. The admin sees all jobs regardless of status;
           only keyword search (title/company) and ordering remain.
    Note: views count was removed earlier (no views_count column).
    ============================================================
--}}

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Manage Jobs</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">Back to Dashboard</a>
    </div>

    {{-- MOD 3: search only — no status filter dropdown. --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.jobs.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search jobs..."
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-2 border">
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('admin.jobs.index') }}" class="inline-flex items-center px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Clear</a>
            @endif
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
                            <td class="px-6 py-4 text-gray-600">{{ $job->user->company_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $job->category->name ?? 'N/A' }}</td>
                            {{-- MOD 2: status is DISPLAY-ONLY (read-only badge). --}}
                            <td class="px-6 py-4">
                                @if($job->status === 'active')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $job->created_at->format('M d, Y') }}</td>
                            {{-- MOD 2: the ONLY action is Delete (dark red), with confirmation. --}}
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" class="inline-flex"
                                      onsubmit="return confirm('Are you sure you want to delete this job posting? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white rounded-md transition"
                                        style="background-color:#8b0000" onmouseover="this.style.backgroundColor='#6b1b1b'" onmouseout="this.style.backgroundColor='#8b0000'">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">No job listings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($jobs->hasPages())
        <div class="mt-6">
            {{ $jobs->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

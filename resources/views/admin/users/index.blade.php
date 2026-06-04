@extends('layouts.app')

@section('title', 'Manage Users')

{{--
    ============================================================
    WHAT: Admin user management — deactivate / activate (no delete).
    MOD 4: Each user shows a status badge (Active / Deactivated). Admins
           toggle status instead of deleting, so accounts/data are preserved.
    MOD 5: Role hierarchy is enforced in the UI via $viewer->canManage($user):
             - super_admin can manage everyone except another super_admin
             - normal admin can manage regular users (seeker/employer) only
             - never yourself
           When the viewer cannot manage a row, NO action button is shown
           (read-only). The server re-checks the same rule (defense in depth).
           Role badges: "Super Admin" (amber), "Admin" (red), else role text.
    ============================================================
--}}

@php $viewer = auth()->user(); @endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Manage Users</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">Back to Dashboard</a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..."
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-2 border">
            </div>
            <div class="sm:w-48">
                <select name="role" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-2 border">
                    <option value="">All Roles</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="employer" {{ request('role') === 'employer' ? 'selected' : '' }}>Employer</option>
                    <option value="seeker" {{ request('role') === 'seeker' ? 'selected' : '' }}>Seeker</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                Filter
            </button>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Name</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Email</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Role</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Joined</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                            {{-- Role badge --}}
                            <td class="px-6 py-4">
                                @if($user->role === 'super_admin')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Super Admin</span>
                                @elseif($user->role === 'admin')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Admin</span>
                                @elseif($user->role === 'employer')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Employer</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Seeker</span>
                                @endif
                            </td>
                            {{-- MOD 4: status badge --}}
                            <td class="px-6 py-4">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600">Deactivated</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                            {{-- MOD 5: action only when the viewer is allowed to manage this row. --}}
                            <td class="px-6 py-4 text-right">
                                @if($viewer->canManage($user))
                                    @if($user->status === 'active')
                                        {{-- Deactivate (red) --}}
                                        <form method="POST" action="{{ route('admin.users.deactivate', $user) }}" class="inline-flex"
                                              onsubmit="return confirm('Deactivate this user? They will no longer be able to log in.')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition">
                                                Deactivate
                                            </button>
                                        </form>
                                    @else
                                        {{-- Activate (green) --}}
                                        <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="inline-flex"
                                              onsubmit="return confirm('Activate this user? They will be able to log in.')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 hover:text-green-900 hover:bg-green-50 rounded-md transition">
                                                Activate
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    {{-- Read-only: viewer lacks permission (e.g. an admin/super-admin row,
                                         or the viewer's own account). --}}
                                    <span class="text-xs text-gray-300">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
        <div class="mt-6">
            {{ $users->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

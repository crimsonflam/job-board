@props(['title', 'value', 'icon' => null, 'color' => 'indigo'])

@php
    $colorMap = [
        'indigo' => [
            'bg'   => 'bg-indigo-50',
            'icon' => 'text-indigo-600',
            'ring' => 'ring-indigo-100',
        ],
        'blue' => [
            'bg'   => 'bg-blue-50',
            'icon' => 'text-blue-600',
            'ring' => 'ring-blue-100',
        ],
        'green' => [
            'bg'   => 'bg-green-50',
            'icon' => 'text-green-600',
            'ring' => 'ring-green-100',
        ],
        'red' => [
            'bg'   => 'bg-red-50',
            'icon' => 'text-red-600',
            'ring' => 'ring-red-100',
        ],
        'amber' => [
            'bg'   => 'bg-amber-50',
            'icon' => 'text-amber-600',
            'ring' => 'ring-amber-100',
        ],
        'purple' => [
            'bg'   => 'bg-purple-50',
            'icon' => 'text-purple-600',
            'ring' => 'ring-purple-100',
        ],
        'teal' => [
            'bg'   => 'bg-teal-50',
            'icon' => 'text-teal-600',
            'ring' => 'ring-teal-100',
        ],
    ];

    $scheme = $colorMap[$color] ?? $colorMap['indigo'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition-shadow']) }}>
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $value }}</p>
        </div>

        @if($icon)
            <div class="flex-shrink-0 p-3 rounded-lg {{ $scheme['bg'] }} ring-1 {{ $scheme['ring'] }}">
                <svg class="w-6 h-6 {{ $scheme['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @switch($icon)
                        @case('briefcase')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            @break
                        @case('users')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            @break
                        @case('eye')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            @break
                        @case('document')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            @break
                        @case('chat')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            @break
                        @case('heart')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            @break
                        @case('chart')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            @break
                        @case('check-circle')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @break
                        @default
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    @endswitch
                </svg>
            </div>
        @endif
    </div>

    @if(isset($slot) && !$slot->isEmpty())
        <div class="mt-4 text-sm text-gray-500">
            {{ $slot }}
        </div>
    @endif
</div>

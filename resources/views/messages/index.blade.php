@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Messages</h1>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        @forelse($conversations as $conversation)
            @php
                $otherUser = $conversation->sender_id === auth()->id()
                    ? $conversation->receiver
                    : $conversation->sender;
                $lastMessage = $conversation->lastMessage ?? $conversation;
            @endphp
            <a href="{{ route('messages.show', $conversation->id) }}"
               class="block px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition {{ ($conversation->unread_count ?? 0) > 0 ? 'bg-indigo-50/50' : '' }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-semibold text-gray-900 text-sm">{{ $otherUser->name ?? 'Unknown User' }}</span>
                            @if(($conversation->unread_count ?? 0) > 0)
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-600 text-white min-w-[20px]">
                                    {{ $conversation->unread_count }}
                                </span>
                            @endif
                        </div>
                        @if($conversation->job)
                            <div class="text-xs text-indigo-600 font-medium mb-1">Re: {{ Str::limit($conversation->job->title, 50) }}</div>
                        @endif
                        <p class="text-sm text-gray-500 truncate">{{ Str::limit($lastMessage->body ?? $lastMessage->last_message, 80) }}</p>
                    </div>
                    <div class="text-xs text-gray-400 whitespace-nowrap mt-1">
                        {{ ($lastMessage->created_at ?? $conversation->updated_at)->diffForHumans() }}
                    </div>
                </div>
            </a>
        @empty
            <div class="px-6 py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <p class="mt-4 text-gray-400 text-sm">No conversations yet.</p>
            </div>
        @endforelse
    </div>

    @if(method_exists($conversations, 'hasPages') && $conversations->hasPages())
        <div class="mt-6">
            {{ $conversations->links() }}
        </div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('title', 'Conversation')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('messages.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium mb-2 inline-block">&larr; Back to Messages</a>
            <h1 class="text-xl font-bold text-gray-900">
                Conversation with {{ $otherUser->name ?? 'Unknown User' }}
            </h1>
            @if($conversation->job)
                <p class="text-sm text-gray-500 mt-1">Regarding: <span class="font-medium text-indigo-600">{{ $conversation->job->title }}</span></p>
            @endif
        </div>
    </div>

    {{-- Messages --}}
    <div class="bg-white rounded-lg border border-gray-200 mb-6">
        <div class="p-6 space-y-4 max-h-[500px] overflow-y-auto" id="messages-container">
            @forelse($messages as $message)
                @php
                    $isCurrentUser = $message->sender_id === auth()->id();
                @endphp
                <div class="flex {{ $isCurrentUser ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[75%] {{ $isCurrentUser ? 'order-1' : '' }}">
                        <div class="text-xs {{ $isCurrentUser ? 'text-right' : 'text-left' }} text-gray-400 mb-1">
                            {{ $message->sender->name ?? 'Unknown' }}
                        </div>
                        <div class="rounded-lg px-4 py-3 {{ $isCurrentUser ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900' }}">
                            <p class="text-sm whitespace-pre-wrap">{{ $message->body }}</p>
                            @if($message->attachment)
                                <a href="{{ asset('storage/' . $message->attachment) }}" target="_blank"
                                   class="inline-flex items-center gap-1 mt-2 text-xs {{ $isCurrentUser ? 'text-indigo-200 hover:text-white' : 'text-indigo-600 hover:text-indigo-800' }} underline">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    View Attachment
                                </a>
                            @endif
                        </div>
                        <div class="text-xs {{ $isCurrentUser ? 'text-right' : 'text-left' }} text-gray-400 mt-1">
                            {{ $message->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400 text-sm">No messages yet. Start the conversation below.</div>
            @endforelse
        </div>
    </div>

    {{-- Reply Form --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <form method="POST" action="{{ route('messages.store', $conversation->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="body" class="sr-only">Message</label>
                <textarea name="body" id="body" rows="3" required placeholder="Type your message..."
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm border resize-none">{{ old('body') }}</textarea>
                @error('body')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <label for="attachment" class="cursor-pointer inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        <span>Attach File</span>
                    </label>
                    <input type="file" name="attachment" id="attachment" class="hidden">
                    @error('attachment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    Send Message
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }

        const fileInput = document.getElementById('attachment');
        const fileLabel = fileInput?.previousElementSibling?.querySelector('span');
        if (fileInput && fileLabel) {
            fileInput.addEventListener('change', function () {
                fileLabel.textContent = this.files[0] ? this.files[0].name : 'Attach File';
            });
        }
    });
</script>
@endpush
@endsection

<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $conversations = Conversation::where('employer_id', $userId)
            ->orWhere('seeker_id', $userId)
            ->with(['employer', 'seeker', 'application.jobListing', 'lastMessage'])
            ->latest('updated_at')
            ->paginate(20);

        return view('messages.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $conversation->messages()->with('sender')->oldest()->get();
        $conversation->load(['employer', 'seeker', 'application.jobListing']);

        return view('messages.show', compact('conversation', 'messages'));
    }

    public function store(Request $request, Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:5120'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('message-attachments', 'public');
        }

        $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'body' => $validated['body'],
            'attachment' => $attachmentPath,
        ]);

        $conversation->touch();

        return back()->with('success', 'Message sent.');
    }

    public function startConversation(Application $application)
    {
        $jobListing = $application->jobListing;
        $user = auth()->user();

        $isEmployer = $user->isEmployer() && $jobListing->company_id === $user->company?->id;
        $isSeeker = $user->isSeeker() && $application->user_id === $user->id;

        if (!$isEmployer && !$isSeeker) {
            abort(403);
        }

        $conversation = Conversation::firstOrCreate(
            ['application_id' => $application->id],
            [
                'employer_id' => $jobListing->user_id,
                'seeker_id' => $application->user_id,
            ]
        );

        return redirect()->route('messages.show', $conversation);
    }

    private function authorizeConversation(Conversation $conversation): void
    {
        $userId = auth()->id();
        if ($conversation->employer_id !== $userId && $conversation->seeker_id !== $userId) {
            abort(403);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\ForumTopic;
use App\Models\ForumReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index()
    {
        $forums = Forum::with(['topics' => function ($query) {
                $query->latest()->take(5);
            }])
            ->where('is_active', true)
            ->get();

        $recentTopics = ForumTopic::with(['forum', 'author', 'replies'])
            ->latest()
            ->take(10)
            ->get();

        return view('forums.index', compact('forums', 'recentTopics'));
    }

    public function show(Forum $forum)
    {
        $topics = $forum->topics()
            ->with(['author', 'replies'])
            ->when(request('search'), function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('forums.show', compact('forum', 'topics'));
    }

    public function createTopic(Request $request, Forum $forum)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $topic = ForumTopic::create([
            'forum_id' => $forum->id,
            'title' => $request->title,
            'content' => $request->content,
            'author_id' => Auth::id(),
        ]);

        return redirect()->route('forums.topic', $topic)
            ->with('success', 'Topic created successfully.');
    }

    public function showTopic(ForumTopic $topic)
    {
        $topic->load(['forum', 'author']);
        
        $replies = $topic->replies()
            ->with('author')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('forums.topic', compact('topic', 'replies'));
    }

    public function replyToTopic(Request $request, ForumTopic $topic)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        ForumReply::create([
            'topic_id' => $topic->id,
            'content' => $request->content,
            'author_id' => Auth::id(),
        ]);

        $topic->touch(); // Update the topic's updated_at timestamp

        return back()->with('success', 'Reply posted successfully.');
    }
}

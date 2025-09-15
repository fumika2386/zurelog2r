<?php
namespace App\Http\Controllers;

use App\Models\Topic;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::where('is_published', true)
            ->withCount('posts')        // 投稿数バッジ用
            ->latest()
            ->paginate(12);

        return view('topics.index', compact('topics'));
    }

    public function posts(Topic $topic)
    {
        $posts = $topic->posts()->with('user')->latest()->paginate(10);
        return view('topics.posts', compact('topic','posts'));
    }
}

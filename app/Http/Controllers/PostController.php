<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // 一覧（公開）
    public function index()
    {
        $posts = Post::with(['user','topic'])->latest()->paginate(10);
        return view('posts.index', compact('posts'));
    }

    // 詳細（公開）
    public function show(Post $post)
    {
        $post->load(['user','topic']);
        return view('posts.show', compact('post'));
    }

    // 作成フォーム（/posts/create?topic_id=123）
    public function create(Request $request)  // ← use Illuminate\Http\Request; を使う形でOK
    {
        $topic = Topic::find($request->query('topic_id')); // nullでもOK
        return view('posts.create', compact('topic'));
    }



    // 保存
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'    => ['required','string','max:255'],
            'body'     => ['nullable','string'],
            'topic_id' => ['nullable','exists:topics,id'],
        ]);

        $post = $request->user()->posts()->create($data);

        return redirect()->route('posts.show', $post)->with('status','created');
    }

    // 編集フォーム
    public function edit(Request $request, Post $post)
    {
        if ($post->user_id !== $request->user()->id) abort(403);
        return view('posts.edit', compact('post'));
    }

    // 更新
    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== $request->user()->id) abort(403);

        $data = $request->validate([
            'title'    => ['required','string','max:255'],
            'body'     => ['nullable','string'],
            'topic_id' => ['nullable','exists:topics,id'],
        ]);

        $post->update($data);

        return redirect()->route('posts.show', $post)->with('status','updated');
    }

    // 削除
    public function destroy(Request $request, Post $post)
    {
        if ($post->user_id !== $request->user()->id) abort(403);

        $post->delete();

        return redirect()->route('posts.index')->with('status','deleted');
    }
}


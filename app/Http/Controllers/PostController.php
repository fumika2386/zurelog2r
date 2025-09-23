<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    // 一覧（公開）
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'new'); // new|old|near|far

        // ベースクエリ
        $query = Post::with(['user','topic']);

        // 価値観ベースの並べ替え（ログイン済み + 回答がある場合のみ）
        if (in_array($sort, ['near','far'], true) && $request->user()) {
            $meId = $request->user()->id;

            // 自分の回答が1つも無ければ、価値観ソートは無効化
            $hasMyAnswers = DB::table('value_answers')->where('user_id', $meId)->exists();

            if ($hasMyAnswers) {
                // 投稿者(user_id)ごとの一致率 similarity ∈ [0,1] を算出するサブクエリ
                $simSub = DB::table('value_answers as a')
                    ->join('value_answers as b', function ($join) use ($meId) {
                        $join->on('b.question_id', '=', 'a.question_id')
                            ->where('b.user_id', '=', $meId);
                    })
                    ->select(
                        'a.user_id',
                        DB::raw('SUM(IF(a.value = b.value, 1, 0)) as matches'),
                        DB::raw('COUNT(*) as total'),
                        DB::raw('SUM(IF(a.value = b.value, 1, 0)) / COUNT(*) as similarity')
                    )
                    ->groupBy('a.user_id');

                // posts に similarity を結合して並べ替え
                $query = $query
                    ->leftJoinSub($simSub, 'sim', 'posts.user_id', '=', 'sim.user_id')
                    ->select('posts.*', DB::raw('COALESCE(sim.similarity, 0) as similarity')); // 未回答=0

                if ($sort === 'near') {
                    $query->orderByDesc('similarity')->orderByDesc('posts.created_at');
                } else { // far
                    $query->orderBy('similarity')->orderByDesc('posts.created_at');
                }
            } else {
                // 自分の回答が無い → 新着順にフォールバック
                $sort = 'new';
            }
        }

        // 時系列の並べ替え
        if ($sort === 'old') {
            $query->orderBy('posts.created_at', 'asc');
        } elseif ($sort === 'new') {
            $query->orderBy('posts.created_at', 'desc');
        }

        $posts = $query->paginate(10)->withQueryString(); // ページングしてクエリ保持

        return view('posts.index', compact('posts', 'sort'));
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
        // お題が未指定ならプルダウン用に一覧を渡す（公開中のみ）
        $topics = $topic ? collect() : Topic::where('is_published', true)
            ->orderByDesc('id')->limit(20)->get(['id','title']);

        return view('posts.create', compact('topic','topics'));
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


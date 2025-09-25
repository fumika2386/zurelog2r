<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Topic;
use App\Models\PostReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PostController extends Controller
{
    // 一覧（公開）
    public function index(Request $request)
    {
        $tagIds = collect($request->query('tags', []))
            ->map(fn($v)=>(int)$v)->filter()->unique()->values();
        $sort = $request->query('sort', 'new'); // new|old|near|far
        $uid  = optional($request->user())->id;

        // 1) ベース + スタンプ集計を常時 withCount（N+1回避）
        $stampCounts = [];
        for ($i = 0; $i <= 4; $i++) {
            $stampCounts["reactions as reactions_s{$i}"] = fn($q) => $q->where('stamp', $i);
        }

        $query = Post::query()
            ->with(['user', 'topic'])
            ->withCount(array_merge(
                $stampCounts,
                ['reactions as reactions_total' => fn($q) => $q]  // 合計
            ));
            
        if ($tagIds->isNotEmpty()) {
            $ids  = $tagIds->all();
            $need = count($ids);

            // AND（すべて含む）
            $query->whereHas(
                'user.tags',
                fn($q) => $q->whereIn('tags.id', $ids),
                '=',
                $need
            );

            // OR（いずれか含む）にしたい場合は上の塊を↓に差し替え
            // $query->whereHas('user.tags', fn($q) => $q->whereIn('tags.id', $ids));
        }

        // 2) 価値観ベースの並べ替え（ログイン + 回答がある場合のみ）
        if (in_array($sort, ['near','far'], true) && $uid) {

            $hasMyAnswers = DB::table('value_answers')->where('user_id', $uid)->exists();
            if ($hasMyAnswers) {
                // ※列名に注意：スキーマが 'answer' なら a.value/b.value を 'answer' に変更
                $simSub = DB::table('value_answers as a')
                    ->join('value_answers as b', function ($join) use ($uid) {
                        $join->on('b.question_id', '=', 'a.question_id')
                             ->where('b.user_id', '=', $uid);
                    })
                    ->select(
                        'a.user_id',
                        DB::raw('SUM(IF(a.value = b.value, 1, 0)) as matches'),
                        DB::raw('COUNT(*) as total'),
                        DB::raw('SUM(IF(a.value = b.value, 1, 0)) / COUNT(*) as similarity')
                    )
                    ->groupBy('a.user_id');

                // 既存の $query に join（← ここが大事。再代入で消さない）
                $query->leftJoinSub($simSub, 'sim', 'posts.user_id', '=', 'sim.user_id')
                      ->select('posts.*', DB::raw('COALESCE(sim.similarity, 0.5) as similarity')); // 未比較=0.5に中寄せ

                if ($sort === 'near') {
                    $query->orderByDesc('similarity')->orderByDesc('posts.created_at');
                } else { // far
                    $query->orderBy('similarity')->orderByDesc('posts.created_at');
                }
            } else {
                // 回答ゼロ → 新着にフォールバック
                $sort = 'new';
            }
        }
        

        // 3) 時系列の並べ替え（near/far で未設定だった場合のみ）
        if ($sort === 'old')      $query->orderBy('posts.created_at', 'asc');
        elseif ($sort === 'new')  $query->orderBy('posts.created_at', 'desc');

        $posts = $query->paginate(10)->withQueryString();

        return view('posts.index', compact('posts', 'sort'));
    }

    // 詳細（公開）
    public function show(Post $post)
    {
        // 投稿本体 + スタンプ集計を eager load
        $stampCounts = [];
        for ($i = 0; $i <= 4; $i++) {
            $stampCounts["reactions as reactions_s{$i}"] = fn($q) => $q->where('stamp', $i);
        }

        $post->load(['user','topic'])
             ->loadCount(array_merge(
                 $stampCounts,
                 ['reactions as reactions_total' => fn($q) => $q]
             ));

        // ※ Blade 側で my stamp が必要なら、個別に取得
        // $myStamp = auth()->check()
        //     ? $post->reactions()->where('user_id', auth()->id())->value('stamp')
        //     : null;

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


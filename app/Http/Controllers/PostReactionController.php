<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostReaction;
use Illuminate\Http\Request;

class PostReactionController extends Controller
{
    public function __construct(){ $this->middleware(['auth']); }

    public function store(Post $post, Request $request)
    {
        $data = $request->validate([
            'stamp' => ['required','integer','between:0,4'],
        ]);

        $userId   = $request->user()->id;
        $existing = PostReaction::where('post_id',$post->id)->where('user_id',$userId)->first();

        $removed = false;
        if ($existing && $existing->stamp === $data['stamp']) {
            // 同じスタンプ再押下 → 取り消し
            $existing->delete();
            $removed = true;
            $myStamp = null;
        } else {
            // 新規 or 変更
            PostReaction::updateOrCreate(
                ['post_id'=>$post->id, 'user_id'=>$userId],
                ['stamp'=>$data['stamp']]
            );
            $myStamp = (int)$data['stamp'];
        }

        // 集計を返す（割合計算はフロントで）
        $counts = PostReaction::where('post_id',$post->id)
            ->selectRaw('stamp, COUNT(*) as c')
            ->groupBy('stamp')
            ->pluck('c','stamp')
            ->toArray();
        $total = array_sum($counts);

        if ($request->wantsJson()) {
            return response()->json([
                'ok'     => true,
                'total'  => $total,
                'counts' => $counts,   // 例: {"0":3,"1":5,...}
                'my'     => $myStamp,  // null = 取り消し後
            ]);
        }

        // フォールバック（JS無効時）
        return back()->with('toast.success', $removed ? 'スタンプを外しました' : 'スタンプしました');
    }

}

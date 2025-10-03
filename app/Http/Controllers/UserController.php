<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;


class UserController extends Controller
{
    public function show(\App\Models\User $user)
    {
        // フォロー数を1クエリで取得
        $user->loadCount(['posts','followers','followings']);
        // 投稿一覧（ユーザーとトピックを先読み）
        $stampCounts = [];
        for ($i = 0; $i <= 4; $i++) {
            $stampCounts["reactions as reactions_s{$i}"] = fn($q) => $q->where('stamp', $i);
        }

        $posts = $user->posts()
            ->with(['topic']) // 投稿者は分かっているので user は不要
            ->withCount(array_merge(
                $stampCounts,
                ['reactions as reactions_total' => fn($q) => $q]
            ))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('users.show', compact('user','posts'));
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function show(\App\Models\User $user)
    {
        // フォロー数を1クエリで取得
        $user->loadCount(['followers', 'followings']);

        // 投稿一覧（ユーザーとトピックを先読み）
        $posts = method_exists($user, 'posts')
            ? $user->posts()->with('topic')->latest()->paginate(10)
            : collect();

        return view('users.show', compact('user', 'posts'));
    }

}

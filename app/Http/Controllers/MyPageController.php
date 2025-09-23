<?php

namespace App\Http\Controllers;

class MyPageController extends Controller
{
    public function show(\Illuminate\Http\Request $request)
    {
        $user = $request->user()->loadCount(['posts','followers','followings']);

        // 自分の投稿をページネーションで取得（10件/ページ）
        $posts = $user->posts()->latest()->paginate(5)->withQueryString();

        return view('mypage.show', compact('user','posts'));
    }

}

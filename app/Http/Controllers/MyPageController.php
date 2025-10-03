<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyPageController extends Controller
{
    public function show(Request $request)
    {
        $user = auth()->user()
            ->load(['tags' => fn($q) => $q->orderBy('sort_order')])
            ->loadCount(['posts','followers','followings']);

        // スタンプ集計（エイリアス名を r_s* / r_total に統一）
        $stampCounts = [];
        for ($i=0; $i<=4; $i++) {
            $stampCounts["reactions as r_s{$i}"] = fn($q) => $q->where('stamp', $i);
        }

        $posts = $user->posts()
            ->withCount(array_merge(
                $stampCounts,
                ['reactions as r_total' => fn($q) => $q] // 合計
            ))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('mypage.show', compact('user','posts'));
    }
}


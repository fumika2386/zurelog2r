<?php

namespace App\Http\Controllers;

class MyPageController extends Controller
{
    public function show()
    {
        $user = auth()->user()->loadCount('posts'); // posts_count

        $followersCount  = method_exists($user,'followers')  ? $user->followers()->count()  : 0;
        $followingsCount = method_exists($user,'followings') ? $user->followings()->count() : 0;

        return view('mypage.show', compact('user','followersCount','followingsCount'));
    }
}

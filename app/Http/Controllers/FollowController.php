<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function __construct()
    {
        // フォローする/外す だけ要ログイン
        $this->middleware(['auth','verified'])->only(['store','destroy']);
    }


    public function store(Request $request, User $user)
    {
        $me = $request->user();
        if ($me->id === $user->id) return back()->with('error', '自分はフォローできません。');

        $me->followings()->syncWithoutDetaching([$user->id]); // ← これ1行でOK
        return back()->with('status', 'followed');
    }


    public function destroy(Request $request, User $user)
    {
        $me = $request->user();
        if ($me->id === $user->id) {
            return back()->with('error', '自分はフォロー解除できません。');
        }

        $me->followings()->detach($user->id);
        return back()->with('status', 'unfollowed');
    }

    // 一覧（任意）：/users/{user}/followers, /users/{user}/followings
    public function followers(User $user)
    {
        $users = $user->followers()->latest('follows.created_at')->paginate(20);
        return view('users.followers', compact('user','users'));
    }

    public function followings(User $user)
    {
        $users = $user->followings()->latest('follows.created_at')->paginate(20);
        return view('users.followings', compact('user','users'));
    }
}

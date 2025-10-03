<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function __construct()
    {
        // 認証ユーザーのみ
        $this->middleware(['auth', 'verified']);
    }

    public function complete(Request $request)
    {
        $user = $request->user();

        if (!$user->onboarded_at) {
            $user->onboarded_at = now();
            $user->save();
        }

        return back()->with('toast.success', 'ようこそ！準備はばっちりです');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Tag;


class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // name, email（Breezeの既存処理）
        $user->fill($request->validated());

        // 追加分：自己紹介
        if ($request->filled('description')) {
            $user->description = $request->string('description');
        }

        // 追加分：アバター画像
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public'); // storage/app/public/avatars
            $user->avatar_path = $path;
        }

        $user->save();

        $tagIds = collect($request->input('tags', []))
                ->map(fn($id) => (int) $id)->filter()->unique()->values();

        $user->tags()->sync($tagIds);

        return Redirect::route('profile.edit')->with('toast.success', 'プロフィールを更新しました');

    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

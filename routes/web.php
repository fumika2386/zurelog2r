<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\ValueSurveyController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PostReactionController;


Route::get('/', fn () => view('welcome'));

Route::get('/dashboard', fn () => redirect()->route('posts.index'))
    ->middleware(['auth','verified'])
    ->name('dashboard');

/** Topics（公開） */
Route::get('/topics', [TopicController::class, 'index'])->name('topics.index');
Route::get('/topics/{topic}/posts', [TopicController::class, 'posts'])->name('topics.posts');

/** Users（公開） */
Route::get('/users/{user}/followers',  [FollowController::class, 'followers'])->whereNumber('user')->name('users.followers');
Route::get('/users/{user}/followings', [FollowController::class, 'followings'])->whereNumber('user')->name('users.followings');
Route::get('/users/{user}', [UserController::class, 'show'])->whereNumber('user')->name('users.show');

/** 認証必須（ここで /posts/create を“先に”登録） */
    Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])
        ->name('onboarding.complete');
    Route::middleware(['auth','verified'])->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::resource('posts', PostController::class)->only(['store','edit','update','destroy']);

    // MyPage / Profile / Survey
    Route::get('/me', [MyPageController::class, 'show'])->name('mypage.show');
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/values/survey',  [ValueSurveyController::class, 'show'])->name('values.survey.show');
    Route::post('/values/survey', [ValueSurveyController::class, 'store'])->name('values.survey.store');

    Route::post('/users/{user}/follow',   [FollowController::class, 'store'])
        ->whereNumber('user')->name('users.follow');
    Route::delete('/users/{user}/follow', [FollowController::class, 'destroy'])
        ->whereNumber('user')->name('users.unfollow');
});


Route::post('/posts/{post}/react', [PostReactionController::class, 'store'])
    ->name('posts.react');
/** Posts（公開：閲覧）— 最後に置く */
Route::resource('posts', PostController::class)->only(['index','show']);

require __DIR__.'/auth.php';

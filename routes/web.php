<?php

use App\Http\Controllers\MyPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\ValueSurveyController;
use App\Http\Controllers\PostController;   

Route::get('/', fn () => view('welcome'));

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth','verified'])->name('dashboard');

/**
 * Topics（公開）
 */
Route::get('/topics', [TopicController::class, 'index'])->name('topics.index');          // 公開: お題一覧
Route::get('/topics/{topic}/posts', [TopicController::class, 'posts'])->name('topics.posts'); // 公開: 皆の投稿

/**
 * Posts（公開）… 投稿の閲覧は誰でもOKにする
 *  ※「topics.posts」から個別詳細へ飛べるように
 */
Route::resource('posts', PostController::class)->only(['index','show']);

/**
 * ログイン必須エリア
 */
Route::middleware(['auth','verified'])->group(function () {
    // MyPage / Profile / Survey
    Route::get('/me', [MyPageController::class, 'show'])->name('mypage.show');
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/values/survey',  [ValueSurveyController::class, 'show'])->name('values.survey.show');
    Route::post('/values/survey', [ValueSurveyController::class, 'store'])->name('values.survey.store');

    // Posts（作成・更新・削除はログイン必須）
    Route::resource('posts', PostController::class)->only(['create','store','edit','update','destroy']);
});

require __DIR__.'/auth.php';

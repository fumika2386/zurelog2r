<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ValueSurveyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyPageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth','verified')->group(function () {
    Route::get('/me', [MyPageController::class, 'show'])->name('mypage.show');
//   profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// 　Value Survey
    Route::get('/values/survey',  [ValueSurveyController::class, 'show'])->name('values.survey.show');
    Route::post('/values/survey', [ValueSurveyController::class, 'store'])->name('values.survey.store');
});

require __DIR__.'/auth.php';

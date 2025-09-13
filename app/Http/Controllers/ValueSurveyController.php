<?php
namespace App\Http\Controllers;

use App\Http\Requests\ValueSurveyRequest;
use App\Models\ValueQuestion;
use App\Models\ValueAnswer;
use Illuminate\Http\RedirectResponse;

class ValueSurveyController extends Controller
{
    // public function __construct() { $this->middleware(['auth','verified']); }

    // フォーム表示
    public function show()
    {
        $user = auth()->user();
        $questions = ValueQuestion::orderBy('sort_order')->get();

        
        // 既存回答を取得して初期表示
        $existing = $user->valueAnswers()
            ->pluck('value','question_id') // [question_id => 0/1]
            ->toArray();

        return view('values.survey', compact('questions','existing'));
    }

    // 保存
    public function store(ValueSurveyRequest $request): RedirectResponse
    {
        $user = $request->user();
        $now  = now();

        $rows = [];
        foreach ($request->input('answers') as $qid => $val) {
            $rows[] = [
                'user_id'     => $user->id,
                'question_id' => (int)$qid,
                'value'       => (int)$val,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        // upsert: (user_id, question_id) をキーに value を更新
        ValueAnswer::upsert(
            $rows,
            ['user_id','question_id'],
            ['value','updated_at']
        );

        return redirect()->route('values.survey.show')->with('status','saved');
    }
}

<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValueSurveyRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        // answers[question_id] => 0 or 1
        return [
            'answers' => ['required','array'],
            'answers.*' => ['required','in:0,1'],
            // 存在チェック（answersのキー=question_id）
            'question_ids' => ['required','array'],
            'question_ids.*' => ['required', Rule::exists('value_questions','id')],
        ];
    }

    protected function prepareForValidation(): void
    {
        // フォームから受けた keys を question_ids としてまとめる
        $this->merge(['question_ids' => array_keys($this->input('answers', []))]);
    }
}

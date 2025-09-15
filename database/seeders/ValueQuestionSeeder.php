<?php
namespace Database\Seeders;

use App\Models\ValueQuestion;
use Illuminate\Database\Seeder;

class ValueQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            ['ソーシャルメディアよりも対面の会話を重視する', 1],
            ['成果よりもプロセスの誠実さを重視する', 2],
            ['短期利益よりも長期的な持続可能性を優先する', 3],
            ['多数派よりも少数派の声に耳を傾けたい', 4],
            ['法律よりも道徳が優先される場面がある', 5],
            ['個人の自由よりも公共の安全を優先すべきだ', 6],
            ['完全リモートより出社が望ましい', 7],
            ['学歴より実務経験を重視する', 8],
            ['AIの判断は人間より公平になり得る', 9],
            ['失敗しても挑戦を評価すべきだ', 10],
        ];
        foreach ($questions as [$text, $order]) {
            ValueQuestion::updateOrCreate(['sort_order' => $order], ['text' => $text]);
        }
    }
}

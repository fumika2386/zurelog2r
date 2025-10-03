<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Topic;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $rows = [

            ['title'=>'初めて気づいた自分の癖',
            'description'=>'ふと気づいた行動・思考パターン',
            'is_published'=>true, 'created_at'=>$now, 'updated_at'=>$now],

            ['title'=>'やらないと決めたこと',
            'description'=>'増やすより減らす。やめて楽になったこと',
            'is_published'=>true,  'created_at'=>$now, 'updated_at'=>$now],
        
            ['title'=>'会社の飲み会、必要派？不要派？',
            'description'=>'コミュニケーションか拘束か。地域・職種での体感差も語ろう。',
            'is_published'=>true,  'created_at'=>$now, 'updated_at'=>$now],

            ['title'=>'社会と個人の幸せの折り合い',
            'description'=>'公共と私的の線引き。人生ステージや地域での違いを探る。',
            'is_published'=>true,  'created_at'=>$now, 'updated_at'=>$now],

            ['title'=>'副業のリアル',
            'description'=>'自己投資か分散疲労か。企業規模・地域での許容度差。',
            'is_published'=>true, 'created_at'=>$now, 'updated_at'=>$now],

            ['title'=>'地域コミュニティとの距離感',
            'description'=>'地元のつながりは資産？負担？年齢と地理で変わる参加度。',
            'is_published'=>true, 'created_at'=>$now, 'updated_at'=>$now],

            // ['title'=>'働く意味：お金／使命／生活のため',
            // 'description'=>'価値観の重み付けと変化のきっかけをシェア。',
            // 'is_published'=>true, 'sort_order'=>140, 'created_at'=>$now, 'updated_at'=>$now],

            // ['title'=>'都市と地方の“豊かさ”の定義',
            // 'description'=>'収入・時間・自然・人間関係…あなたの優先順位は？',
            // 'is_published'=>true, 'sort_order'=>150, 'created_at'=>$now, 'updated_at'=>$now],

            ['title'=>'上司へ一言',
            'description'=>'言えなかった建設的な一言を。称賛・要望・ズレの指摘まで。',
            'is_published'=>true,  'created_at'=>$now, 'updated_at'=>$now],

            ['title'=>'部下へ一言',
            'description'=>'伝えたい期待・感謝・フィードバックを丁寧に言語化。',
            'is_published'=>true, 'created_at'=>$now, 'updated_at'=>$now],

            ['title'=>'旦那へ一言',
            'description'=>'普段は言えない感謝・要望・伝えたい一言を丁寧に。',
            'is_published'=>true,  'created_at'=>$now, 'updated_at'=>$now],

            ['title'=>'奥さんへ一言',
            'description'=>'普段は言えない感謝・要望・伝えたい一言を丁寧に。',
            'is_published'=>true, 'created_at'=>$now, 'updated_at'=>$now],

            ['title'=>'努力と余白',
            'description'=>'追い込む努力と緩める余白のベストバランスを語ろう。',
            'is_published'=>true, 'created_at'=>$now, 'updated_at'=>$now],

        ];

        // タイトルをキーに新規作成 or 更新（重複しない）
        \App\Models\Topic::upsert(
            $rows,
            ['title'], // 一意キー
            ['description','is_published','sort_order','updated_at'] // 更新する列
        );


    }
}

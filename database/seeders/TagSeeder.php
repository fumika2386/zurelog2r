<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder {
  public function run(): void {
    $tags = [
      ['name'=>'昭和世代',     'slug'=>'showa',     'sort_order'=>5],
      ['name'=>'団塊世代',     'slug'=>'dankai',     'sort_order'=>10],
      ['name'=>'氷河期世代',     'slug'=>'hyouga',     'sort_order'=>10],
      ['name'=>'ゆとり世代',     'slug'=>'satori',     'sort_order'=>15],
      ['name'=>'Z世代',        'slug'=>'gen-z',     'sort_order'=>20],

      ['name'=>'男性',     'slug'=>'man', 'sort_order'=>24],
      ['name'=>'女性',     'slug'=>'woman', 'sort_order'=>25],
      ['name'=>'ジェンダーレス','slug'=>'genderless','sort_order'=>30],

      ['name'=>'子育て中',   'slug'=>'parents',   'sort_order'=>40],
      ['name'=>'独身',     'slug'=>'dokushin',     'sort_order'=>45],
      ['name'=>'学生',     'slug'=>'student',     'sort_order'=>47],

      ['name'=>'係長', 'slug'=>'management','sort_order'=>50],
      ['name'=>'事務職',       'slug'=>'clerical',  'sort_order'=>60],
      ['name'=>'営業職',       'slug'=>'sales',     'sort_order'=>70],
      ['name'=>'体育会系',     'slug'=>'athletics', 'sort_order'=>80],
      ['name'=>'文化系',     'slug'=>'bunka', 'sort_order'=>80],

    ];
    foreach ($tags as $t) {
      \App\Models\Tag::updateOrCreate(['slug'=>$t['slug']], $t);
    }
  }
}

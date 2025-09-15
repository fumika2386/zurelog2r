<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Topic;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        Topic::insert([
            ['title' => '在宅勤務は生産性を上げる？', 'description' => '働き方と評価のこれから', 'is_published' => true, 'created_at' => $now, 'updated_at' => $now],
            ['title' => '大学無償化は本当に必要？', 'description' => '教育の公平性と財政負担について', 'is_published' => true, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'AI規制はどこまで必要？',   'description' => 'イノベーションと安全性のバランス', 'is_published' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}

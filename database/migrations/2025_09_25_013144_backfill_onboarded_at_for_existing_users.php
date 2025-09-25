<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 既存ユーザー：onboarded_at が NULL のものを作成日時で埋める
        DB::table('users')
          ->whereNull('onboarded_at')
          ->update(['onboarded_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        // 差し戻し時は何もしない（安全のため）
    }
};

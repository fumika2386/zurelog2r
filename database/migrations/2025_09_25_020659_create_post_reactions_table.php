<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('post_reactions')) {
            Schema::create('post_reactions', function (Blueprint $t) {
                $t->id();
                $t->foreignId('post_id')->constrained()->cascadeOnDelete();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                // 0..4 の5種類（enumでもOK。拡張しやすさ重視でtinyInteger）
                $t->tinyInteger('stamp')->unsigned();
                $t->timestamps();

                $t->unique(['post_id','user_id']);      // 1ユーザー1スタンプ
                $t->index(['post_id','stamp']);          // 集計高速化
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('post_reactions');
    }
};


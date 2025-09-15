<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('follows', function (Blueprint $table) {
            // ピボットなのでIDなしでOK
            $table->foreignId('follower_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('followed_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            // 同じ組み合わせの重複フォローを禁止
            $table->unique(['follower_id', 'followed_id']);
            // 片方の検索を速く
            $table->index('followed_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};

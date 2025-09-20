<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('followed_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['follower_id','followed_id']); // 重複フォロー禁止
            $table->index(['followed_id']);                // カウント向け
        });
    }
    public function down(): void {
        Schema::dropIfExists('follows');
    }
};

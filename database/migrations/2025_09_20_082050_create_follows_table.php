<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void 
    {
        // 既にあるなら何もしない（ここで止まらないようにする）
        if (\Schema::hasTable('follows')) {
            return;
        }

        \Schema::create('follows', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->foreignId('follower_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('followed_id')->constrained('users')->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['follower_id','followed_id']);
            $t->index('followed_id');
            $t->index('follower_id');
        });
    }


    public function down(): void {
        Schema::dropIfExists('follows');
    }
};

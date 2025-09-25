<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    if (!Schema::hasTable('tags')) {
      Schema::create('tags', function (Blueprint $t) {
        $t->id();
        $t->string('name')->unique();           // 表示名（日本語OK・ユニーク）
        $t->string('slug')->unique();           // URL/内部用（romaji等）
        $t->unsignedInteger('sort_order')->default(0);
        $t->timestamps();
      });
    }
    if (!Schema::hasTable('user_tag')) {
      Schema::create('user_tag', function (Blueprint $t) {
        $t->id();
        $t->foreignId('user_id')->constrained()->cascadeOnDelete();
        $t->foreignId('tag_id')->constrained()->cascadeOnDelete();
        $t->timestamps();
        $t->unique(['user_id','tag_id']);
        $t->index(['tag_id','user_id']);
      });
    }
  }
  public function down(): void {
    Schema::dropIfExists('user_tag');
    Schema::dropIfExists('tags');
  }
};

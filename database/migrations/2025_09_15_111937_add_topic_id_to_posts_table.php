<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void{
        if (!Schema::hasColumn('posts', 'topic_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->foreignId('topic_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'topic_id')) {
                $table->dropConstrainedForeignId('topic_id');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToLivePostsTable extends Migration
{
    public function up(): void
    {
        Schema::table('live_posts', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('live_posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
}
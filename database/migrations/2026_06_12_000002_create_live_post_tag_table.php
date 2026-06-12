<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_post_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_post_id')->constrained('live_posts')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['live_post_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_post_tag');
    }
};

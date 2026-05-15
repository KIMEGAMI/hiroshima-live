<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('live_post_artists', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_post_artists');
    }
};
Schema::create('live_post_artists', function (Blueprint $table) {
    $table->id();
    $table->foreignId('live_post_id')->constrained('live_posts')->cascadeOnDelete();
    $table->foreignId('artist_id')->constrained('artists')->cascadeOnDelete();
    $table->timestamps();

    $table->unique(['live_post_id', 'artist_id']);
});
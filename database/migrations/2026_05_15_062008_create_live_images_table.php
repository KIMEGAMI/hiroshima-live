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
        Schema::create('live_images', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_images');
    }
};
Schema::create('live_images', function (Blueprint $table) {
    $table->id();
    $table->foreignId('live_post_id')->constrained('live_posts')->cascadeOnDelete();
    $table->string('image_path');
    $table->unsignedInteger('sort_order')->default(0);
    $table->timestamps();
});
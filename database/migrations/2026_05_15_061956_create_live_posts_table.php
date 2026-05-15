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
        Schema::create('live_posts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_posts');
    }
};
Schema::create('live_posts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('live_house_id')->nullable()->constrained('live_houses')->nullOnDelete();

    $table->string('title');
    $table->date('event_date')->index();
    $table->time('start_time')->nullable();
    $table->time('open_time')->nullable();
    $table->text('description')->nullable();
    $table->string('price')->nullable();
    $table->string('status', 50)->default('published')->index();

    $table->timestamps();
});
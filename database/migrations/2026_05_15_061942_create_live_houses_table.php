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
        Schema::create('live_houses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_houses');
    }
};
Schema::create('live_houses', function (Blueprint $table) {
    $table->id();
    $table->string('name')->index();
    $table->string('prefecture', 100)->nullable();
    $table->string('city', 100)->nullable();
    $table->string('address')->nullable();
    $table->text('access')->nullable();
    $table->string('official_url')->nullable();
    $table->timestamps();
});


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_submissions', function (Blueprint $table): void {
            $table->id();
            $table->string('reference_number')->unique();
            $table->string('status')->default('pending')->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('live_post_id')->nullable()->constrained()->nullOnDelete();
            $table->string('applicant_name');
            $table->string('organization_name');
            $table->string('applicant_type');
            $table->string('contact_email');
            $table->string('phone')->nullable();
            $table->string('official_site_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('x_url')->nullable();
            $table->string('event_title');
            $table->date('event_date');
            $table->time('open_time')->nullable();
            $table->time('start_time');
            $table->string('venue_name');
            $table->string('venue_address')->nullable();
            $table->string('performers');
            $table->text('description');
            $table->string('ticket_price')->nullable();
            $table->string('ticket_url')->nullable();
            $table->text('notes')->nullable();
            $table->string('flyer_image_path')->nullable();
            $table->string('official_event_url')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'event_date']);
            $table->index('contact_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_submissions');
    }
};

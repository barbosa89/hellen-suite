<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('tra_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 30);
            $table->string('status', 30);
            $table->string('idempotency_key')->unique();
            $table->json('payload_snapshot')->nullable();
            $table->string('external_reference', 255)->nullable();
            $table->string('last_error_code', 64)->nullable();
            $table->text('last_error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stay_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_occupancy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stay_guest_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['hotel_id', 'status']);
            $table->index(['stay_id', 'status']);
        });

        Schema::create('tra_submission_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 30);
            $table->string('request_hash', 64);
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->string('response_reference', 255)->nullable();
            $table->string('error_category', 30)->nullable();
            $table->text('error_message')->nullable();
            $table->foreignId('tra_submission_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index('tra_submission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tra_submission_attempts');
        Schema::dropIfExists('tra_submissions');
    }
};

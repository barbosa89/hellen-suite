<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);
            $table->string('direction', 10);
            $table->unsignedBigInteger('amount_minor');
            $table->string('currency', 3);
            $table->string('comment')->nullable();
            $table->timestamp('occurred_at');
            $table->uuid('idempotency_key')->unique();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->unique()->constrained()->restrictOnDelete();
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['hotel_id', 'occurred_at']);
            $table->index(['hotel_id', 'type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
};

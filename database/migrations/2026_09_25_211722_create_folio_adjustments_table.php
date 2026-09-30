<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('folio_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);
            $table->string('direction', 10);
            $table->unsignedBigInteger('amount_minor');
            $table->string('reason');
            $table->timestamp('occurred_at');
            $table->uuid('idempotency_key')->unique();
            $table->foreignId('stay_folio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('folio_charge_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['stay_folio_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folio_adjustments');
    }
};

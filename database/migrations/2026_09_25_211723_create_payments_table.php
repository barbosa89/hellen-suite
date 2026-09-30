<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->string('method', 30);
            $table->unsignedBigInteger('amount_minor');
            $table->string('currency', 3);
            $table->string('comment')->nullable();
            $table->string('support_path')->nullable();
            $table->timestamp('paid_at');
            $table->uuid('idempotency_key')->unique();
            $table->foreignId('stay_folio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_payment_id')->nullable()->constrained('payments')->restrictOnDelete();
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['stay_folio_id', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

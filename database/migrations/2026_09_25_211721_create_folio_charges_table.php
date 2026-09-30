<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('folio_charges', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);
            $table->string('description');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('unit_amount_minor');
            $table->unsignedBigInteger('total_amount_minor');
            $table->date('service_start_on')->nullable();
            $table->date('service_end_on')->nullable();
            $table->timestamp('posted_at');
            $table->uuid('idempotency_key')->unique();
            $table->foreignId('stay_folio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_occupancy_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['room_occupancy_id', 'type']);
            $table->index(['stay_folio_id', 'posted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folio_charges');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('room_occupancy_events', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);
            $table->json('before_data')->nullable();
            $table->json('after_data');
            $table->foreignId('room_occupancy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['room_occupancy_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_occupancy_events');
    }
};

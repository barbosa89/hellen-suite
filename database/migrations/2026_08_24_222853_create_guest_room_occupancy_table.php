<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('guest_room_occupancy', function (Blueprint $table) {
            $table->foreignId('guest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_occupancy_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['guest_id', 'room_occupancy_id']);
            $table->index('room_occupancy_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_room_occupancy');
    }
};

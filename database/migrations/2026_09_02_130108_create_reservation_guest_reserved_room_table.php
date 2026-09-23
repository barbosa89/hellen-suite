<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_guest_reserved_room', function (Blueprint $table) {
            $table->foreignId('reservation_guest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reserved_room_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique('reservation_guest_id');
            $table->unique(['reserved_room_id', 'reservation_guest_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_guest_reserved_room');
    }
};

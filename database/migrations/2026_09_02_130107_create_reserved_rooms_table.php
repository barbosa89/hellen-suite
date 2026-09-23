<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('reserved_rooms', function (Blueprint $table) {
            $table->id();
            $table->decimal('nightly_rate', 12, 2);
            $table->date('planned_check_in_on');
            $table->date('planned_check_out_on');
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->unique(['reservation_id', 'room_id']);
            $table->index(['room_id', 'planned_check_in_on', 'planned_check_out_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reserved_rooms');
    }
};

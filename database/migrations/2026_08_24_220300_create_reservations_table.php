<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('status', 30)->default('draft');
            $table->date('planned_check_in_on');
            $table->date('planned_check_out_on');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('no_show_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('responsible_guest_id')->constrained('guests')->restrictOnDelete();
            $table->timestamps();

            $table->index(['hotel_id', 'status', 'planned_check_in_on']);
            $table->index(['hotel_id', 'planned_check_in_on', 'planned_check_out_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};

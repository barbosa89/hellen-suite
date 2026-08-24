<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->unsignedTinyInteger('capacity');
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['hotel_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};

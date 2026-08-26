<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('identification_number', 50);
            $table->string('mobile', 30)->nullable();
            $table->string('email')->nullable();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('identification_type_id')->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->unique(['hotel_id', 'identification_type_id', 'identification_number']);
            $table->index(['hotel_id', 'last_name', 'first_name']);
            $table->index(['hotel_id', 'mobile']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};

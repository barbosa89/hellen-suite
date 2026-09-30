<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('number');
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['hotel_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_vouchers');
    }
};

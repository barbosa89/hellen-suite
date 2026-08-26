<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('stay_guests', function (Blueprint $table) {
            $table->id();
            $table->string('role', 30);
            $table->foreignId('stay_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->unique(['stay_id', 'guest_id']);
            $table->index(['stay_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stay_guests');
    }
};

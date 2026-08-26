<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('room_occupancies', function (Blueprint $table) {
            $table->id();
            $table->decimal('nightly_rate', 12, 2);
            $table->timestamp('checked_in_at');
            $table->date('expected_check_out_on');
            $table->timestamp('checked_out_at')->nullable();
            $table->string('end_reason', 30)->nullable();
            $table->foreignId('stay_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->index(['room_id', 'checked_out_at']);
            $table->index(['stay_id', 'checked_out_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_occupancies');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('stays', function (Blueprint $table) {
            $table->id();
            $table->string('status', 30);
            $table->string('currency', 3)->default('USD');
            $table->timestamp('checked_in_at');
            $table->date('expected_check_out_on');
            $table->timestamp('checked_out_at')->nullable();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('responsible_guest_id')->constrained('guests')->restrictOnDelete();
            $table->foreignId('reservation_id')->nullable()->unique()->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->index(['hotel_id', 'status', 'expected_check_out_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stays');
    }
};

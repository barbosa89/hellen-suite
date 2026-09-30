<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('stay_folios', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('currency', 3);
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stay_id')->constrained()->cascadeOnDelete();
            $table->foreignId('closed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['stay_id', 'closed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stay_folios');
    }
};

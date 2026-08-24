<?php

declare(strict_types=1);

use App\Constants\HousekeepingStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('number', 20);
            $table->string('floor', 20)->nullable();
            $table->decimal('reference_price', 12, 2);
            $table->enum('housekeeping_status', HousekeepingStatus::toArray())
                ->default(HousekeepingStatus::Clean->value);
            $table->boolean('is_active')->default(true);
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->index()->constrained();
            $table->timestamps();

            $table->unique(['hotel_id', 'number']);
            $table->index(['hotel_id', 'housekeeping_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

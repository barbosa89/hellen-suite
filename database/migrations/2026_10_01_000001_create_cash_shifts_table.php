<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('cash_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('number');
            $table->foreignId('previous_cash_shift_id')->nullable()->unique()->constrained('cash_shifts')->restrictOnDelete();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->string('opening_note')->nullable();
            $table->string('closing_note')->nullable();
            $table->timestamps();

            $table->unique(['hotel_id', 'number']);
            $table->index(['hotel_id', 'opened_at']);
            $table->index(['hotel_id', 'closed_at']);
        });

        DB::statement('CREATE UNIQUE INDEX cash_shifts_one_open_per_hotel ON cash_shifts (hotel_id) WHERE closed_at IS NULL');

        Schema::create('cash_shift_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_shift_id')->constrained()->cascadeOnDelete();
            $table->string('payment_method', 30);
            $table->string('currency', 3);
            $table->unsignedBigInteger('opening_minor')->default(0);
            $table->unsignedBigInteger('inflow_minor')->default(0);
            $table->unsignedBigInteger('outflow_minor')->default(0);
            $table->bigInteger('expected_closing_minor')->nullable();
            $table->bigInteger('declared_closing_minor')->nullable();
            $table->bigInteger('difference_minor')->nullable();
            $table->timestamps();

            $table->unique(['cash_shift_id', 'payment_method', 'currency'], 'cash_shift_reconciliation_unique');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('cash_shift_id')->nullable()->after('stay_folio_id')->constrained()->restrictOnDelete();
            $table->index(['cash_shift_id', 'paid_at']);
        });

        Schema::table('cash_movements', function (Blueprint $table) {
            $table->foreignId('cash_shift_id')->nullable()->after('hotel_id')->constrained()->restrictOnDelete();
            $table->index(['cash_shift_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::table('cash_movements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cash_shift_id');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cash_shift_id');
        });
        Schema::dropIfExists('cash_shift_reconciliations');
        Schema::dropIfExists('cash_shifts');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('id');
            $table->date('date');
            $table->text('commentary');
            $table->enum('payment_method', ['cash', 'transfer', 'courtesy']);
            $table->decimal('value', 10, 2);
            $table->text('invoice')->nullable();

            $table->foreignId('voucher_id');
            $table->foreign('voucher_id')->references('id')
                ->on('vouchers')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}

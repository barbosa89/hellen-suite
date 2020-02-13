<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_invoice', function (Blueprint $table) {
            $table->bigInteger('guest_id')->unsigned();
            $table->bigInteger('voucher_id')->unsigned();
            $table->boolean('main')->default(false);
            $table->boolean('active'); // The guest is at the hotel and is related to an voucher

            $table->foreign('guest_id')->references('id')->on('guests')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('voucher_id')->references('id')->on('vouchers')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['guest_id', 'voucher_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guest_invoice');
    }
}

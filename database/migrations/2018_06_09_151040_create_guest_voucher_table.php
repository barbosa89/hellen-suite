<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_voucher', function (Blueprint $table) {
            $table->foreignId('guest_id');
            $table->foreignId('voucher_id');
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
        Schema::dropIfExists('guest_voucher');
    }
}

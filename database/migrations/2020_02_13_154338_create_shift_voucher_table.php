<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_voucher', function (Blueprint $table) {
            $table->foreignId('voucher_id');
            $table->foreignId('shift_id');

            $table->foreign('voucher_id')->references('id')->on('vouchers')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('shifts')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['shift_id', 'voucher_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_voucher');
    }
}

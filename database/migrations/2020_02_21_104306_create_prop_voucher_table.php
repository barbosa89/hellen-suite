<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prop_voucher', function (Blueprint $table) {
            $table->foreignId('voucher_id');
            $table->foreignId('prop_id');
            $table->integer('quantity');
            $table->decimal('value', 10, 2);
            $table->dateTime('created_at');

            $table->foreign('voucher_id')->references('id')->on('vouchers')
            ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('prop_id')->references('id')->on('props')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prop_voucher');
    }
}

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
            $table->bigInteger('voucher_id')->unsigned();
            $table->bigInteger('prop_id')->unsigned();
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

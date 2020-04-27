<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_voucher', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('voucher_id');
            $table->foreignId('product_id');
            $table->integer('quantity');
            $table->decimal('value', 10, 2);
            $table->dateTime('created_at');

            $table->foreign('voucher_id')->references('id')->on('vouchers')
            ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')
                ->onUpdate('cascade')->onDelete('cascade');

            // $table->primary(['product_id', 'voucher_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_voucher');
    }
}

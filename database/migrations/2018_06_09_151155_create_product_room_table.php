<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductRoomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_room', function (Blueprint $table) {
            $table->foreignId('product_id');
            $table->foreignId('room_id');
            $table->integer('quantity');

            $table->foreign('product_id')->references('id')->on('products')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['product_id', 'room_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_room');
    }
}

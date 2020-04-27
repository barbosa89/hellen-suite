<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id('id');
            $table->string('number', 20);
            $table->string('description');
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('serial_number', 150)->nullable();
            $table->string('location')->nullable();
            $table->decimal('price', 10, 2);

            $table->foreignId('room_id')->unsigned()->nullable();
            $table->foreign('room_id')->references('id')
                ->on('rooms')->onDelete('cascade')->onUpdate('cascade');

            $table->foreignId('hotel_id');
            $table->foreign('hotel_id')->references('id')
                ->on('hotels')->onDelete('cascade')->onUpdate('cascade');

            $table->foreignId('user_id');
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('assets');
    }
}

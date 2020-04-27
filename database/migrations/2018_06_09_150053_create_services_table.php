<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id('id');
            $table->string('description');
            $table->decimal('price', 10, 2);
            $table->boolean('is_dining_service')->default(false);
            $table->boolean('status')->default(true);

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
        Schema::dropIfExists('services');
    }
}

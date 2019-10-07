<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestVehicleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_vehicle', function (Blueprint $table) {
            $table->bigInteger('guest_id')->unsigned();
            $table->bigInteger('vehicle_id')->unsigned();
            $table->datetime('created_at')->nullable();
            $table->bigInteger('invoice_id')->nullable();
            $table->string('other')->nullable();

            $table->foreign('guest_id')->references('id')->on('guests')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')
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
        Schema::dropIfExists('guest_vehicle');
    }
}

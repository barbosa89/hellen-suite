<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceRoomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_room', function (Blueprint $table) {
            $table->bigInteger('invoice_id')->unsigned();
            $table->bigInteger('room_id')->unsigned();
            $table->integer('quantity'); // On days
            $table->decimal('value', 10, 2);
            $table->date('start')->nullable();
            $table->date('end')->nullable();

            $table->foreign('invoice_id')->references('id')->on('invoices')
            ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['room_id', 'invoice_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_room');
    }
}

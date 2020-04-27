<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_voucher', function (Blueprint $table) {
            $table->foreignId('voucher_id');
            $table->foreignId('room_id');
            $table->integer('quantity'); // On days
            $table->decimal('price', 10, 2); // Final price given to the user
            $table->decimal('discount', 10, 2);
            $table->decimal('subvalue', 10, 2);
            $table->decimal('taxes', 10, 2);
            $table->decimal('value', 10, 2);
            $table->date('start');
            $table->date('end')->nullable();
            $table->boolean('enabled'); // The room can be processed

            $table->foreign('voucher_id')->references('id')->on('vouchers')
            ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['room_id', 'voucher_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_voucher');
    }
}

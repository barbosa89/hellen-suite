<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdditionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additionals', function (Blueprint $table) {
            $table->id('id');
            $table->string('description');
            $table->boolean('billable')->default(true);
            $table->decimal('value', 10, 2);

            $table->foreignId('voucher_id');
            $table->foreign('voucher_id')->references('id')
                ->on('vouchers')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('additionals');
    }
}

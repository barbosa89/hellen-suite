<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number')->unique();
            $table->decimal('discount', 10, 2);
            $table->decimal('subvalue', 10, 2);
            $table->decimal('taxes', 10, 2);
            $table->decimal('value', 10, 2);
            $table->boolean('open')->default(true);
            $table->enum('status', [0, 1, 2]); # 0: loss, 1: paid, 2: credit
            $table->integer('user_id')->unsigned();
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
        Schema::dropIfExists('invoices');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checks', function (Blueprint $table) {
            $table->id();

            $table->timestamp('in_at');
            $table->timestamp('out_at')->nullable();

            $table->foreignId('guest_id');
            $table->foreign('guest_id')
                ->references('id')
                ->on('guests')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('voucher_id');
            $table->foreign('voucher_id')
                ->references('id')
                ->on('vouchers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

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
        Schema::dropIfExists('checks');
    }
}

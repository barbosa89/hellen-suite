<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id('id');
            $table->decimal('cash', 12, 2)->default(0.0);
            $table->boolean('open')->default(true);
            $table->bigInteger('team_member');
            $table->string('team_member_name');

            $table->foreignId('hotel_id');
            $table->foreign('hotel_id')->references('id')
                ->on('hotels')->onDelete('cascade')->onUpdate('cascade');

            $table->foreignId('user_id');
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
            $table->datetime('closed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shifts');
    }
}

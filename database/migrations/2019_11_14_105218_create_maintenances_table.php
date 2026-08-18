<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id('id');
            $table->date('date');
            $table->text('commentary');
            $table->float('value', 10, 2)->nullable();
            $table->text('invoice')->nullable();

            $table->foreignId('maintainable_id');
            $table->string('maintainable_type');

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
        Schema::dropIfExists('maintenances');
    }
}

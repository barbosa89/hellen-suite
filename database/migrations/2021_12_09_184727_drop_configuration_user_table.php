<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropConfigurationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('configuration_user');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('configuration_user', function (Blueprint $table) {
            $table->foreignId('configuration_id');
            $table->foreignId('user_id');
            $table->string('value')->nullable();

            $table->foreign('configuration_id')->references('id')->on('configurations')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['configuration_id', 'user_id']);
            $table->timestamps();
        });
    }
}

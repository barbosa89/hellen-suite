<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('dni', 15)->unique();
            $table->string('name', 150);
            $table->string('last_name', 150);
            $table->string('email', 120)->nullable();
            $table->enum('gender', ['m', 'f', 'x'])->nullable();
            $table->date('birthdate')->nullable();
            $table->string('profession', 100)->nullable();

            $table->integer('country_id')->unsigned();
            $table->foreign('country_id')
                ->references('id')
                ->on('countries');

            $table->bigInteger('responsible_adult')->default(0);
            $table->boolean('status')->default(0);

            $table->integer('identification_type_id')->unsigned();
            $table->foreign('identification_type_id')->references('id')
                ->on('identification_types')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade')
                ->onUpdate('cascade');

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
        Schema::dropIfExists('guests');
    }
}

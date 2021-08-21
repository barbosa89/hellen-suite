<?php

use App\Constants\Genders;
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
            $table->id('id');
            $table->string('dni', 15);
            $table->string('name', 150);
            $table->string('last_name', 150);
            $table->string('email', 120)->nullable();
            $table->string('address', 191)->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('gender', [Genders::MALE, Genders::FEMALE, Genders::OTHER])->nullable();
            $table->date('birthdate')->nullable();
            $table->string('profession', 100)->nullable();

            $table->foreignId('country_id');
            $table->foreign('country_id')
                ->references('id')
                ->on('countries');

            $table->foreignId('responsible_adult')->default(0);
            $table->boolean('status')->default(0);
            $table->boolean('banned')->default(0);

            $table->foreignId('identification_type_id');
            $table->foreign('identification_type_id')->references('id')
                ->on('identification_types')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->foreignId('user_id');
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

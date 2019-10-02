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
            $table->bigIncrements('id');
            $table->string('number')->unique();
            $table->decimal('discount', 10, 2);
            $table->decimal('subvalue', 10, 2);
            $table->decimal('taxes', 10, 2)->default(0.0);
            $table->decimal('value', 10, 2);
            $table->boolean('open')->default(true);
            # 0: inactive, 1: active
            $table->boolean('status')->dafault(true);
            $table->boolean('reservation')->default(false);

            $table->boolean('are_tourists')->default(false)->nullable();
            $table->boolean('for_job')->default(false)->nullable();

            $table->integer('hotel_id')->nullable()->unsigned();
            $table->foreign('hotel_id')->references('id')
                ->on('companies')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('company_id')->nullable()->unsigned();
            $table->foreign('company_id')->references('id')
                ->on('companies')->onDelete('cascade')->onUpdate('cascade');

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

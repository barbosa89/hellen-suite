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
            $table->string('origin')->nullable();
            $table->string('destination')->nullable();
            $table->decimal('discount', 10, 2)->default(0.0);
            $table->decimal('subvalue', 10, 2)->default(0.0);
            $table->decimal('taxes', 10, 2)->default(0.0);
            $table->decimal('value', 10, 2)->default(0.0);
            $table->boolean('open')->default(true);
            # 0: inactive, 1: active
            $table->boolean('status')->dafault(true);
            $table->boolean('payment_status')->dafault(false);
            $table->boolean('reservation')->default(false);

            $table->bigInteger('hotel_id')->unsigned();
            $table->foreign('hotel_id')->references('id')
                ->on('hotels')->onDelete('cascade')->onUpdate('cascade');

            $table->bigInteger('company_id')->nullable()->unsigned();
            $table->foreign('company_id')->references('id')
                ->on('companies')->onDelete('cascade')->onUpdate('cascade');

            // Invoice owner
            $table->bigInteger('user_id')->unsigned();
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

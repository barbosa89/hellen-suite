<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('number', 40);
            $table->string('customer_name', 120);
            $table->string('customer_dni', 20);
            $table->decimal('value', 10, 2);
            $table->decimal('discount', 10, 2)->default(0.0);
            $table->decimal('taxes', 10, 2)->default(0.0);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['PENDING', 'CANCELED', 'PAID'])->default('PENDING');

            $table->foreignId('identification_type_id');
            $table->foreign('identification_type_id')
                ->references('id')
                ->on('identification_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('currency_id');
            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
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
        Schema::dropIfExists('invoices');
    }
}

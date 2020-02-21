<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('number')->unique();
            $table->string('origin')->nullable();
            $table->string('destination')->nullable();
            $table->decimal('discount', 10, 2)->default(0.0);
            $table->decimal('subvalue', 10, 2)->default(0.0);
            $table->decimal('taxes', 10, 2)->default(0.0);
            $table->decimal('value', 10, 2)->default(0.0);
            $table->boolean('open')->default(true);

            // Status
            // 0: inactive
            // 1: active
            $table->boolean('status')->default(true);
            $table->boolean('payment_status')->default(false);
            $table->boolean('losses')->default(false);
            $table->boolean('reservation')->default(false);

            // Type
            // Products in lodging vouchers: lodging
            // Products in restaurant: dining, loss
            // Products in direct sales: sale, loss
            // Products entry: entry

            // Services in lodging vouchers: lodging
            // Services in restaurant: loss, dining

            // Rooms: lodging
            // Props: entry, discard
            $table->enum('type', ['sale', 'entry', 'loss', 'discard', 'lodging', 'dining']);

            $table->string('made_by')->nullable();

            $table->text('comments')->nullable();

            $table->bigInteger('hotel_id')->unsigned();
            $table->foreign('hotel_id')->references('id')
                ->on('hotels')->onDelete('cascade')->onUpdate('cascade');

            $table->bigInteger('company_id')->nullable()->unsigned();
            $table->foreign('company_id')->references('id')
                ->on('companies')->onDelete('cascade')->onUpdate('cascade');

            // Vouche owner
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
        Schema::dropIfExists('vouchers');
    }
}

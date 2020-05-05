<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoteShiftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('note_shift', function (Blueprint $table) {
            $table->foreignId('note_id');
            $table->foreignId('shift_id');

            $table->foreign('note_id')
                ->references('id')
                ->on('notes')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('shift_id')
                ->references('id')
                ->on('shifts')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->primary(['shift_id', 'note_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('note_shift');
    }
}

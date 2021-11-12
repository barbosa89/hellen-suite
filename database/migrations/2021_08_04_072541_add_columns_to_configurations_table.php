<?php

use App\Constants\Modules;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->enum('module', Modules::toArray())
                ->after('name');

            $table->timestamp('enabled_at')
                ->nullable()
                ->after('module');

            $table->unique(['name', 'module']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropUnique(['name', 'module']);
            $table->dropColumn('enabled_at');
            $table->dropColumn('module');
        });
    }
}

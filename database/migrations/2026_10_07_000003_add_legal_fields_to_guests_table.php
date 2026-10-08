<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->string('second_first_name', 100)->nullable()->after('first_name');
            $table->string('second_last_name', 100)->nullable()->after('last_name');
            $table->date('birth_date')->nullable()->after('identification_number');
            $table->char('gender', 1)->nullable()->after('birth_date');
            $table->char('nationality', 3)->nullable()->after('gender');
            $table->char('residence_country', 3)->nullable()->after('nationality');
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn([
                'second_first_name',
                'second_last_name',
                'birth_date',
                'gender',
                'nationality',
                'residence_country',
            ]);
        });
    }
};

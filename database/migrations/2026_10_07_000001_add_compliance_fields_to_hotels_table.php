<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->char('country_code', 2)->nullable()->after('email');
            $table->string('timezone', 64)->nullable()->after('country_code');

            $table->index('country_code');
        });

        Schema::create('hotel_compliance_profiles', function (Blueprint $table) {
            $table->id();
            $table->char('jurisdiction', 2);
            $table->string('scheme', 30);
            $table->string('establishment_code', 100)->nullable();
            $table->text('credentials')->nullable();
            $table->boolean('enabled')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['hotel_id', 'jurisdiction', 'scheme']);
            $table->unique('establishment_code');
            $table->index(['jurisdiction', 'scheme']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_compliance_profiles');

        Schema::table('hotels', function (Blueprint $table) {
            $table->dropIndex(['country_code']);
            $table->dropColumn(['country_code', 'timezone']);
        });
    }
};

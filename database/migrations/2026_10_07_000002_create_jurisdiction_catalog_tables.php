<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('jurisdiction_subdivisions', function (Blueprint $table) {
            $table->id();
            $table->char('country_code', 2);
            $table->string('code', 16);
            $table->string('name', 100);
            $table->string('type', 30);
            $table->string('iso_reference', 16)->nullable();
            $table->string('source', 64)->nullable();
            $table->string('version', 32)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['country_code', 'code']);
            $table->index('country_code');
        });

        Schema::create('jurisdiction_localities', function (Blueprint $table) {
            $table->id();
            $table->string('code', 16);
            $table->string('name', 100);
            $table->string('type', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('subdivision_id')->constrained('jurisdiction_subdivisions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['subdivision_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurisdiction_localities');
        Schema::dropIfExists('jurisdiction_subdivisions');
    }
};

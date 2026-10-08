<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('stay_guests', function (Blueprint $table) {
            $table->timestamp('checked_in_at')->nullable()->after('role');
            $table->timestamp('checked_out_at')->nullable()->after('checked_in_at');
            $table->char('residence_country', 3)->nullable()->after('checked_out_at');
            $table->string('residence_subdivision', 16)->nullable()->after('residence_country');
            $table->string('residence_locality', 16)->nullable()->after('residence_subdivision');
            $table->char('origin_country', 3)->nullable()->after('residence_locality');
            $table->string('origin_subdivision', 16)->nullable()->after('origin_country');
            $table->string('origin_locality', 16)->nullable()->after('origin_subdivision');
            $table->char('destination_country', 3)->nullable()->after('origin_locality');
            $table->string('destination_subdivision', 16)->nullable()->after('destination_country');
            $table->string('destination_locality', 16)->nullable()->after('destination_subdivision');
            $table->char('travel_purpose', 2)->nullable()->after('destination_locality');
            $table->char('transport_means', 2)->nullable()->after('travel_purpose');
        });

        Schema::table('reservation_guests', function (Blueprint $table) {
            $table->char('residence_country', 3)->nullable()->after('role');
            $table->string('residence_subdivision', 16)->nullable()->after('residence_country');
            $table->string('residence_locality', 16)->nullable()->after('residence_subdivision');
            $table->char('origin_country', 3)->nullable()->after('residence_locality');
            $table->string('origin_subdivision', 16)->nullable()->after('origin_country');
            $table->string('origin_locality', 16)->nullable()->after('origin_subdivision');
            $table->char('destination_country', 3)->nullable()->after('origin_locality');
            $table->string('destination_subdivision', 16)->nullable()->after('destination_country');
            $table->string('destination_locality', 16)->nullable()->after('destination_subdivision');
            $table->char('travel_purpose', 2)->nullable()->after('destination_locality');
            $table->char('transport_means', 2)->nullable()->after('travel_purpose');
        });

        Schema::table('room_occupancies', function (Blueprint $table) {
            $table->foreignId('principal_guest_id')->nullable()->after('room_id')->constrained('guests')->restrictOnDelete();
        });

        Schema::table('reserved_rooms', function (Blueprint $table) {
            $table->foreignId('principal_guest_id')->nullable()->after('room_id')->constrained('guests')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reserved_rooms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('principal_guest_id');
        });

        Schema::table('room_occupancies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('principal_guest_id');
        });

        Schema::table('reservation_guests', function (Blueprint $table) {
            $table->dropColumn([
                'residence_country',
                'residence_subdivision',
                'residence_locality',
                'origin_country',
                'origin_subdivision',
                'origin_locality',
                'destination_country',
                'destination_subdivision',
                'destination_locality',
                'travel_purpose',
                'transport_means',
            ]);
        });

        Schema::table('stay_guests', function (Blueprint $table) {
            $table->dropColumn([
                'checked_in_at',
                'checked_out_at',
                'residence_country',
                'residence_subdivision',
                'residence_locality',
                'origin_country',
                'origin_subdivision',
                'origin_locality',
                'destination_country',
                'destination_subdivision',
                'destination_locality',
                'travel_purpose',
                'transport_means',
            ]);
        });
    }
};

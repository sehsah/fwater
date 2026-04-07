<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->integer('apartments_count')->nullable()->after('units_count');
            $table->integer('people_per_apartment')->nullable()->after('apartments_count');
            $table->integer('elevators_count')->nullable()->after('people_per_apartment');
            $table->integer('ac_units_count')->nullable()->after('elevators_count');
            $table->integer('water_filters_count')->nullable()->after('ac_units_count');
            $table->float('electric_rate')->nullable()->after('water_filters_count');
            $table->float('water_rate')->nullable()->after('electric_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'apartments_count',
                'people_per_apartment',
                'elevators_count',
                'ac_units_count',
                'water_filters_count',
                'electric_rate',
                'water_rate',
            ]);
        });
    }
};

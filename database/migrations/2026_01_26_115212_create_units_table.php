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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['residential', 'commercial', 'industrial', 'agricultural', 'other'])->nullable();
            $table->boolean('is_occupied')->default(false);
            $table->integer('number_of_conditioning')->nullable();
            $table->integer('number_of_people')->nullable();
            $table->integer('number_of_rooms')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};

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
        Schema::create('attendance_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('shift_name')->default('Standard Shift');
            $table->time('s1_start')->nullable();
            $table->time('s1_end')->nullable();
            $table->time('s2_start')->nullable();
            $table->time('s2_end')->nullable();
            $table->integer('late_minutes_allowed')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_shifts');
    }
};

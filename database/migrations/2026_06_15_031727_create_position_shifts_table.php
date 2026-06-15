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
        Schema::create('position_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained('positions')->onDelete('cascade');
            $table->time('morn_in_time');   // គ្រូ = 07:00 | រដ្ឋបាល = 07:30
            $table->time('morn_out_time');  // គ្រូ = 11:00 | រដ្ឋបាល = 12:00
            $table->time('aft_in_time');    // គ្រូ = 14:00 | រដ្ឋបាល = 13:30
            $table->time('aft_out_time');   // គ្រូ = 18:00 | រដ្ឋបាល = 17:00

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_shifts');
    }
};

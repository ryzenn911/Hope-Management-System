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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('date');
            $table->time('check_in_morn')->nullable();
            $table->time('check_out_morn')->nullable();
            $table->time('check_in_aft')->nullable();
            $table->time('check_out_aft')->nullable();
            $table->enum('morn_status', ['Present', 'Late', 'Absent'])->nullable();
            $table->enum('aft_status', ['Present', 'Late',  'Absent'])->nullable();
            $table->string('device_info')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

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
            $table->date('date'); // កាលបរិច្ឆេទថ្ងៃធ្វើការ

            // កំណត់ត្រាម៉ោងពិតប្រាកដដែលគាត់ Check-in/out
            $table->time('in_1')->nullable();
            $table->time('out_1')->nullable();
            $table->time('in_2')->nullable();
            $table->time('out_2')->nullable();

            $table->string('status')->default('Absent'); // Present, Partial, Absent, Late
            $table->timestamps();
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

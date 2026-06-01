<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->string('employee_code')->unique();
            $table->string('name_kh');
            $table->string('name_en');
            $table->enum('gender', ['Male', 'Female']);
            $table->date('dob')->nullable();
            $table->string('phone')->nullable();
            $table->string('education_level')->nullable();
            $table->date('join_date')->nullable();
            $table->enum('status', ['Active', 'Resign'])->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

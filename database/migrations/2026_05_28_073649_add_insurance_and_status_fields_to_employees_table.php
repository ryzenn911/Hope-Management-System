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
        Schema::table('employees', function (Blueprint $table) {
            // បន្ថែម Column ទាំង ៤ (អាចដាក់ ->nullable() ដើម្បីកុំឱ្យទើសទិន្នន័យចាស់ដែលគ្មានតម្លៃទាំងនេះ)
            $table->string('marital_status')->nullable()->after('gender'); // ស្ថានភាពគ្រួសារ (ឧទាហរណ៍៖ លីវ ឬ រៀបការរួច)
            $table->string('nssf_number')->nullable();      // លេខបេឡាជាតិរបបសន្តិសុខសង្គម (ប.ស.ស)
            $table->string('labor_book')->nullable();       // សៀវភៅការងារ
            $table->integer('family_number')->nullable();    // ចំនួនសមាជិកគ្រួសារ (ជាលេខ)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // សម្រាប់លុបចេញវិញ បើសិនជាយើងប្រកាស Rollback
            $table->dropColumn(['marital_status', 'nssf_number', 'labor_book', 'family_number']);
        });
    }
};

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
        Schema::table('attendance_settings', function (Blueprint $table) {
            // បន្ថែម Column ដែលខ្វះ ដោយដាក់ nullable() ដើម្បីកុំឱ្យមានបញ្ហាជាមួយទិន្នន័យចាស់
            $table->date('attendance_date')->nullable()->after('employee_id');
            $table->string('status')->nullable()->after('s2_out');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_settings', function (Blueprint $table) {
            // សម្រាប់លុបវិញពេលយើង Rollback
            $table->dropColumn(['attendance_date', 'status']);
        });
    }

};

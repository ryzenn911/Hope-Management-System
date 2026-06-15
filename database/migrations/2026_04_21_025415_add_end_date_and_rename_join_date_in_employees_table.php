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
            if (Schema::hasColumn('employees', 'join_date')) {
                $table->renameColumn('join_date', 'hire_date');
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'end_date')) {
                $table->date('end_date')->nullable()->after('hire_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'end_date')) {
                $table->dropColumn('end_date');
            }
            if (Schema::hasColumn('employees', 'hire_date')) {
                $table->renameColumn('hire_date', 'join_date');
            }
        });
    }
};

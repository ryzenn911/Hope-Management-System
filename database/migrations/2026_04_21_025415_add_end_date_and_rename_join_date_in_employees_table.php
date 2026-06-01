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
            // ១. ប្តូរឈ្មោះពី join_date ទៅ hire_date ជាមុនសិន
            if (Schema::hasColumn('employees', 'join_date')) {
                $table->renameColumn('join_date', 'hire_date');
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            // ២. បង្កើត column end_date ដោយដាក់នៅបន្ទាប់ពី hire_date
            if (!Schema::hasColumn('employees', 'end_date')) {
                // ប្រសិនបើកូដខាងលើដូរឈ្មោះបានជោគជ័យ យើងអាចប្រើ after('hire_date') បាន
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

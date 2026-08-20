<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'hr_manager', 'manager_departemen', 'karyawan') NOT NULL");
        
        // Change status_kerja enum
        DB::statement("ALTER TABLE employees MODIFY COLUMN status_kerja ENUM('tetap', 'kontrak', 'magang', 'musiman', 'harian', 'tidak tetap') DEFAULT 'tetap'");
        
        // Add manager_id to departments
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'manager_id')) {
                $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'manager_id')) {
                $table->dropForeign(['manager_id']);
                $table->dropColumn('manager_id');
            }
        });

        DB::statement("ALTER TABLE employees MODIFY COLUMN status_kerja ENUM('tetap', 'kontrak', 'magang', 'musiman') DEFAULT 'tetap'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'hr_manager', 'hr_training_manager', 'hr_admin_manager', 'manager_departemen', 'karyawan') NOT NULL");
    }
};

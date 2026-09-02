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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('cv_file')->nullable()->after('cv_text');
            $table->string('cv_url')->nullable()->after('cv_file');
            $table->string('activation_otp')->nullable()->after('cv_url');
            $table->timestamp('activation_otp_expires_at')->nullable()->after('activation_otp');
        });

        DB::statement("ALTER TABLE employees MODIFY COLUMN status_kerja ENUM('tetap', 'kontrak', 'magang', 'musiman', 'harian (DW)', 'tidak tetap', 'harian', 'tenaga_lepas') DEFAULT 'tetap'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE employees MODIFY COLUMN status_kerja ENUM('tetap', 'kontrak', 'magang', 'musiman', 'harian (DW)', 'tidak tetap') DEFAULT 'tetap'");

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['cv_file', 'cv_url', 'activation_otp', 'activation_otp_expires_at']);
        });
    }
};
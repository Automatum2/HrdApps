<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('foto_masuk')->nullable();
            $table->string('lokasi_masuk')->nullable();
            $table->string('foto_keluar')->nullable();
            $table->string('lokasi_keluar')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['foto_masuk', 'lokasi_masuk', 'foto_keluar', 'lokasi_keluar']);
        });
    }
};

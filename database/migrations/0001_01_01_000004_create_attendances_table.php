<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->enum('status_kerja', ['WFO', 'WFH', 'WFF', 'WOD', 'WEH']);
            $table->enum('status_kehadiran', ['hadir', 'izin', 'sakit', 'alpha', 'cuti']);
            $table->decimal('total_jam_kerja', 5, 2)->default(0);
            $table->decimal('jam_lembur', 5, 2)->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

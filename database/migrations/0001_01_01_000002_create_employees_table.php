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
            $table->string('nik')->unique();
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('no_telepon');
            $table->text('alamat');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->foreignId('department_id')->constrained('departments')->onDelete('restrict');
            $table->foreignId('position_id')->constrained('positions')->onDelete('restrict');
            $table->decimal('gaji_pokok', 15, 2);
            $table->string('no_rekening');
            $table->string('nama_bank');
            $table->date('tanggal_masuk');
            $table->enum('status', ['aktif', 'nonaktif', 'cuti'])->default('aktif');
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

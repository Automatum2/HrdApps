<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('period_id')->constrained('payroll_periods')->onDelete('restrict');
            $table->decimal('gaji_pokok', 15, 2);
            $table->decimal('total_tunjangan', 15, 2)->default(0);
            $table->decimal('total_potongan', 15, 2)->default(0);
            $table->decimal('total_lembur', 15, 2)->default(0);
            $table->decimal('gaji_kotor', 15, 2)->default(0);
            $table->decimal('gaji_bersih', 15, 2)->default(0);
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->date('tanggal_bayar')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamps();

            // Memastikan 1 karyawan hanya memiliki 1 catatan gaji per periode
            $table->unique(['employee_id', 'period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};

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
            $table->string('wfd_destination_name')->nullable()->after('status');
            $table->text('wfd_destination_address')->nullable()->after('wfd_destination_name');
            $table->decimal('wfd_latitude', 10, 8)->nullable()->after('wfd_destination_address');
            $table->decimal('wfd_longitude', 11, 8)->nullable()->after('wfd_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'wfd_destination_name',
                'wfd_destination_address',
                'wfd_latitude',
                'wfd_longitude'
            ]);
        });
    }
};
